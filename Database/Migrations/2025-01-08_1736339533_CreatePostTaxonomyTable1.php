<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class CreatePostTaxonomyTable1 implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE IF NOT EXISTS post_taxonomies (
                id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                post_id INT NOT NULL,
                taxonomy_id INT NOT NULL,
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
                FOREIGN KEY (taxonomy_id) REFERENCES taxonomies(id) ON DELETE CASCADE
            )"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE post_taxonomies"
        ];
    }
}