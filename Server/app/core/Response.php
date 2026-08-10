<?php
/**
 * Response — JSON response helper — PHP 7.4 compatible
 */

declare(strict_types=1);

namespace App\Core;

class Response
{
    public static function json($data, int $statusCode = 200, array $headers = []): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        foreach ($headers as $key => $value) {
            header("{$key}: {$value}");
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function success($data = [], string $message = '', int $statusCode = 200): void
    {
        $payload = ['success' => true];
        if ($message !== '') $payload['message'] = $message;
        if (!empty($data))   $payload['data']    = $data;
        self::json($payload, $statusCode);
    }

    public static function error(string $message, int $statusCode = 400, ?string $error = null): void
    {
        $payload = ['success' => false, 'message' => $message];
        if ($error !== null) $payload['error'] = $error;
        self::json($payload, $statusCode);
    }

    public static function badRequest(string $message = 'Bad request'): void
    {
        self::error($message, 400);
    }

    public static function unauthorized(string $message = 'Unauthorized'): void
    {
        self::error($message, 401);
    }

    public static function forbidden(string $message = 'Forbidden'): void
    {
        self::error($message, 403);
    }

    public static function notFound(string $message = 'Resource not found'): void
    {
        self::error($message, 404);
    }

    public static function serverError(string $message = 'Internal server error'): void
    {
        self::error($message, 500);
    }

    public static function validationError(array $errors, string $message = 'Validation failed'): void
    {
        self::json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }
}
