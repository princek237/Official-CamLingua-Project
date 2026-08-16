<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Database;
use App\Middleware\AuthMiddleware;

class AdminUserController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── GET /api/admin/users ───────────────────────────────────────────────────
    public function index(): void
    {
        try {
            $page   = isset($_GET['page'])  ? max(1, (int)$_GET['page'])  : 1;
            $limit  = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
            $offset = ($page - 1) * $limit;
            $search = $_GET['search'] ?? '';
            $status = $_GET['status'] ?? '';

            $where  = [];
            $params = [];

            if ($search !== '') {
                $where[]  = '(username LIKE ? OR email LIKE ? OR full_name LIKE ?)';
                $params[] = "%$search%";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            if ($status !== '') {
                $where[]  = 'status = ?';
                $params[] = $status;
            }

            $whereSql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            $total = (int) $this->db->fetchOne(
                "SELECT COUNT(*) as total FROM users $whereSql", $params
            )['total'];

            $sql   = "SELECT id, username, email, phone_number, full_name, role, status, created_at
                      FROM users $whereSql ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
            $users = $this->db->fetchAll($sql, $params);

            Response::success([
                'users'      => $users,
                'pagination' => [
                    'total'       => $total,
                    'page'        => $page,
                    'limit'       => $limit,
                    'total_pages' => (int) ceil($total / $limit),
                ],
            ]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching users: ' . $e->getMessage());
        }
    }

    // ── GET /api/admin/users/:id ───────────────────────────────────────────────
    public function show(array $params): void
    {
        $id = (int)($params['id'] ?? 0);
        try {
            $user = $this->db->fetchOne(
                'SELECT id, username, email, phone_number, full_name, bio, role, status,
                        email_verified, created_at, role_assigned_by, role_assigned_at
                 FROM users WHERE id = ?',
                [$id]
            );
            if (!$user) {
                Response::error('User not found.', 404);
            }
            Response::success(['user' => $user]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching user: ' . $e->getMessage());
        }
    }

    // ── POST /api/admin/users ──────────────────────────────────────────────────
    public function store(): void
    {
        $body   = $this->getBody();
        $errors = $this->validateRequired($body, ['username', 'email', 'password', 'status']);
        if ($errors) {
            $this->validationError($errors);
        }

        $username     = $this->sanitize($body['username']);
        $email        = strtolower($this->sanitize($body['email']));
        $passwordHash = password_hash($body['password'], PASSWORD_DEFAULT);
        // Role is always 'user' when creating via admin form.
        // To grant admin, use PUT /api/admin/users/:id/role afterwards.
        $status   = $body['status'];
        $fullName = $this->sanitize($body['full_name'] ?? '');
        $phone    = $this->sanitize($body['phone_number'] ?? '');

        try {
            $existing = $this->db->fetchOne(
                'SELECT id FROM users WHERE username = ? OR email = ?',
                [$username, $email]
            );
            if ($existing) {
                Response::validationError(['username' => 'Username or email already exists.']);
            }

            $userId = $this->db->insert('users', [
                'username'      => $username,
                'email'         => $email,
                'phone_number'  => $phone    ?: null,
                'password_hash' => $passwordHash,
                'full_name'     => $fullName ?: null,
                'role'          => 'user',   // always starts as 'user'
                'status'        => $status,
            ]);

            // Assign free plan
            $free = $this->db->fetchOne("SELECT id FROM subscriptions WHERE slug = 'free'");
            if ($free) {
                $this->db->insert('user_subscriptions', [
                    'user_id'         => $userId,
                    'subscription_id' => $free['id'],
                    'status'          => 'active',
                ]);
            }

            Response::success(['message' => 'User created successfully.'], 201);
        } catch (\Exception $e) {
            Response::serverError('Error creating user: ' . $e->getMessage());
        }
    }

    // ── PUT /api/admin/users/:id ───────────────────────────────────────────────
    public function update(array $params): void
    {
        $id   = (int)($params['id'] ?? 0);
        $body = $this->getBody();

        $errors = $this->validateRequired($body, ['username', 'email', 'status']);
        if ($errors) {
            $this->validationError($errors);
        }

        $username = $this->sanitize($body['username']);
        $email    = strtolower($this->sanitize($body['email']));
        $status   = $body['status'];
        $fullName = $this->sanitize($body['full_name'] ?? '');
        $phone    = $this->sanitize($body['phone_number'] ?? '');

        // Prevent role from being changed via this endpoint
        if (isset($body['role'])) {
            Response::error('Role changes must use PUT /api/admin/users/:id/role.', 400);
        }

        try {
            $existing = $this->db->fetchOne(
                'SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?',
                [$username, $email, $id]
            );
            if ($existing) {
                Response::validationError(['username' => 'Username or email already taken by another user.']);
            }

            $sqlParams = [$username, $email, $phone ?: null, $fullName ?: null, $status];
            $sql       = 'UPDATE users SET username=?, email=?, phone_number=?, full_name=?, status=?';

            if (!empty($body['password'])) {
                $sql         .= ', password_hash=?';
                $sqlParams[]  = password_hash($body['password'], PASSWORD_DEFAULT);
            }

            $sql         .= ' WHERE id=?';
            $sqlParams[]  = $id;

            $this->db->execute($sql, $sqlParams);
            Response::success(['message' => 'User updated successfully.']);
        } catch (\Exception $e) {
            Response::serverError('Error updating user: ' . $e->getMessage());
        }
    }

    // ── PUT /api/admin/users/:id/role ──────────────────────────────────────────
    /**
     * Assign or revoke the admin role for a target user.
     * Only an authenticated admin can call this endpoint (enforced by AdminMiddleware).
     * Additional rules enforced here:
     *   1. An admin cannot change their own role.
     *   2. The requested role must be 'user' or 'admin'.
     *   3. The action is recorded in role_assigned_by / role_assigned_at for audit.
     */
    public function assignRole(array $params): void
    {
        $targetId = (int)($params['id'] ?? 0);
        $body     = $this->getBody();

        $errors = $this->validateRequired($body, ['role']);
        if ($errors) {
            $this->validationError($errors);
        }

        $newRole = strtolower(trim($body['role']));
        if (!in_array($newRole, ['user', 'admin'], true)) {
            Response::validationError(['role' => "Role must be 'user' or 'admin'."]);
        }

        // Get the acting admin from the JWT-verified session
        $actingAdmin = AuthMiddleware::user();
        if (!$actingAdmin) {
            Response::unauthorized('Authentication required.');
        }

        // Rule 1: An admin cannot change their own role
        if ((int)$actingAdmin['id'] === $targetId) {
            Response::error('You cannot change your own role.', 403);
        }

        try {
            $target = $this->db->fetchOne('SELECT id, username, role FROM users WHERE id = ?', [$targetId]);
            if (!$target) {
                Response::error('User not found.', 404);
            }

            if ($target['role'] === $newRole) {
                Response::success(['message' => 'User already has that role. No change made.']);
            }

            // Record the audit trail
            $this->db->execute(
                'UPDATE users
                 SET role = ?, role_assigned_by = ?, role_assigned_at = CURRENT_TIMESTAMP
                 WHERE id = ?',
                [$newRole, (int)$actingAdmin['id'], $targetId]
            );

            $action  = $newRole === 'admin' ? 'granted admin privileges' : 'revoked admin privileges';
            Response::success([
                'message'    => "Successfully {$action} for user '{$target['username']}'.",
                'user_id'    => $targetId,
                'new_role'   => $newRole,
                'changed_by' => (int)$actingAdmin['id'],
            ]);
        } catch (\Exception $e) {
            Response::serverError('Error updating role: ' . $e->getMessage());
        }
    }

    // ── DELETE /api/admin/users/:id ────────────────────────────────────────────
    public function destroy(array $params): void
    {
        $id          = (int)($params['id'] ?? 0);
        $actingAdmin = AuthMiddleware::user();

        // Prevent self-deletion
        if ($actingAdmin && (int)$actingAdmin['id'] === $id) {
            Response::error('You cannot delete your own account.', 403);
        }

        try {
            $affected = $this->db->execute('DELETE FROM users WHERE id = ?', [$id]);
            if ($affected === 0) {
                Response::error('User not found.', 404);
            }
            Response::success(['message' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            Response::serverError('Error deleting user: ' . $e->getMessage());
        }
    }
}
