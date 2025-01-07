<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Database\MySQLWrapper;
use Exception;

class DbWipe extends AbstractCommand 
{
  // 使用するコマンド名を設定
  protected static ?string $alias = 'db-wipe';

  public static function getArguments(): array
  {
    // バックアップを作成するオプションを追加する
    return [
      (new Argument('backup'))->description('Create backup file, before cleanup to database.')->required(false)->allowAsShort(true)
    ];
  }

  public function execute(): int
  {
    $backup = $this->getArgumentValue('backup');
    $this->log('Starging DbWipe.....');
    if($backup === false){
      $this->cleanupToDatabase();
    }
    else {
      // バックアップを作成してから、データベースをクリアにする
      $this->generateBackupFile();
      $this->cleanupToDatabase();
    }

    return 0;
  }

  private function cleanupToDatabase(): void {
    $this->log('Running DbWipe...');

    $mysql = new MySQLWrapper();
    $sql = 'SHOW TABLES';
    $result = $mysql->query($sql);

    if($result->num_rows > 0) {
      $mysql->query('SET FOREIGN_KEY_CHECKS = 0');

      $rows = $result->fetch_all();
      foreach($rows as $row) {
          $table = $row[0];
          $truncateSql = "TRUNCATE TABLE `$table`";
          $result = $mysql->query($truncateSql);
          if($result === false) throw new Exception("Could not execute query.");
      }
    
      $mysql->query('SET FOREIGN_KEY_CHECKS = 1');
      $this->log("DbWipe ended...\n");
    }
    else {
      $this->log("Do not exists row.");
    }
  }

  private function generateBackupFile(): void {
    $this->log('Creating backup file....');
    $this->log('Successfully backup file.');
  }
}