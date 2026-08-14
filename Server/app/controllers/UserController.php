<?php
/**
 * UserController — PHP 7.4 compatible
 * GET  /api/user/profile          (protected)
 * PUT  /api/user/profile          (protected)
 * GET  /api/user/subscription     (protected)
 * POST /api/user/subscribe        (protected)
 * GET  /api/subscriptions         (public)
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Middleware\AuthMiddleware;

class UserController extends Controller
{
    /** @var Database */
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── GET /api/user/profile ──────────────────────────────────────────────────
    public function getProfile(): void
    {
        $user = AuthMiddleware::user();
        $full = $this->db->fetchOne(
            'SELECT * FROM user_with_subscription WHERE id = ?',
            [$user['id']]
        );

        if (!$full) {
            Response::notFound('User not found.');
        }

        unset($full['password_hash']);

        // Append stats
        $stats = $this->db->fetchOne(
            'SELECT
                COUNT(*) as total_translations,
                SUM(is_favorite) as saved_words
             FROM translation_history
             WHERE user_id = ?',
            [$user['id']]
        );

        $full['stats'] = [
            'total_translations' => (int)($stats['total_translations'] ?? 0),
            'saved_words'        => (int)($stats['saved_words'] ?? 0),
        ];

        Response::success(['user' => $full]);
    }

    // ── PUT /api/user/profile ──────────────────────────────────────────────────
    public function updateProfile(): void
    {
        $user = AuthMiddleware::user();
        $body = $this->getBody();

        $updateData = [];

        if (isset($body['full_name'])) {
            $v = $this->sanitize($body['full_name']);
            if (strlen($v) < 1 || strlen($v) > 100) {
                Response::validationError(['full_name' => 'Full name must be between 1 and 100 characters.']);
            }
            $updateData['full_name'] = $v;
        }

        if (isset($body['bio'])) {
            $updateData['bio'] = $this->sanitize($body['bio']);
        }

        if (isset($body['username'])) {
            $v = $this->sanitize($body['username']);
            if (strlen($v) < 3 || strlen($v) > 50) {
                Response::validationError(['username' => 'Username must be between 3 and 50 characters.']);
            }
            // Check uniqueness
            $existing = $this->db->fetchOne(
                'SELECT id FROM users WHERE username = ? AND id != ?',
                [$v, $user['id']]
            );
            if ($existing) {
                Response::error('Username already taken.', 409);
            }
            $updateData['username'] = $v;
        }

        if (isset($body['avatar_url'])) {
            $v = filter_var($body['avatar_url'], FILTER_VALIDATE_URL);
            if ($v === false) {
                Response::validationError(['avatar_url' => 'Invalid avatar URL.']);
            }
            $updateData['avatar_url'] = $v;
        }

        if (empty($updateData)) {
            Response::badRequest('No valid fields provided for update.');
        }

        $this->db->update('users', $updateData, ['id' => $user['id']]);

        $updated = $this->db->fetchOne(
            'SELECT * FROM user_with_subscription WHERE id = ?',
            [$user['id']]
        );
        unset($updated['password_hash']);

        Response::success(['user' => $updated], 'Profile updated successfully.');
    }

    // ── GET /api/user/subscription ─────────────────────────────────────────────
    public function getSubscription(): void
    {
        $user = AuthMiddleware::user();

        $sub = $this->db->fetchOne(
            'SELECT us.*, s.name, s.slug, s.description, s.price_monthly,
                    s.price_yearly, s.features, s.limits
             FROM user_subscriptions us
             JOIN subscriptions s ON us.subscription_id = s.id
             WHERE us.user_id = ? AND us.status = ?
             ORDER BY us.created_at DESC LIMIT 1',
            [$user['id'], 'active']
        );

        if (!$sub) {
            Response::notFound('No active subscription found.');
        }

        // Decode JSON fields
        $sub['features'] = json_decode($sub['features'] ?? '[]', true);
        $sub['limits']   = json_decode($sub['limits']   ?? '{}', true);

        // Usage stats for this billing period
        $usageRow = $this->db->fetchOne(
            'SELECT COUNT(*) as count FROM translation_history
             WHERE user_id = ? AND DATE(created_at) = CURDATE()',
            [$user['id']]
        );
        $sub['usage'] = [
            'translations_today' => (int)($usageRow['count'] ?? 0),
        ];

        Response::success(['subscription' => $sub]);
    }

    // ── POST /api/user/subscribe ───────────────────────────────────────────────
    /**
     * Handles ONLY the Free plan (no payment required).
     * Pro and Premium plans must go through POST /api/payment/initiate instead.
     */
    public function subscribe(): void
    {
        $user = AuthMiddleware::user();
        $body = $this->getBody();

        $errors = $this->validateRequired($body, ['plan']);
        if ($errors) {
            $this->validationError($errors);
        }

        $planSlug = strtolower($this->sanitize($body['plan']));

        // Paid plans require a CamPay payment — redirect the client
        if ($planSlug !== 'free') {
            Response::error(
                'Paid plans require payment. Use POST /api/payment/initiate to subscribe.',
                402
            );
        }

        $plan = $this->db->fetchOne(
            'SELECT id FROM subscriptions WHERE slug = ? AND is_active = 1',
            [$planSlug]
        );
        if (!$plan) {
            Response::notFound('Subscription plan not found.');
        }

        // Cancel any existing active subscription
        $this->db->query(
            "UPDATE user_subscriptions SET status = 'cancelled', cancelled_at = CURRENT_TIMESTAMP
             WHERE user_id = ? AND status = 'active'",
            [$user['id']]
        );

        // Free plan: no expiry
        $this->db->insert('user_subscriptions', [
            'user_id'         => $user['id'],
            'subscription_id' => $plan['id'],
            'status'          => 'active',
            'billing_cycle'   => 'monthly',
            'expires_at'      => null,
        ]);

        Response::success([], 'Successfully switched to the Free plan.');
    }

    // ── GET /api/subscriptions (public — plan listing) ─────────────────────────
    public function getPlans(): void
    {
        $plans = $this->db->fetchAll(
            'SELECT id, name, slug, description, price_monthly, price_yearly, features, limits
             FROM subscriptions
             WHERE is_active = 1
             ORDER BY price_monthly ASC'
        );

        foreach ($plans as &$plan) {
            $plan['id']           = (int)$plan['id'];
            $plan['price_monthly'] = (float)$plan['price_monthly'];
            $plan['price_yearly']  = (float)$plan['price_yearly'];
            $plan['features']      = json_decode($plan['features'] ?? '[]', true);
            $plan['limits']        = json_decode($plan['limits']   ?? '{}', true);
        }

        Response::success(['plans' => $plans]);
    }
}
