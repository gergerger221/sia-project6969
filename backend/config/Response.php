<?php
// backend/config/Response.php
namespace App\Config;

class Response {
    public static function json(bool $success, string $message, $data = null, int $statusCode = 200): void {
        // Allow CORS for local Vue development and XAMPP
        if (!headers_sent()) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($statusCode);
        }

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error(string $message, int $statusCode = 400, $data = null): void {
        self::json(false, $message, $data, $statusCode);
    }

    public static function success(string $message = 'Success', $data = null, int $statusCode = 200): void {
        self::json(true, $message, $data, $statusCode);
    }
}
