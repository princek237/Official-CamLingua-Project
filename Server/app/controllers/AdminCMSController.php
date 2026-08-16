<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Database;

/**
 * AdminCMSController
 *
 * Manages editable website content stored in the `settings` table.
 * Admin routes are protected by AuthMiddleware + AdminMiddleware.
 * The public GET endpoint is open so frontend pages can fetch content
 * without a token.
 *
 * Routes:
 *   GET  /api/cms          – public, returns all CMS keys as a flat object
 *   GET  /api/admin/cms    – admin, same data + descriptions
 *   PUT  /api/admin/cms    – admin, bulk-upsert one or many keys
 */
class AdminCMSController extends Controller
{
    private $db;

    /** Keys that are purely operational and must NOT be exposed/edited via CMS */
    private const BLOCKED_KEYS = ['translation_api_provider'];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Public endpoint ───────────────────────────────────────────────────────

    /**
     * GET /api/cms
     * Returns all settings as a flat key→value object. Safe for public use.
     */
    public function publicIndex(): void
    {
        try {
            $rows = $this->db->fetchAll(
                'SELECT setting_key, setting_value FROM settings ORDER BY setting_key'
            );

            $data = [];
            foreach ($rows as $row) {
                $key = $row['setting_key'];
                if (in_array($key, self::BLOCKED_KEYS, true)) {
                    continue;
                }
                $data[$key] = $row['setting_value'];
            }

            Response::success(['content' => $data]);
        } catch (\Exception $e) {
            Response::serverError('Failed to load site content.');
        }
    }

    // ── Admin endpoints ───────────────────────────────────────────────────────

    /**
     * GET /api/admin/cms
     * Returns all settings with descriptions for the admin editor.
     */
    public function index(): void
    {
        try {
            $rows = $this->db->fetchAll(
                'SELECT setting_key, setting_value, description FROM settings ORDER BY setting_key'
            );

            $data = [];
            foreach ($rows as $row) {
                $data[$row['setting_key']] = [
                    'value'       => $row['setting_value'],
                    'description' => $row['description'],
                ];
            }

            Response::success(['content' => $data]);
        } catch (\Exception $e) {
            Response::serverError('Failed to load CMS content.');
        }
    }

    /**
     * PUT /api/admin/cms
     * Bulk-upsert settings. Body: { "key": "value", ... }
     * Creates the key if it doesn't exist yet (INSERT … ON DUPLICATE KEY UPDATE).
     */
    public function update(): void
    {
        $body = $this->getBody();

        if (!is_array($body) || empty($body)) {
            Response::validationError(['error' => 'Request body must be a non-empty JSON object.']);
            return;
        }

        // Reject attempts to touch blocked operational keys
        foreach (array_keys($body) as $key) {
            if (in_array($key, self::BLOCKED_KEYS, true)) {
                Response::validationError(['error' => "Key '{$key}' cannot be edited via the CMS."]);
                return;
            }
        }

        $pdo = $this->db->getConnection();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO settings (setting_key, setting_value)
                 VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value),
                                         updated_at    = CURRENT_TIMESTAMP'
            );

            foreach ($body as $key => $value) {
                $key   = trim((string) $key);
                $value = is_string($value) ? $value : json_encode($value);
                if ($key === '') continue;
                $stmt->execute([$key, $value]);
            }

            $pdo->commit();

            Response::success(['message' => 'Content updated successfully.']);
        } catch (\Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            Response::serverError('Failed to save content: ' . $e->getMessage());
        }
    }
}
