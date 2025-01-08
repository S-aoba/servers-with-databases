<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class CreateUserSettingTable1 implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE IF NOT EXISTS user_settings (
                entry_id INT PRIMARY KEY AUTO_INCREMENT,
                meta_key VARCHAR(255),
                meta_value VARCHAR(255),
                user_id BIGINT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE user_settings"
        ];
    }
}