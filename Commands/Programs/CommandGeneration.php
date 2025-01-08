<?php

namespace Commands\Programs;

use Commands\AbstractCommand;

class CommandGeneration extends AbstractCommand {
  protected static ?string $alias = 'comm-gen';
  protected static bool $requiredCommandValue = true;

  public static function getArguments(): array {
    return [];
  }

  public function execute(): int
  {
    // ボイラープレートコードのクラス名を取得
    $args = $this->getCommandValue();
    // 先頭を大文字にする(パスカルケース)
    $className = $this->toPascalCase($args);
    // ボイラープレートコードを生成する
    $boilerPlateCode = $this->generateBoilerPlateCode($className);
    // Commands/Programsディレクトリにファイルとして配置する
    $this->createCommandFile($boilerPlateCode, $className);
    // register.phpにクラスを登録する
    $this->registerClass($className);

    return 0;
  }

  private function registerClass(string $className): void {
    $directory = dirname(__DIR__, 2) . "/Commands//";
    $fileName = "registry.php";
    $filePath = $directory . $fileName;
    $array = include $filePath;
    
    array_push($array, "Commands\\Programs\\{$className}");

    $classList = '';
    foreach($array as $class) {
      $classList .= "  " .  $class . '::class' .  ',' . PHP_EOL;
    }
    $classList = rtrim($classList, ',' . PHP_EOL);
    
    $code = 
<<<plate
<?php

return [
{$classList}
];
plate;

    file_put_contents($filePath, $code);
  }

  private function createCommandFile(string $boilerPlateCode, string $className): void {
    $directory = dirname(__DIR__, 2) . "/Commands/Programs/";
    $fileName = $className;
    $filePath = $directory . $fileName . '.php';

    if(!is_dir($directory)) {
      mkdir($directory, 0777, true);
    }

    file_put_contents($filePath, $boilerPlateCode);
  }

  private function generateBoilerPlateCode(string $className): string {
    $code = 
<<<BoilerPlaceCode
<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;

class {$className} extends AbstractCommand
{
    // TODO: エイリアスを設定してください。
    protected static ?string \$alias = '{INSERT COMMAND HERE}';

    // TODO: 引数を設定してください。
    public static function getArguments(): array
    {
        return [];
    }

    // TODO: 実行コードを記述してください。
    public function execute(): int
    {
        return 0;
    }
}

BoilerPlaceCode;
    return $code;
  }

  private function toPascalCase($class): string {
    // キャメルケース内の単語を分割し、アルファベット以外の文字を削除
    $string = preg_replace('/([^A-Z])([A-Z])/', '$1 $2', $class); // 小文字→大文字の境界で分割
    $string = preg_replace('/[^a-zA-Z]/', ' ', $string); // 非アルファベットをスペースに

    // 単語ごとに頭文字を大文字化してスペースを削除
    $string = ucwords(strtolower($string)); // 小文字にしてから頭文字を大文字化
    $string = str_replace(' ', '', $string);

    return $string;
  }

}