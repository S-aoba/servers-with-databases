<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class UpdateCarPartsTable1 implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "ALTER TABLE car_parts
            ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            "
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "ALTER TABLE car_parts
            DROP COLUMN created_at,
            DROP COLUMN updated_at
            "
        ];
    }
}