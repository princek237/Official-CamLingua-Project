<?php
/**
 * API Routes
 * All routes prefixed with /api
 */

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\TranslationController;
use App\Controllers\HistoryController;
use App\Controllers\ContactController;
use App\Controllers\UserController;
use App\Controllers\AdminController;
use App\Controllers\PaymentController;
use App\Controllers\AdminUserController;
use App\Controllers\AdminLanguageController;
use App\Controllers\AdminTranslationController;
use App\Controllers\AdminSubscriptionController;
use App\Controllers\AdminSettingsController;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

$router = Router::getInstance();

// ── Global middleware ──────────────────────────────────────────────────────────
// CORS headers (handled in .htaccess + Router->dispatch for OPTIONS)

// ── Public routes (no auth required) ───────────────────────────────────────────

// Health check
$router->get('/api/health', function () {
    \App\Core\Response::success(['status' => 'ok', 'timestamp' => time()]);
});

// Auth
$router->post('/api/auth/register', [AuthController::class, 'register']);
$router->post('/api/auth/login',    [AuthController::class, 'login']);

// Contact (public)
$router->post('/api/contact', [ContactController::class, 'submit']);

// Plans listing (public)
$router->get('/api/subscriptions', [UserController::class, 'getPlans']);

// CamPay webhook — no JWT auth, called server-to-server by CamPay
$router->post('/api/payment/webhook', [PaymentController::class, 'webhook']);

// ── Protected routes (auth required) ───────────────────────────────────────────

$authMiddleware = [AuthMiddleware::class];

// Auth: current user
$router->get('/api/auth/me',      [AuthController::class, 'me'],     $authMiddleware);
$router->post('/api/auth/logout', [AuthController::class, 'logout'], $authMiddleware);

// Translation
$router->post('/api/translate', [TranslationController::class, 'translate'], $authMiddleware);

// Translation History
$router->get('/api/history',                 [HistoryController::class, 'index'],          $authMiddleware);
$router->delete('/api/history/{id}',         [HistoryController::class, 'delete'],         $authMiddleware);
$router->post('/api/history/{id}/favorite',  [HistoryController::class, 'toggleFavorite'], $authMiddleware);

// User Profile
$router->get('/api/user/profile',      [UserController::class, 'getProfile'],     $authMiddleware);
$router->put('/api/user/profile',      [UserController::class, 'updateProfile'],  $authMiddleware);
$router->get('/api/user/subscription', [UserController::class, 'getSubscription'], $authMiddleware);

// Free-plan downgrade only — paid plans go through /api/payment/initiate
$router->post('/api/user/subscribe', [UserController::class, 'subscribe'], $authMiddleware);

// ── Payment / CamPay ────────────────────────────────────────────────────────────
$router->post('/api/payment/initiate',    [PaymentController::class, 'initiate'], $authMiddleware);
$router->get('/api/payment/status/{ref}', [PaymentController::class, 'status'],   $authMiddleware);

// ── Admin routes ────────────────────────────────────────────────────────────────

$adminMiddleware = [AuthMiddleware::class, AdminMiddleware::class];

// Admin Dashboard
$router->get('/api/admin/dashboard', [AdminController::class, 'getDashboardStats'], $adminMiddleware);

// Admin Users (CRUD)
$router->get('/api/admin/users',              [AdminUserController::class, 'index'],      $adminMiddleware);
$router->get('/api/admin/users/{id}',         [AdminUserController::class, 'show'],       $adminMiddleware);
$router->post('/api/admin/users',             [AdminUserController::class, 'store'],      $adminMiddleware);
// Role assignment MUST be registered before PUT /{id} so the literal '/role' segment
// is matched before {id} can swallow it as a parameter value.
$router->put('/api/admin/users/{id}/role',    [AdminUserController::class, 'assignRole'], $adminMiddleware);
$router->put('/api/admin/users/{id}',         [AdminUserController::class, 'update'],     $adminMiddleware);
$router->delete('/api/admin/users/{id}',      [AdminUserController::class, 'destroy'],    $adminMiddleware);

// Admin Languages (CRUD)
$router->get('/api/admin/languages',         [AdminLanguageController::class, 'index'],   $adminMiddleware);
$router->get('/api/admin/languages/{id}',    [AdminLanguageController::class, 'show'],    $adminMiddleware);
$router->post('/api/admin/languages',        [AdminLanguageController::class, 'store'],   $adminMiddleware);
$router->put('/api/admin/languages/{id}',    [AdminLanguageController::class, 'update'],  $adminMiddleware);
$router->delete('/api/admin/languages/{id}', [AdminLanguageController::class, 'destroy'], $adminMiddleware);

// Admin Translations (CRUD)
$router->get('/api/admin/translations',         [AdminTranslationController::class, 'index'],   $adminMiddleware);
$router->get('/api/admin/translations/{id}',    [AdminTranslationController::class, 'show'],    $adminMiddleware);
$router->delete('/api/admin/translations/{id}', [AdminTranslationController::class, 'destroy'], $adminMiddleware);

// Admin Subscriptions (CRUD)
$router->get('/api/admin/subscriptions',         [AdminSubscriptionController::class, 'index'],   $adminMiddleware);
$router->get('/api/admin/subscriptions/{id}',    [AdminSubscriptionController::class, 'show'],    $adminMiddleware);
$router->post('/api/admin/subscriptions',        [AdminSubscriptionController::class, 'store'],   $adminMiddleware);
$router->put('/api/admin/subscriptions/{id}',    [AdminSubscriptionController::class, 'update'],  $adminMiddleware);
$router->delete('/api/admin/subscriptions/{id}', [AdminSubscriptionController::class, 'destroy'], $adminMiddleware);

// Admin Settings
$router->get('/api/admin/settings', [AdminSettingsController::class, 'index'],  $adminMiddleware);
$router->put('/api/admin/settings', [AdminSettingsController::class, 'update'], $adminMiddleware);

// Reports (contact messages — read only)
$router->get('/api/admin/reports', [AdminController::class, 'getReportsList'], $adminMiddleware);
