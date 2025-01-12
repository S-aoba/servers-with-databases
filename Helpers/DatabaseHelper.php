<?php

namespace Helpers;

use Database\MySQLWrapper;
use Exception;

class DatabaseHelper
{
    public static function getRandomComputerPart(): array{
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $part = $result->fetch_assoc();

        if (!$part) throw new Exception('Could not find a single part in database');

        return $part;
    }

    public static function getComputerPartById(int $id): array{
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $part = $result->fetch_assoc();

        if (!$part) throw new Exception('Could not find a single part in database');

        return $part;
    }

    public static function getComputerPartByType(string $type, int $page, int $perpage): array {
        $db = new MySQLWrapper();
    
        // OFFSET を計算
        $offset = ($page - 1) * $perpage;
    
        // SQL 準備
        $stmt = $db->prepare("SELECT * FROM computer_parts WHERE type = ? ORDER BY id ASC LIMIT ? OFFSET ?");
        $stmt->bind_param('sii', $type, $perpage, $offset);
    
        // 実行
        $stmt->execute();
    
        // 結果を取得
        $result = $stmt->get_result();
        $parts = $result->fetch_all(MYSQLI_ASSOC); // 複数行を取得
    
        // データがない場合は空配列を返す
        if (!$parts) return [];
    
        return $parts;
    }
    
    public static function getRandomComputer(): array {
        $db = new MySQLWrapper();
    
        $types = ['cpu', 'gpu', 'motherboard', 'power', 'memory', 'ssd', 'hd'];
        $randomComputer = [];
        // DBからType別に一つずつ取得する
        foreach($types as $type) {
            $stmt = $db->prepare("SELECT * FROM computer_parts WHERE type = ? ORDER BY RAND() LIMIT 1");
            $stmt->bind_param('s', $type);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if($row === false) throw new Exception("Could not find {$type}");

            $randomComputer[$type] = $row;
        }

        return $randomComputer;
    }

    public static function getNewestComputerParts(int $page, int $perpage): array {
        $db = new MySQLWrapper();
    
        // OFFSET を計算
        $offset = ($page - 1) * $perpage;
    
        // SQL 準備
        $stmt = $db->prepare("SELECT * FROM computer_parts ORDER BY release_date DESC LIMIT ? OFFSET ?");
        $stmt->bind_param('ii', $perpage, $offset);
    
        // 実行
        $stmt->execute();
    
        // 結果を取得
        $result = $stmt->get_result();
        $parts = $result->fetch_all(MYSQLI_ASSOC); // 複数行を取得
    
        // データがない場合は空配列を返す
        if (!$parts) return [];
    
        return $parts;
    }
}