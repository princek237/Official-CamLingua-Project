<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Database;

class AdminTranslationController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        try {
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
            $offset = ($page - 1) * $limit;
            
            $search = $_GET['search'] ?? '';
            $userId = $_GET['user_id'] ?? '';
            $language = $_GET['language'] ?? '';
            $status = $_GET['status'] ?? '';
            $date = $_GET['date'] ?? '';

            $where = [];
            $params = [];

            if ($search !== '') {
                $where[] = '(th.source_text LIKE ? OR th.translated_text LIKE ?)';
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            if ($userId !== '') {
                $where[] = 'th.user_id = ?';
                $params[] = $userId;
            }
            if ($language !== '') {
                $where[] = '(th.source_lang = ? OR th.target_lang = ?)';
                $params[] = $language;
                $params[] = $language;
            }
            if ($status !== '') {
                $where[] = 'th.status = ?';
                $params[] = $status;
            }
            if ($date !== '') {
                $where[] = 'DATE(th.created_at) = ?';
                $params[] = $date;
            }

            $whereSql = '';
            if (count($where) > 0) {
                $whereSql = 'WHERE ' . implode(' AND ', $where);
            }

            // Get total count for pagination
            $countSql = "SELECT COUNT(*) as total FROM translation_history th $whereSql";
            $total = (int) $this->db->fetchOne($countSql, $params)['total'];

            // Get records
            $sql = "
                SELECT 
                    th.id, th.source_lang, th.target_lang, th.source_text, th.translated_text, th.status, th.created_at, u.username as user_id 
                FROM translation_history th 
                LEFT JOIN users u ON th.user_id = u.id 
                $whereSql 
                ORDER BY th.created_at DESC 
                LIMIT $limit OFFSET $offset
            ";
            
            $translations = $this->db->fetchAll($sql, $params);

            Response::success([
                'translations' => $translations,
                'pagination' => [
                    'total' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching translations: ' . $e->getMessage());
        }
    }

    public function show(int $id): void
    {
        try {
            $translation = $this->db->fetchOne('
                SELECT th.*, u.username 
                FROM translation_history th 
                LEFT JOIN users u ON th.user_id = u.id 
                WHERE th.id = ?
            ', [$id]);
            
            if (!$translation) {
                Response::error('Translation not found', 404);
            }
            Response::success(['translation' => $translation]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching translation: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): void
    {
        try {
            $this->db->execute('DELETE FROM translation_history WHERE id = ?', [$id]);
            Response::success(['message' => 'Translation deleted successfully']);
        } catch (\Exception $e) {
            Response::serverError('Error deleting translation: ' . $e->getMessage());
        }
    }
}
