<?php
/**
 * HistoryController — PHP 7.4 compatible
 * GET    /api/history
 * DELETE /api/history/{id}
 * POST   /api/history/{id}/favorite
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Middleware\AuthMiddleware;

class HistoryController extends Controller
{
    /** @var Database */
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── GET /api/history ───────────────────────────────────────────────────────
    public function index(): void
    {
        $user = AuthMiddleware::user();

        $search   = $this->sanitize($this->query('search', ''));
        $lang     = $this->sanitize($this->query('lang', ''));
        $date     = $this->sanitize($this->query('date', ''));
        $page     = max(1, (int)$this->query('page', 1));
        $perPage  = min(50, max(5, (int)$this->query('per_page', 10)));
        $offset   = ($page - 1) * $perPage;

        // Build WHERE clause dynamically
        $conditions = ['user_id = ?'];
        $params     = [(int)$user['id']];

        if ($search !== '') {
            $conditions[] = '(source_text LIKE ? OR translated_text LIKE ?)';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
        }

        if ($lang !== '') {
            $conditions[] = '(source_lang = ? OR target_lang = ?)';
            $params[] = $lang;
            $params[] = $lang;
        }

        if ($date !== '') {
            switch ($date) {
                case 'today':
                    $conditions[] = 'DATE(created_at) = CURDATE()';
                    break;
                case 'week':
                    $conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
                    break;
                case 'month':
                    $conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
                    break;
                case 'year':
                    $conditions[] = 'created_at >= DATE_SUB(NOW(), INTERVAL 365 DAY)';
                    break;
            }
        }

        $where = implode(' AND ', $conditions);

        // Total count
        $totalRow = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM translation_history WHERE {$where}",
            $params
        );
        $total = (int)($totalRow['total'] ?? 0);

        // Fetch page
        $rows = $this->db->fetchAll(
            "SELECT * FROM translation_history WHERE {$where}
             ORDER BY created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        // Cast types
        $rows = array_map(function (array $row): array {
            $row['id']          = (int)$row['id'];
            $row['user_id']     = (int)$row['user_id'];
            $row['is_favorite'] = (bool)$row['is_favorite'];
            return $row;
        }, $rows);

        Response::success([
            'items'       => $rows,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int)ceil($total / $perPage),
        ]);
    }

    // ── DELETE /api/history/{id} ───────────────────────────────────────────────
    public function delete(array $params): void
    {
        $user = AuthMiddleware::user();
        $id   = (int)($params['id'] ?? 0);

        if ($id <= 0) {
            Response::badRequest('Invalid history ID.');
        }

        // Ensure the record belongs to this user
        $row = $this->db->fetchOne(
            'SELECT id FROM translation_history WHERE id = ? AND user_id = ?',
            [$id, $user['id']]
        );
        if (!$row) {
            Response::notFound('Translation not found.');
        }

        $this->db->query(
            'DELETE FROM translation_history WHERE id = ? AND user_id = ?',
            [$id, $user['id']]
        );

        Response::success([], 'Translation deleted.');
    }

    // ── POST /api/history/{id}/favorite ───────────────────────────────────────
    public function toggleFavorite(array $params): void
    {
        $user = AuthMiddleware::user();
        $id   = (int)($params['id'] ?? 0);

        if ($id <= 0) {
            Response::badRequest('Invalid history ID.');
        }

        $row = $this->db->fetchOne(
            'SELECT id, is_favorite FROM translation_history WHERE id = ? AND user_id = ?',
            [$id, $user['id']]
        );
        if (!$row) {
            Response::notFound('Translation not found.');
        }

        $newValue = $row['is_favorite'] ? 0 : 1;
        $this->db->update(
            'translation_history',
            ['is_favorite' => $newValue],
            ['id' => $id, 'user_id' => $user['id']]
        );

        Response::success(['is_favorite' => (bool)$newValue]);
    }
}
