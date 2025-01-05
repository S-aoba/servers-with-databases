<?php

spl_autoload_extensions(".php");
spl_autoload_register();

use Helpers\Settings;
/*
    接続の失敗時にエラーを報告し、例外をスローします。データベース接続を初期化する前にこの設定を行ってください。
    テストするには、.env設定で誤った情報を入力します。
*/
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/*
 * https://www.php.net/manual/en/class.mysqli.php で利用可能なすべてのメソッドを確認できます。
 */
$mysqli = new mysqli('localhost', Settings::env('DATABASE_USER'), Settings::env('DATABASE_USER_PASSWORD'), Settings::env('DATABASE_NAME'));

// https://www.php.net/manual/en/mysqli.get-charset.php
$charset = $mysqli->get_charset();

if($charset === null) throw new Exception('Charset could be read');

// データベースの文字セット、照合順序、統計情報について取得します。
printf(
    "%s's charset: %s.%s",
    Settings::env('DATABASE_NAME'),
    $charset->charset,
    PHP_EOL
);

printf(
    "collation: %s.%s",
    $charset->collation,
    PHP_EOL
);

// 接続を閉じるには、closeメソッドを使用します。
$mysqli->close();