<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Database;

class AdminSettingsController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        try {
            $settings = $this->db->fetchAll('SELECT setting_key, setting_value, description FROM settings');
            
            // Format settings as a key-value object for easier frontend consumption
            $formatted = [];
            foreach ($settings as $setting) {
                $formatted[$setting['setting_key']] = [
                    'value' => $setting['setting_value'],
                    'description' => $setting['description']
                ];
            }

            Response::success(['settings' => $formatted]);
        } catch (\Exception $e) {
            Response::serverError('Error fetching settings: ' . $e->getMessage());
        }
    }

    public function update(): void
    {
        $body = $this->getBody();
        if (!is_array($body) || empty($body)) {
            Response::validationError(['error' => 'Invalid settings data provided.']);
        }

        $pdo = $this->db->getConnection();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('UPDATE settings SET setting_value = ? WHERE setting_key = ?');

            foreach ($body as $key => $value) {
                $stmt->execute([is_string($value) ? $value : json_encode($value), $key]);
            }

            $pdo->commit();
            Response::success(['message' => 'Settings updated successfully']);
        } catch (\Exception $e) {
            $pdo->rollBack();
            Response::serverError('Error updating settings: ' . $e->getMessage());
        }
    }
}
