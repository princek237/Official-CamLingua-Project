<?php
/**
 * ContactController — PHP 7.4 compatible
 * POST /api/contact  (public)
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Core\JWT;

class ContactController extends Controller
{
    /** @var Database */
    private $db;

    private const ALLOWED_SUBJECTS = [
        'getting-started',
        'translation',
        'billing',
        'languages',
        'privacy',
        'technical',
        'other',
    ];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── POST /api/contact ──────────────────────────────────────────────────────
    public function submit(): void
    {
        $body = $this->getBody();

        // Validate required fields
        $errors = $this->validateRequired($body, ['full_name', 'email', 'subject', 'message']);
        if ($errors) {
            $this->validationError($errors);
        }

        $fullName = $this->sanitize($body['full_name']);
        $email    = strtolower($this->sanitize($body['email']));
        $subject  = $this->sanitize($body['subject']);
        $message  = $this->sanitize($body['message']);

        // Field validation
        if (strlen($fullName) < 2) {
            Response::validationError(['full_name' => 'Name must be at least 2 characters.']);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::validationError(['email' => 'Invalid email address.']);
        }
        if (!in_array($subject, self::ALLOWED_SUBJECTS, true)) {
            Response::validationError(['subject' => 'Invalid subject.']);
        }
        if (strlen($message) < 10) {
            Response::validationError(['message' => 'Message must be at least 10 characters.']);
        }
        if (strlen($message) > 2000) {
            Response::validationError(['message' => 'Message must not exceed 2000 characters.']);
        }

        // Detect if logged-in user (optional auth — token may or may not be present)
        $userId = null;
        $token  = JWT::fromHeader();
        if ($token) {
            try {
                $payload = JWT::decode($token);
                $userId  = (int)($payload['user_id'] ?? null);
            } catch (\Exception $e) {
                // Guest submission — that's fine
            }
        }

        // Save to database
        $this->db->insert('contact_messages', [
            'user_id'    => $userId,
            'full_name'  => $fullName,
            'email'      => $email,
            'subject'    => $subject,
            'message'    => $message,
            'status'     => 'new',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
        ]);

        Response::success([], "Thank you, {$fullName}! We'll get back to you within 24 hours.", 201);
    }
}
