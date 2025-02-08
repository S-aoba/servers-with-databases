<?php

namespace Middleware;

use Response\HTTPRenderer;

class HttpLoggingMiddleware implements Middleware {

  public function handle(callable $next): HTTPRenderer
  {
    date_default_timezone_set('Asia/Tokyo');
    // logsディレクトリのパス（一段上のディレクトリ）
    $logDir = dirname(__DIR__) . '/logs';

    // logsディレクトリが存在しない場合、作成する
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    // ログファイルのパス
    $logFile = $logDir . '/http_logging_' . date('Y-m-d') . '.log';

    // リクエスト情報の取得
    $url = $_SERVER['REQUEST_URI'] ?? 'unknown';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
    $timestamp = date('Y-m-d H:i:s');
    $queryParams = $_SERVER['QUERY_STRING'] ?? '(none)';
    $headers = getallheaders();

    // ログ（リクエスト前）
    $logMessage = sprintf(
        "\n========== [HTTP REQUEST] ==========\n".
        "[%s] %s %s\n".
        "Query Params: %s\n".
        "Headers: %s\n".
        "=====================================\n",
        $timestamp,
        $method,
        $url,
        empty($queryParams) ? '(none)' : $queryParams,
        json_encode($headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    // ログをファイルに記録
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    error_log("Running Middleware " . self::class . " Preprocess");

    // レスポンス処理前の時間を記録
    $startTime = microtime(true);
    $response = $next(); // レスポンスを取得
    $responseTime = round((microtime(true) - $startTime) * 1000, 2) . 'ms';

    // レスポンス情報の取得
    $statusCode = http_response_code();
    $responseHeaders = headers_list();

    // ログ（レスポンス後）
    $logMessage = sprintf(
        "\n========== [HTTP RESPONSE] ==========\n".
        "[%s] Status: %s\n".
        "Response Time: %s\n".
        "Headers: %s\n".
        "======================================\n",
        date('Y-m-d H:i:s'),
        $statusCode,
        $responseTime,
        json_encode($responseHeaders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    // ログをファイルに記録
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    error_log("Running Middleware " . self::class . " Postprocess");

    return $response;
  }
  
  
}