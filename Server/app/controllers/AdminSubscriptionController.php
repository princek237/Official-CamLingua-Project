<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Database;

class AdminSubscriptionController extends Controller
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
                $where[] = '(name LIKE ? OR description LIKE ?)';
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            $whereSql = '';
            if (count($where) > 0) {
                $whereSql = 'WHERE ' . implode(' AND ', $where);
            }

            $countSql = "SELECT COUNT(*) as total FROM subscriptions $whereSql";
            $total = (int) $this->db->fetchOne($countSql, $params)['total'];

            $sql = "
                SELECT s.id, s.name, s.slug, s.price_monthly, s.price_yearly, s.is_active,
                (SELECT COUNT(*) FROM user_subscriptions us WHERE us.subscription_id = s.id AND us.status = 'active') as active_subscribers
                FROM subscriptions s
                $whereSql
                ORDER BY s.price_monthly ASC
                LIMIT $limit OFFSET $offset
            ";
            
            $subscriptions = $this->db->fetchAll($sql, $params);

            Response::success([
                'subscriptions' => $subscriptions,
                'pagination' => [
                    'total' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching subscriptions: ' . $e->getMessage());
        }
    }

    public function show(int $id): void
    {
        try {
            $subscription = $this->db->fetchOne('SELECT * FROM subscriptions WHERE id = ?', [$id]);
            if (!$subscription) {
                Response::error('Subscription plan not found', 404);
            }
            Response::success(['subscription' => $subscription]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching subscription: ' . $e->getMessage());
        }
    }

    public function store(): void
    {
        $body = $this->getBody();
        $errors = $this->validateRequired($body, ['name', 'slug', 'price_monthly', 'price_yearly']);
        if ($errors) {
            $this->validationError($errors);
        }

        $name = $this->sanitize($body['name']);
        $slug = strtolower($this->sanitize($body['slug']));
        $priceMonthly = (float) $body['price_monthly'];
        $priceYearly = (float) $body['price_yearly'];
        $description = $this->sanitize($body['description'] ?? '');
        $isActive = isset($body['is_active']) ? (int)$body['is_active'] : 1;
        $features = isset($body['features']) ? json_encode($body['features']) : '[]';
        $limits = isset($body['limits']) ? json_encode($body['limits']) : '{}';

        try {
            $existing = $this->db->fetchOne('SELECT id FROM subscriptions WHERE slug = ?', [$slug]);
            if ($existing) {
                Response::validationError(['slug' => 'Slug already exists.']);
            }

            $this->db->execute(
                'INSERT INTO subscriptions (name, slug, description, price_monthly, price_yearly, features, limits, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [$name, $slug, $description, $priceMonthly, $priceYearly, $features, $limits, $isActive]
            );

            Response::success(['message' => 'Subscription plan created successfully'], 201);
        } catch (\Exception $e) {
            Response::serverError('Error creating subscription plan: ' . $e->getMessage());
        }
    }

    public function update(int $id): void
    {
        $body = $this->getBody();
        $errors = $this->validateRequired($body, ['name', 'slug', 'price_monthly', 'price_yearly']);
        if ($errors) {
            $this->validationError($errors);
        }

        $name = $this->sanitize($body['name']);
        $slug = strtolower($this->sanitize($body['slug']));
        $priceMonthly = (float) $body['price_monthly'];
        $priceYearly = (float) $body['price_yearly'];
        $description = $this->sanitize($body['description'] ?? '');
        $isActive = isset($body['is_active']) ? (int)$body['is_active'] : 1;
        $features = isset($body['features']) ? json_encode($body['features']) : '[]';
        $limits = isset($body['limits']) ? json_encode($body['limits']) : '{}';

        try {
            $existing = $this->db->fetchOne('SELECT id FROM subscriptions WHERE slug = ? AND id != ?', [$slug, $id]);
            if ($existing) {
                Response::validationError(['slug' => 'Slug already exists for another plan.']);
            }

            $this->db->execute(
                'UPDATE subscriptions SET name = ?, slug = ?, description = ?, price_monthly = ?, price_yearly = ?, features = ?, limits = ?, is_active = ? WHERE id = ?',
                [$name, $slug, $description, $priceMonthly, $priceYearly, $features, $limits, $isActive, $id]
            );

            Response::success(['message' => 'Subscription plan updated successfully']);
        } catch (\Exception $e) {
            Response::serverError('Error updating subscription plan: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): void
    {
        try {
            // Check if there are active subscribers
            $activeCount = (int) $this->db->fetchOne("SELECT COUNT(*) as count FROM user_subscriptions WHERE subscription_id = ? AND status = 'active'", [$id])['count'];
            if ($activeCount > 0) {
                Response::validationError(['error' => 'Cannot delete a plan with active subscribers. Disable it instead.']);
                return;
            }

            $this->db->execute('DELETE FROM subscriptions WHERE id = ?', [$id]);
            Response::success(['message' => 'Subscription plan deleted successfully']);
        } catch (\Exception $e) {
            Response::serverError('Error deleting subscription plan: ' . $e->getMessage());
        }
    }
}
