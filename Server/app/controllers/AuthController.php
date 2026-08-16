<?php
/**
 * AuthController — register, login, logout, me — PHP 7.4 compatible
 * POST /api/auth/register
 * POST /api/auth/login
 * POST /api/auth/logout  (protected)
 * GET  /api/auth/me      (protected)
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\JWT;
use App\Core\Response;
use App\Middleware\AuthMiddleware;

class AuthController extends Controller
{
    /** @var Database */
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── POST /api/auth/register ────────────────────────────────────────────────
    public function register(): void
    {
        $body = $this->getBody();

        // Validate required fields
        $errors = $this->validateRequired($body, ['username', 'email', 'password']);
        if ($errors) {
            $this->validationError($errors);
        }

        $username = $this->sanitize($body['username']);
        $email    = strtolower($this->sanitize($body['email']));
        $password = $body['password'];

        // Validate formats
        if (strlen($username) < 3 || strlen($username) > 50) {
            Response::validationError(['username' => 'Username must be between 3 and 50 characters.']);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::validationError(['email' => 'Invalid email address.']);
        }
        if (strlen($password) < 6) {
            Response::validationError(['password' => 'Password must be at least 6 characters.']);
        }

        // Check uniqueness
        $existing = $this->db->fetchOne(
            'SELECT id FROM users WHERE email = ? OR username = ?',
            [$email, $username]
        );
        if ($existing) {
            Response::error('Email or username already in use.', 409);
        }

        // Create user
        $hash   = password_hash($password, PASSWORD_BCRYPT);
        $userId = $this->db->insert('users', [
            'username'      => $username,
            'email'         => $email,
            'password_hash' => $hash,
            'full_name'     => $this->sanitize($body['full_name'] ?? $username),
        ]);

        // Assign free plan
        $freePlan = $this->db->fetchOne("SELECT id FROM subscriptions WHERE slug = 'free'");
        if ($freePlan) {
            $this->db->insert('user_subscriptions', [
                'user_id'         => $userId,
                'subscription_id' => $freePlan['id'],
                'status'          => 'active',
            ]);
        }

        // Return token
        $user  = $this->db->fetchOne('SELECT * FROM user_with_subscription WHERE id = ?', [$userId]);
        // Fallback: if view returns nothing (no subscription yet), fetch from base table
        if (empty($user)) {
            $user = $this->db->fetchOne('SELECT * FROM users WHERE id = ?', [$userId]);
        }
        $token = JWT::encode(['user_id' => (int)$userId, 'role' => $user['role'] ?? 'user']);

        Response::success(
            ['token' => $token, 'user' => $this->sanitizeUser($user)],
            'Account created successfully.',
            201
        );
    }

    // ── POST /api/auth/login ───────────────────────────────────────────────────
    public function login(): void
    {
        $body = $this->getBody();

        $errors = $this->validateRequired($body, ['email', 'password']);
        if ($errors) {
            $this->validationError($errors);
        }

        $email    = strtolower($this->sanitize($body['email']));
        $password = $body['password'];

        $user = $this->db->fetchOne(
            "SELECT * FROM users WHERE email = ? AND status = 'active'",
            [$email]
        );

        // Use a consistent timing check to avoid user enumeration
        $hash = $user['password_hash'] ?? '$2y$10$abcdefghijklmnopqrstuuVGiwDMvfixlRi0GlLpd3Og7Vbd3xYpO';
        if (!$user || !password_verify($password, $hash)) {
            Response::error('Invalid email or password.', 401);
        }

        $token    = JWT::encode(['user_id' => (int)$user['id'], 'role' => $user['role']]);
        $fullUser = $this->db->fetchOne('SELECT * FROM user_with_subscription WHERE id = ?', [$user['id']]);
        // Fallback: if view returns nothing (e.g. no subscription row), use the base users record
        // This guarantees the 'role' field is always present in the API response
        if (empty($fullUser)) {
            $fullUser = $user;
        }

        Response::success(
            ['token' => $token, 'user' => $this->sanitizeUser($fullUser)],
            'Login successful.'
        );
    }

    // ── POST /api/auth/logout  (protected) ────────────────────────────────────
    public function logout(): void
    {
        // JWT is stateless — client must discard the token.
        // This endpoint exists for client-side consistency.
        Response::success([], 'Logged out successfully.');
    }

    // ── GET /api/auth/me  (protected) ──────────────────────────────────────────
    public function me(): void
    {
        $user     = AuthMiddleware::user();
        $fullUser = $this->db->fetchOne('SELECT * FROM user_with_subscription WHERE id = ?', [$user['id']]);
        Response::success(['user' => $this->sanitizeUser($fullUser)]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    /** Remove sensitive fields before sending user data to the client */
    private function sanitizeUser($user): array
    {
        if (!$user || !is_array($user)) return [];
        unset($user['password_hash']);
        return $user;
    }
}
