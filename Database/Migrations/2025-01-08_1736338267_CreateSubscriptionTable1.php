<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class CreateSubscriptionTable1 implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE IF NOT EXISTS subscriptions (
                id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                subscription VARCHAR(50) NOT NULL,
                status VARCHAR(50) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                ends_at DATETIME DEFAULT NULL,
                user_id BIGINT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE subscriptions"
        ];
    }
}