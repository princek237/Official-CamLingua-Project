<?php
/**
 * PaymentController — PHP 7.4 compatible
 *
 * POST /api/payment/initiate       Initiate a CamPay Mobile Money charge
 * GET  /api/payment/status/{ref}   Poll the status of a pending payment
 * POST /api/payment/webhook        CamPay server-to-server callback (no auth guard)
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Middleware\AuthMiddleware;
use App\Services\CamPayService;
use App\Services\MailService;
use App\Services\EmailTemplates;

class PaymentController extends Controller
{
    /** @var Database */
    private $db;

    /** @var CamPayService */
    private $campay;

    public function __construct()
    {
        $this->db = Database::getInstance();

        $config       = require ROOT_PATH . '/app/config/config.php';
        $this->campay = new CamPayService($config['campay']);
    }

    // ── POST /api/payment/initiate ────────────────────────────────────────────
    /**
     * Validates the request, records a PENDING payment row, then calls
     * CamPay /collect/ to push a USSD prompt to the payer's phone.
     *
     * Request body (JSON):
     *   plan          string  "pro" | "premium"
     *   billing_cycle string  "monthly" | "yearly"
     *   phone         string  "237677123456"  (9-digit Cameroonian number, with country code)
     *
     * Response (success):
     *   { external_reference, campay_reference, ussd_code, operator, amount }
     */
    public function initiate(): void
    {
        $user = AuthMiddleware::user();
        $body = $this->getBody();

        // ── Validate inputs ───────────────────────────────────────────────────
        $errors = $this->validateRequired($body, ['plan', 'phone']);
        if ($errors) {
            $this->validationError($errors);
        }

        $planSlug     = strtolower($this->sanitize($body['plan']));
        $billingCycle = $this->sanitize($body['billing_cycle'] ?? 'monthly');
        $phone        = preg_replace('/\s+/', '', $this->sanitize($body['phone']));

        if (!in_array($billingCycle, ['monthly', 'yearly'], true)) {
            $billingCycle = 'monthly';
        }

        // Normalise phone: accept "677123456" (9 digits) → prefix with "237"
        if (preg_match('/^[67]\d{8}$/', $phone)) {
            $phone = '237' . $phone;
        }
        if (!preg_match('/^237[67]\d{8}$/', $phone)) {
            Response::validationError(['phone' => 'Enter a valid MTN or Orange Cameroon number (e.g. 677123456).']);
        }

        // ── Resolve plan & price ──────────────────────────────────────────────
        $plan = $this->db->fetchOne(
            'SELECT id, name, price_monthly, price_yearly FROM subscriptions WHERE slug = ? AND is_active = 1',
            [$planSlug]
        );
        if (!$plan) {
            Response::notFound('Subscription plan not found.');
        }

        // Free plan needs no payment
        if ($planSlug === 'free') {
            Response::badRequest('No payment required for the Free plan.');
        }

        $amount = (int) round(
            $billingCycle === 'yearly' ? (float)$plan['price_yearly'] : (float)$plan['price_monthly']
        );

        if ($amount <= 0) {
            Response::badRequest('Invalid plan price.');
        }

        // ── Generate a unique external reference ──────────────────────────────
        $externalRef = $this->generateUuid();

        // ── Persist PENDING payment row ───────────────────────────────────────
        $this->db->insert('payments', [
            'user_id'            => $user['id'],
            'subscription_id'    => $plan['id'],
            'external_reference' => $externalRef,
            'phone'              => $phone,
            'amount'             => $amount,
            'currency'           => 'XAF',
            'billing_cycle'      => $billingCycle,
            'status'             => 'PENDING',
        ]);

        // ── Call CamPay ───────────────────────────────────────────────────────
        try {
            $description = 'CamLingua ' . ucfirst($planSlug) . ' (' . $billingCycle . ')';
            $result      = $this->campay->collect($phone, $amount, $description, $externalRef);
        } catch (\RuntimeException $e) {
            // Mark our row as failed so the user can retry
            $this->db->query(
                "UPDATE payments SET status = 'FAILED', failure_reason = ? WHERE external_reference = ?",
                [$e->getMessage(), $externalRef]
            );
            Response::error('Payment initiation failed: ' . $e->getMessage(), 502);
        }

        $campayRef = $result['reference'] ?? null;

        // Store the CamPay reference for status polling
        if ($campayRef) {
            $this->db->query(
                'UPDATE payments SET campay_reference = ? WHERE external_reference = ?',
                [$campayRef, $externalRef]
            );
        }

        Response::success([
            'external_reference' => $externalRef,
            'campay_reference'   => $campayRef,
            'ussd_code'          => $result['ussd_code']  ?? null,
            'operator'           => $result['operator']   ?? null,
            'amount'             => $amount,
            'currency'           => 'XAF',
        ], 'Payment initiated. Please approve the prompt on your phone.');
    }

    // ── GET /api/payment/status/{ref} ─────────────────────────────────────────
    /**
     * The frontend polls this until status is SUCCESSFUL or FAILED.
     * {ref} is our external_reference (UUID).
     *
     * On SUCCESSFUL: activates the subscription and returns subscription data.
     */
    public function status(array $params): void
    {
        $externalRef = $params['ref'] ?? '';
        $user = AuthMiddleware::user();

        // Load our payment row
        $payment = $this->db->fetchOne(
            'SELECT * FROM payments WHERE external_reference = ? AND user_id = ?',
            [$externalRef, $user['id']]
        );

        if (!$payment) {
            Response::notFound('Payment record not found.');
        }

        // Already resolved — return cached result without hitting CamPay again
        if ($payment['status'] === 'SUCCESSFUL') {
            Response::success(['status' => 'SUCCESSFUL', 'subscription_activated' => true]);
        }
        if ($payment['status'] === 'FAILED') {
            Response::success(['status' => 'FAILED', 'reason' => $payment['failure_reason']]);
        }

        // Still PENDING — ask CamPay for the latest status
        if (empty($payment['campay_reference'])) {
            Response::success(['status' => 'PENDING']);
        }

        try {
            $result = $this->campay->getTransactionStatus($payment['campay_reference']);
        } catch (\RuntimeException $e) {
            // Transient error — stay PENDING so the client retries
            Response::success(['status' => 'PENDING', 'note' => $e->getMessage()]);
        }

        $campayStatus = strtoupper($result['status'] ?? 'PENDING');

        if ($campayStatus === 'SUCCESSFUL') {
            $this->activateSubscription($payment, $result);
            Response::success(['status' => 'SUCCESSFUL', 'subscription_activated' => true]);
        }

        if ($campayStatus === 'FAILED') {
            $reason = $result['message'] ?? 'Payment declined by operator.';
            $this->db->query(
                "UPDATE payments SET status = 'FAILED', failure_reason = ? WHERE external_reference = ?",
                [$reason, $externalRef]
            );
            Response::success(['status' => 'FAILED', 'reason' => $reason]);
        }

        // Still PENDING
        Response::success(['status' => 'PENDING']);
    }

    // ── POST /api/payment/webhook ─────────────────────────────────────────────
    /**
     * CamPay calls this URL asynchronously when a transaction settles.
     * No JWT auth — validated by matching the campay_reference in our DB.
     *
     * Expected body (from CamPay):
     *   { reference, external_reference, status, amount, currency,
     *     operator, code, operator_reference }
     */
    public function webhook(): void
    {
        $body = $this->getBody();

        $campayRef    = $body['reference']          ?? '';
        $externalRef  = $body['external_reference'] ?? '';
        $campayStatus = strtoupper($body['status']  ?? '');

        if ($campayRef === '' || $campayStatus === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields.']);
            exit;
        }

        // Find the payment row (match on campay_reference OR external_reference)
        $payment = $this->db->fetchOne(
            'SELECT * FROM payments WHERE campay_reference = ? OR external_reference = ?',
            [$campayRef, $externalRef]
        );

        if (!$payment) {
            // Unknown reference — acknowledge to prevent CamPay retries, log silently
            http_response_code(200);
            echo json_encode(['received' => true]);
            exit;
        }

        // Idempotency: already processed
        if ($payment['status'] !== 'PENDING') {
            http_response_code(200);
            echo json_encode(['received' => true]);
            exit;
        }

        // Persist campay_reference in case initiate() didn't store it yet
        if (empty($payment['campay_reference']) && $campayRef !== '') {
            $this->db->query(
                'UPDATE payments SET campay_reference = ? WHERE id = ?',
                [$campayRef, $payment['id']]
            );
            $payment['campay_reference'] = $campayRef;
        }

        if ($campayStatus === 'SUCCESSFUL') {
            $this->activateSubscription($payment, $body);
        } elseif ($campayStatus === 'FAILED') {
            $reason = $body['message'] ?? 'Payment declined by operator.';
            $this->db->query(
                "UPDATE payments SET status = 'FAILED', failure_reason = ?,
                 campay_code = ?, operator_reference = ?, operator = ?
                 WHERE id = ?",
                [
                    $reason,
                    $body['code']               ?? null,
                    $body['operator_reference'] ?? null,
                    $body['operator']           ?? null,
                    $payment['id'],
                ]
            );
        }

        http_response_code(200);
        echo json_encode(['received' => true]);
        exit;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Mark the payment SUCCESSFUL, then cancel any existing active
     * subscription for the user and insert a fresh active one.
     * Sends a confirmation email to the user's registered email address.
     *
     * @param array $payment  Our payments DB row
     * @param array $campay   CamPay transaction response / webhook body
     */
    private function activateSubscription(array $payment, array $campay): void
    {
        // Update payment record
        $this->db->query(
            "UPDATE payments
             SET status = 'SUCCESSFUL',
                 campay_code = ?,
                 operator_reference = ?,
                 operator = ?,
                 campay_reference = COALESCE(campay_reference, ?)
             WHERE id = ?",
            [
                $campay['code']               ?? null,
                $campay['operator_reference'] ?? null,
                $campay['operator']           ?? null,
                $campay['reference']          ?? null,
                $payment['id'],
            ]
        );

        // Cancel any existing active subscription for this user
        $this->db->query(
            "UPDATE user_subscriptions
             SET status = 'cancelled', cancelled_at = CURRENT_TIMESTAMP
             WHERE user_id = ? AND status = 'active'",
            [$payment['user_id']]
        );

        // Calculate expiry
        $expiresAt = $payment['billing_cycle'] === 'yearly'
            ? date('Y-m-d H:i:s', strtotime('+1 year'))
            : date('Y-m-d H:i:s', strtotime('+1 month'));

        // Activate new subscription
        $this->db->insert('user_subscriptions', [
            'user_id'         => $payment['user_id'],
            'subscription_id' => $payment['subscription_id'],
            'status'          => 'active',
            'billing_cycle'   => $payment['billing_cycle'],
            'started_at'      => date('Y-m-d H:i:s'),
            'expires_at'      => $expiresAt,
        ]);

        // ── Send confirmation email ───────────────────────────────────────────
        $this->sendConfirmationEmail($payment, $campay, $expiresAt);
    }

    /**
     * Send a subscription confirmation email to the user.
     * Runs silently — a mail failure must NOT break the subscription activation.
     */
    private function sendConfirmationEmail(array $payment, array $campay, string $expiresAt): void
    {
        try {
            // Fetch user email, name, and plan name
            $user = $this->db->fetchOne(
                'SELECT email, full_name, username FROM users WHERE id = ?',
                [$payment['user_id']]
            );
            $plan = $this->db->fetchOne(
                'SELECT name FROM subscriptions WHERE id = ?',
                [$payment['subscription_id']]
            );

            if (!$user || empty($user['email'])) return;

            $config  = require ROOT_PATH . '/app/config/config.php';
            $mailer  = new MailService($config['mail']);

            $html = EmailTemplates::subscriptionConfirmation([
                'user_name'    => $user['full_name'] ?: $user['username'],
                'plan_name'    => $plan['name']      ?? 'Pro',
                'billing_cycle'=> $payment['billing_cycle'],
                'amount'       => $payment['amount'],
                'currency'     => $payment['currency'],
                'operator'     => $campay['operator']  ?? '',
                'campay_code'  => $campay['code']      ?? '',
                'expires_at'   => $expiresAt,
                'app_url'      => $config['app']['url'] ?? 'http://localhost/CamLingua',
            ]);

            $mailer->send(
                $user['email'],
                'Your CamLingua ' . ($plan['name'] ?? 'Pro') . ' subscription is confirmed!',
                $html,
                $user['full_name'] ?: $user['username']
            );
        } catch (\Throwable $e) {
            // Log silently — never surface mail errors to the user
            error_log('CamLingua MailService error: ' . $e->getMessage());
        }
    }

    /**
     * Generate a RFC 4122 v4 UUID.
     */
    private function generateUuid(): string
    {
        $data    = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // variant bits
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
