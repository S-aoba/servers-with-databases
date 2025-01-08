<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Database\MySQLWrapper;
use DateTime;
use Exception;

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
      $this->log("Cache Book Data exists.");
      $isOver30Date = $this->calculationDiffDate($cahedBookData);

      if($isOver30Date) {
        $this->log("Cache Book Data is over 30 Date. So fetch the new Book Data.");
        $bookData = $this->fetchBookData($isbn);      
        $this->deleteCahceData($isbn);
        $this->saveBookDataToDB($bookData, $isbn);
        $this->printBookData($bookData);

        return 0;
      }
      $this->log("Show you Cache Book Data.");
      $this->printBookData($cahedBookData[0]);
      
    }
    else {
      // Open Library APIからBook Dataを取得する
      $bookData = $this->fetchBookData($isbn);
      $this->saveBookDataToDB($bookData, $isbn);
      $this->printBookData($bookData);
    }

    return 0;
  }

  private function calculationDiffDate(array $cachedBookData) : bool {
    $currentDate = new DateTime('Asia/tokyo');
    $chachedDate = new DateTime($cachedBookData[0]['created_at']);

    $diff = $currentDate->diff($chachedDate);
    $daysDifference = $diff->days;
    if($daysDifference < 30) return false;

    return true;
  }

  private function fetchBookData(string $isbn): array{
    $url = "https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data";

    $result = file_get_contents($url);
    $data = json_decode($result, true);
    if(empty($data)) throw new Exception("{$isbn} do not exists. Please input another isbn.");

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

  private function deleteCahceData(string $isbn): void {
    $this->log("Starting delete old cache book data.");

    $mysql = new MySQLWrapper();
    $id = "book-search-isbn-{$isbn}";
    // 既存のキャシュを削除する
    $command = "DELETE FROM book_searchs WHERE id = '{$id}'";
    $result = $mysql->query($command);
    if($result === false) throw new Exception("Could not delete cache book data.");

    $this->log("Delete old cache book data successfully.");
  }

  private function printBookData(array $bookData): void {
    // TODO: コンソール表示を見やすくしたい
    print_r($bookData);
  }
}