<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Database;

class AdminLanguageController extends Controller
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

            $where = [];
            $params = [];

            if ($search !== '') {
                $where[] = '(name LIKE ? OR code LIKE ?)';
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            $whereSql = '';
            if (count($where) > 0) {
                $whereSql = 'WHERE ' . implode(' AND ', $where);
            }

            // Get total count for pagination
            $countSql = "SELECT COUNT(*) as total FROM languages $whereSql";
            $total = (int) $this->db->fetchOne($countSql, $params)['total'];

            // Get records with translation count
            // We use a subquery to count translations where this language is the target
            $sql = "
                SELECT l.id, l.name, l.code, l.is_active, l.created_at,
                (SELECT COUNT(*) FROM translation_history th WHERE th.target_lang = l.code) as translations_count
                FROM languages l
                $whereSql 
                ORDER BY l.name ASC 
                LIMIT $limit OFFSET $offset
            ";
            
            $languages = $this->db->fetchAll($sql, $params);

            Response::success([
                'languages' => $languages,
                'pagination' => [
                    'total' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching languages: ' . $e->getMessage());
        }
    }

    public function show(int $id): void
    {
        try {
            $language = $this->db->fetchOne('SELECT id, name, code, is_active FROM languages WHERE id = ?', [$id]);
            if (!$language) {
                Response::error('Language not found', 404);
            }
            Response::success(['language' => $language]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching language: ' . $e->getMessage());
        }
    }

    public function store(): void
    {
        $body = $this->getBody();
        $errors = $this->validateRequired($body, ['name', 'code']);
        if ($errors) {
            $this->validationError($errors);
        }

        $name = $this->sanitize($body['name']);
        $code = strtolower($this->sanitize($body['code']));
        $isActive = isset($body['is_active']) ? (int)$body['is_active'] : 1;

        try {
            $existing = $this->db->fetchOne('SELECT id FROM languages WHERE code = ?', [$code]);
            if ($existing) {
                Response::validationError(['code' => 'Language code already exists.']);
            }

            $this->db->execute(
                'INSERT INTO languages (name, code, is_active) VALUES (?, ?, ?)',
                [$name, $code, $isActive]
            );

            Response::success(['message' => 'Language created successfully'], 201);
        } catch (\Exception $e) {
            Response::serverError('Error creating language: ' . $e->getMessage());
        }
    }

    public function update(int $id): void
    {
        $body = $this->getBody();
        $errors = $this->validateRequired($body, ['name', 'code']);
        if ($errors) {
            $this->validationError($errors);
        }

        $name = $this->sanitize($body['name']);
        $code = strtolower($this->sanitize($body['code']));
        $isActive = isset($body['is_active']) ? (int)$body['is_active'] : 1;

        try {
            $existing = $this->db->fetchOne('SELECT id FROM languages WHERE code = ? AND id != ?', [$code, $id]);
            if ($existing) {
                Response::validationError(['code' => 'Language code already exists for another language.']);
            }

            $this->db->execute(
                'UPDATE languages SET name = ?, code = ?, is_active = ? WHERE id = ?',
                [$name, $code, $isActive, $id]
            );

            Response::success(['message' => 'Language updated successfully']);
        } catch (\Exception $e) {
            Response::serverError('Error updating language: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): void
    {
        try {
            $this->db->execute('DELETE FROM languages WHERE id = ?', [$id]);
            Response::success(['message' => 'Language deleted successfully']);
        } catch (\Exception $e) {
            Response::serverError('Error deleting language: ' . $e->getMessage());
        }
    }
}
