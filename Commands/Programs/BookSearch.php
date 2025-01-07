<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Database\MySQLWrapper;
use Exception;
use JsonException;

class BookSearch extends AbstractCommand {

  // 使用するコマンド名を設定
  protected static ?string $alias = 'book-search';

  public static function getArguments():array 
  {
    return [
      (new Argument('isbn'))->description('Get the book information to using isbn from Open Library API.')->required(true)->allowAsShort(true)
    ];
  }

  public function execute(): int 
  {
    $isbn = $this->getArgumentValue('isbn');

    if($isbn === false) throw new Exception('Please input of the isbn word.');

    // キャッシュの確認
    $cahedBookData = $this->getCacheData($isbn);
    
    if(count($cahedBookData) > 0) {
      // キャッシュデータの保存期間が30日を超えていたら、新しいデータを取得して保存する
      // $this->printBookData($cahedBookData);
    }
    else {
      // Open Library APIからBook Dataを取得する
      $bookData = $this->fetchBookData($isbn);
      $this->saveBookDataToDB($bookData, $isbn);
      // $this->printBookData($bookData);
    }

    return 0;
  }

  private function fetchBookData(string $isbn): array{
    $url = "https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data";

    $result = file_get_contents($url);
    $data = json_decode($result, true);
    if(empty($data)) throw new Exception("{$isbn} is not exists. Please input another isbn.");

    $book = $data["ISBN:{$isbn}"];
    // Titile, Authors, Publishers ,Publish Date のみを抽出してDBへ保存する
    $formattedBookData = [
      'Title' => $book['title'],
      'Authors' => $book['authors'],
      'Publishers' => $book['publishers'],
      'PublishDate' => $book['publish_date']
    ];
    
    return $formattedBookData;
  }


  private function saveBookDataToDB(array $data, string $isbn): void{
    $mysql = new MySQLWrapper();
    $id = "book-search-isbn-{$isbn}";
    $jsonData = json_encode($data);

    $command = "INSERT INTO book_searchs (id, data) VALUES (?, ?)";

    $stmt = $mysql->prepare($command);
    $stmt->bind_param("ss", $id, $jsonData);
    $stmt->execute();

    $stmt->close();
  }

  private function getCacheData(string $isbn): array {
    $mysql = new MySQLWrapper();
    $id = "book-search-isbn-{$isbn}";
    $command = "SELECT * FROM book_searchs WHERE id = '{$id}'";

    $result = $mysql->query($command)->fetch_all(MYSQLI_ASSOC);
    
    if(empty($result)) return [];
    
    return $result;
  }

  private function updateCahceData(): bool {
    return true;
  }

  private function printBookData(array $bookData): void {

  }
}