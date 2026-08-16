<?php
/**
 * Auth Middleware
 * Verifies JWT token and attaches user info to $_SESSION or a global.
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\JWT;
use App\Core\Response;
use App\Core\Database;

class AuthMiddleware
{
    public function handle(): void
    {
        $token = JWT::fromHeader();

        if (!$token) {
            Response::unauthorized('Missing authorization token');
        }

        try {
            $payload = JWT::decode($token);
        } catch (\Exception $e) {
            Response::unauthorized('Invalid or expired token: ' . $e->getMessage());
        }

        // Verify user still exists and is active
        $userId = $payload['user_id'] ?? null;
        if (!$userId) {
            Response::unauthorized('Invalid token payload');
        }

        $db   = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM users WHERE id = ? AND status = 'active'", [$userId]);

        if (!$user) {
            Response::unauthorized('User not found or inactive');
        }

        // Store authenticated user in a global for easy access in controllers
        $GLOBALS['auth_user'] = $user;
    }

    /**
     * Get the currently authenticated user
     */
    public static function user(): ?array
    {
        return $GLOBALS['auth_user'] ?? null;
    }
}
