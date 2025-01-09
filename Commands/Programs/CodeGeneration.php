<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;

class CodeGeneration extends AbstractCommand
{
    // 使用するコマンド名を設定します
    protected static ?string $alias = 'code-gen';
    protected static bool $requiredCommandValue = true;

    // 引数を割り当てます
    public static function getArguments(): array
    {
        return [
            (new Argument('name'))->description('Name of the file that is to be generated.')->required(false),
        ];
    }

    public function execute(): int
    {
        $codeGenType = $this->getCommandValue();
        $this->log('Generating code for.......' . $codeGenType);

        if ($codeGenType === 'migration') {
            $migrationName = $this->getArgumentValue('name');
            $this->generateMigrationFile($migrationName);
        }
        else if ($codeGenType === 'seeder') {
            $seederName = $this->getArgumentValue('name');
            $this->generateSeederFile($seederName);
        }

        return 0;
    }

    private function generateSeederFile(string $seederName) : void {
        $seederName = $this->pascalCase($seederName);
        $seederName = $this->addSeederWord($seederName);
        $filename = sprintf(
            '%s.php',
            $seederName
        );
        
        $seederContent = $this->getSeederContent($seederName);
        
        $path = sprintf("%s/../../Database/Seeds/%s", __DIR__,$filename);

        file_put_contents($path, $seederContent);
        $this->log("Seeder file {$filename} has been generated!");
    }

    private function getSeederContent(string $seederName): string {
        return <<<SEEDER
<?php
namespace Database\Seeds;

use Database\AbstractSeeder;

class {$seederName} extends AbstractSeeder {

    // TODO: tableName文字列を割り当ててください。
    protected ?string \$tableName = null;

    // TODO: tableColumns配列を割り当ててください。
    protected array \$tableColumns = [];

    public function createRowData(): array
    {
        // TODO: createRowData()メソッドを実装してください。
        return [];
    }
}       
SEEDER;
    }

    private function addSeederWord(string $seederName): string {
        // 入力された$seederNameの後ろにSeederがついているかどうかをチェックし、ついていれば何もせず、ついていなければSeederを付けて返す
        if(str_contains($seederName, 'Seeder')) {
            return $seederName;
        }
        else if(str_contains($seederName, 'seeder')) {
            return str_replace('seeder', 'Seeder', $seederName);
        }

        return $seederName . "Seeder";
    }

    private function generateMigrationFile(string $migrationName): void
    {
        $filename = sprintf(
            '%s_%s_%s.php',
            date('Y-m-d'),
            time(),
            $migrationName
        );

        $migrationContent = $this->getMigrationContent($migrationName);

        // 移行ファイルを保存するパスを指定します
        $path = sprintf("%s/../../Database/Migrations/%s", __DIR__,$filename);

        file_put_contents($path, $migrationContent);
        $this->log("Migration file {$filename} has been generated!");
    }

    private function getMigrationContent(string $migrationName): string
    {
        $className = $this->pascalCase($migrationName);

        return <<<MIGRATION
<?php
namespace Database\Migrations;

use Database\SchemaMigration;

class {$className} implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [];
    }
}
MIGRATION;
    }

    private function pascalCase(string $string): string{
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}