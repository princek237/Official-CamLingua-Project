<?php
/**
 * Admin Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Database;

class AdminController extends Controller
{
    /** @var Database */
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getDashboardStats(): void
    {
        try {
            // Total Users
            $usersCount = (int) $this->db->fetchOne('SELECT COUNT(*) as count FROM users')['count'];

            // Total Translations
            $translationsCount = (int) $this->db->fetchOne('SELECT COUNT(*) as count FROM translation_history')['count'];

            // Total Languages (approximated by counting distinct source_lang and target_lang used, or simply distinct source_lang + target_lang combined, but easier to use target_lang)
            $languagesCountRow = $this->db->fetchOne('
                SELECT COUNT(DISTINCT lang) as count FROM (
                    SELECT source_lang as lang FROM translation_history
                    UNION
                    SELECT target_lang as lang FROM translation_history
                ) as langs
            ');
            $languagesCount = (int) $languagesCountRow['count'];
            
            // Subscriptions/Revenue count (Active)
            $subscriptionsCount = (int) $this->db->fetchOne("SELECT COUNT(*) as count FROM user_subscriptions WHERE status = 'active'")['count'];

            // Recent Translations
            $recentTranslations = $this->db->fetchAll('
                SELECT 
                    th.id, th.source_lang, th.target_lang, th.source_text, th.created_at, u.username as user_id 
                FROM translation_history th 
                LEFT JOIN users u ON th.user_id = u.id 
                ORDER BY th.created_at DESC 
                LIMIT 5
            ');

            // Recent Users
            $recentUsers = $this->db->fetchAll('
                SELECT id, username, email, full_name, role, created_at 
                FROM users 
                ORDER BY created_at DESC 
                LIMIT 5
            ');

            // Daily Translations (last 30 days)
            $dailyTranslations = $this->db->fetchAll('
                SELECT DATE(created_at) as date, COUNT(*) as count 
                FROM translation_history 
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at)
                ORDER BY DATE(created_at) ASC
            ');
            
            $chartLabels = [];
            $chartValues = [];
            foreach ($dailyTranslations as $row) {
                $chartLabels[] = date('M j', strtotime($row['date']));
                $chartValues[] = (int) $row['count'];
            }

            // Top Languages (Target Lang)
            $topLanguages = $this->db->fetchAll('
                SELECT target_lang as name, COUNT(*) as count 
                FROM translation_history 
                GROUP BY target_lang 
                ORDER BY count DESC 
                LIMIT 5
            ');

            // If empty, return empty values but not null to prevent JS errors
            if (empty($chartLabels)) {
                $chartLabels = ['No Data'];
                $chartValues = [0];
            }

            Response::success([
                'stats' => [
                    'users' => $usersCount,
                    'translations' => $translationsCount,
                    'languages' => $languagesCount,
                    'subscriptions' => $subscriptionsCount,
                ],
                'recent_translations' => $recentTranslations,
                'recent_users' => $recentUsers,
                'chart_data' => [
                    'labels' => $chartLabels,
                    'values' => $chartValues
                ],
                'top_languages' => $topLanguages
            ]);

        } catch (\Exception $e) {
            Response::serverError('Error fetching dashboard stats: ' . $e->getMessage());
        }
    }

    public function getUsersList(): void
    {
        try {
            $users = $this->db->fetchAll('
                SELECT id, username, email, full_name, role, is_active, created_at 
                FROM users 
                ORDER BY created_at DESC 
                LIMIT 100
            ');
            Response::success(['users' => $users]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching users: ' . $e->getMessage());
        }
    }

    public function getLanguagesList(): void
    {
        try {
            $languages = $this->db->fetchAll('
                SELECT target_lang as name, COUNT(*) as count 
                FROM translation_history 
                GROUP BY target_lang 
                ORDER BY count DESC
            ');
            Response::success(['languages' => $languages]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching languages: ' . $e->getMessage());
        }
    }

    public function getTranslationsList(): void
    {
        try {
            $translations = $this->db->fetchAll('
                SELECT 
                    th.id, th.source_lang, th.target_lang, th.source_text, th.translated_text, th.created_at, u.username as user_id 
                FROM translation_history th 
                LEFT JOIN users u ON th.user_id = u.id 
                ORDER BY th.created_at DESC 
                LIMIT 100
            ');
            Response::success(['translations' => $translations]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching translations: ' . $e->getMessage());
        }
    }

    public function getSubscriptionsList(): void
    {
        try {
            $subscriptions = $this->db->fetchAll('
                SELECT 
                    us.id, u.username, s.name as plan_name, us.status, us.billing_cycle, us.started_at, us.expires_at
                FROM user_subscriptions us
                JOIN users u ON us.user_id = u.id
                JOIN subscriptions s ON us.subscription_id = s.id
                ORDER BY us.started_at DESC
                LIMIT 100
            ');
            Response::success(['subscriptions' => $subscriptions]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching subscriptions: ' . $e->getMessage());
        }
    }

    public function getReportsList(): void
    {
        try {
            $reports = $this->db->fetchAll('
                SELECT 
                    id, full_name, email, subject, message, status, created_at
                FROM contact_messages
                ORDER BY created_at DESC
                LIMIT 100
            ');
            Response::success(['reports' => $reports]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching reports: ' . $e->getMessage());
        }
    }
}
