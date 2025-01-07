<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Database\MySQLWrapper;
use Helpers\Settings;

use Exception;

class DbWipe extends AbstractCommand 
{
  // 使用するコマンド名を設定
  protected static ?string $alias = 'db-wipe';

  public static function getArguments(): array
  {
    return [
      (new Argument('backup'))->description('Create backup file, before cleanup to database.')->required(false)->allowAsShort(true),
      (new Argument('reconstruction'))->description('Reconstruction to database to using backup file.')->required(false)->allowAsShort(true)
    ];
  }

  public function execute(): int
  {
    $backup = $this->getArgumentValue('backup');
    $reconstruction = $this->getArgumentValue('reconstruction');
    
    $this->log('Starging DbWipe.....');
    
    if($backup) {
      // バックアップを作成してから、データベースをクリアにする
      $this->generateBackupFile();
      $this->cleanupToDatabase();
    }

    else if ($reconstruction){
      $this->reconstructionDatabase();
    }
    
    else {
      $this->cleanupToDatabase();
    }

    return 0;
  }

  private function reconstructionDatabase(): void {
    $filePath = dirname(__DIR__, 2) . "/Database/Backup/backup.sql";
    $isExistBackupFile = file_exists($filePath);

    if($isExistBackupFile === false) throw new Exception("Do not exists backup file.");
    else {
      $username = $username??Settings::env('DATABASE_USER');
      $database = $database??Settings::env('DATABASE_NAME');

      $command = "mysql -u {$username} -p {$database} < {$filePath}";
      exec($command, $output, $returnVar);
      
      if($returnVar === 0) {
        $this->log("Successfully restored.");
      }
      else {
        throw new Exception("Could not restored.");
      }
    }
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

    $username = $username??Settings::env('DATABASE_USER');
    $password = $password??Settings::env('DATABASE_USER_PASSWORD');
    $database = $database??Settings::env('DATABASE_NAME');

    $backupDir = dirname(__DIR__, 2) . "/Database/Backup/";
    $backupFile = $backupDir . "backup" . ".sql";

    if (!is_dir($backupDir)) {
      mkdir($backupDir, 0755, true);
    }
  
    $command = "mysqldump -u {$username} -p{$password} {$database} > {$backupFile}";
    exec($command, $output, $returnVar);
  
    if ($returnVar === 0) {
      $this->log('Create backup file Successfully.');
    } else {
      throw new Exception("Could not create backup file.");
    }
  }
}