<?php
/**
 * Admin Middleware
 * Verifies that the authenticated user has the 'admin' role.
 * Must be used after AuthMiddleware.
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Response;

class AdminMiddleware
{
    public function handle(): void
    {
        $user = AuthMiddleware::user();

        if (!$user) {
            Response::unauthorized('Authentication required');
        }

        if ($user['role'] !== 'admin') {
            Response::forbidden('Access denied. Administrator privileges required.');
        }
    }
}
