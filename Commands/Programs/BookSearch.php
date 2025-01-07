<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
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
    $isCahedBookData = $this->checkCacheData($isbn);
    
    if(count($isCahedBookData) > 0) {
      // $this->printBookData($isCahedBookData);
    }

    else {
      // Open Library APIからBook Dataを取得する
      $bookData = $this->fetchBookData($isbn);
      // $this->printBookData($bookData);
    }

    return 0;
  }

  private function fetchBookData(string $isbn): array{
    $url = "https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data";

    $result = file_get_contents($url);
    $data = json_decode($result, true);

    if(isset($data["ISBN:{$isbn}"])){
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
    
    return [];
  }


  private function saveBookData(): bool{
    return true;
  }

  private function checkCacheData(string $isbn): array {
    return [];
  }

  private function updateCahceData(): bool {
    return true;
  }

  private function printBookData(array $bookData): void {

  }
}