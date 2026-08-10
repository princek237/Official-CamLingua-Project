<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Database;

class AdminUserController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // GET /api/admin/users
    public function index(): void
    {
        try {
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
            $offset = ($page - 1) * $limit;
            $search = $_GET['search'] ?? '';
            $status = $_GET['status'] ?? '';

            $where = [];
            $params = [];

            if ($search !== '') {
                $where[] = '(username LIKE ? OR email LIKE ? OR full_name LIKE ?)';
                $params[] = "%$search%";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            if ($status !== '') {
                $where[] = 'status = ?';
                $params[] = $status;
            }

            $whereSql = '';
            if (count($where) > 0) {
                $whereSql = 'WHERE ' . implode(' AND ', $where);
            }

            // Get total count for pagination
            $countSql = "SELECT COUNT(*) as total FROM users $whereSql";
            $total = (int) $this->db->fetchOne($countSql, $params)['total'];

            // Get records
            $sql = "SELECT id, username, email, phone_number, full_name, role, status, created_at 
                    FROM users $whereSql ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
            
            $users = $this->db->fetchAll($sql, $params);

            Response::success([
                'users' => $users,
                'pagination' => [
                    'total' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching users: ' . $e->getMessage());
        }
    }

    // GET /api/admin/users/:id
    public function show(int $id): void
    {
        try {
            $user = $this->db->fetchOne('SELECT id, username, email, phone_number, full_name, bio, role, status, email_verified, created_at FROM users WHERE id = ?', [$id]);
            if (!$user) {
                Response::error('User not found', 404);
            }
            Response::success(['user' => $user]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching user: ' . $e->getMessage());
        }
    }

    // POST /api/admin/users
    public function store(): void
    {
        $body = $this->getBody();
        $errors = $this->validateRequired($body, ['username', 'email', 'password', 'role', 'status']);
        if ($errors) {
            $this->validationError($errors);
        }

        $username = $this->sanitize($body['username']);
        $email = strtolower($this->sanitize($body['email']));
        $passwordHash = password_hash($body['password'], PASSWORD_DEFAULT);
        $role = $body['role'];
        $status = $body['status'];
        $fullName = $this->sanitize($body['full_name'] ?? '');
        $phone = $this->sanitize($body['phone_number'] ?? '');

        try {
            // Check if username or email exists
            $existing = $this->db->fetchOne('SELECT id FROM users WHERE username = ? OR email = ?', [$username, $email]);
            if ($existing) {
                Response::validationError(['username' => 'Username or email already exists.']);
            }

            $this->db->execute(
                'INSERT INTO users (username, email, phone_number, password_hash, full_name, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$username, $email, $phone ?: null, $passwordHash, $fullName ?: null, $role, $status]
            );

            Response::success(['message' => 'User created successfully'], 201);
        } catch (\Exception $e) {
            Response::serverError('Error creating user: ' . $e->getMessage());
        }
    }

    // PUT /api/admin/users/:id
    public function update(int $id): void
    {
        $body = $this->getBody();
        $errors = $this->validateRequired($body, ['username', 'email', 'role', 'status']);
        if ($errors) {
            $this->validationError($errors);
        }

        $username = $this->sanitize($body['username']);
        $email = strtolower($this->sanitize($body['email']));
        $role = $body['role'];
        $status = $body['status'];
        $fullName = $this->sanitize($body['full_name'] ?? '');
        $phone = $this->sanitize($body['phone_number'] ?? '');

        try {
            // Check if username or email exists for OTHER users
            $existing = $this->db->fetchOne('SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?', [$username, $email, $id]);
            if ($existing) {
                Response::validationError(['username' => 'Username or email already exists for another user.']);
            }

            $params = [$username, $email, $phone ?: null, $fullName ?: null, $role, $status];
            $sql = 'UPDATE users SET username=?, email=?, phone_number=?, full_name=?, role=?, status=?';

            if (!empty($body['password'])) {
                $sql .= ', password_hash=?';
                $params[] = password_hash($body['password'], PASSWORD_DEFAULT);
            }

            $sql .= ' WHERE id=?';
            $params[] = $id;

            $this->db->execute($sql, $params);

            Response::success(['message' => 'User updated successfully']);
        } catch (\Exception $e) {
            Response::serverError('Error updating user: ' . $e->getMessage());
        }
    }

    // DELETE /api/admin/users/:id
    public function destroy(int $id): void
    {
        try {
            $this->db->execute('DELETE FROM users WHERE id = ?', [$id]);
            Response::success(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            Response::serverError('Error deleting user: ' . $e->getMessage());
        }
    }
}
