<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class CreateCarPartTable1 implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE IF NOT EXISTS car_parts (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(50) NOT NULL,
                description VARCHAR(255),
                price FLOAT,
                quantity_in_stock INT,
                car_id INT NOT NUll,
                FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
            )"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE car_parts"
        ];
    }
}