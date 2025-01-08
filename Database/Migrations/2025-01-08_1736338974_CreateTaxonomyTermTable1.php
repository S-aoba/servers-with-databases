<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class CreateTaxonomyTermTable1 implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE IF NOT EXISTS taxonomy_terms (
                id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                description VARCHAR(255),
                parent INT,
                type_id INT NOT NULL,
                FOREIGN KEY (type_id) REFERENCES taxonomies(id) ON DELETE CASCADE
            )"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE taxonomy_terms"
        ];
    }
}