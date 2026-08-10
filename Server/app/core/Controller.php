<?php
/**
 * Base Controller — PHP 7.4 compatible
 */

declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected function getBody(): array
    {
        $raw = file_get_contents('php://input');
        if (empty($raw)) return [];
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    protected function input(string $key, $default = null)
    {
        return $this->getBody()[$key] ?? $default;
    }

    protected function query(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    protected function sanitize($value): string
    {
        return trim(strip_tags((string)$value));
    }

    protected function validateRequired(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
                $errors[$field] = "The {$field} field is required.";
            }
        }
        return $errors;
    }

    protected function success($data = [], string $message = '', int $code = 200): void
    {
        Response::success($data, $message, $code);
    }

    protected function error(string $message, int $code = 400): void
    {
        Response::error($message, $code);
    }

    protected function validationError(array $errors): void
    {
        Response::validationError($errors);
    }
}
