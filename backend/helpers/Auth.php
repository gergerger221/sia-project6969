<?php
// backend/helpers/Auth.php
namespace App\Helpers;

use App\Config\Database;
use App\Config\Response;
use PDO;

class Auth {
    /**
     * Generates a base64 session token.
     */
    public static function generateToken(int $userId): string {
        $token = bin2hex(random_bytes(32));
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
        $stmt->execute(['token' => $token, 'id' => $userId]);
        return $token;
    }

    /**
     * Extracts and validates user from Authorization header.
     */
    public static function user(): ?array {
        $authHeader = '';
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }
        if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (!$authHeader && isset($_GET['token'])) {
            $authHeader = 'Bearer ' . $_GET['token'];
        }

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT u.id, u.role_id, u.username, u.email, u.student_id, u.status,
                       r.name as role_name, r.slug as role_slug,
                       p.first_name, p.middle_name, p.last_name, p.suffix, p.gender, p.contact_number
                FROM users u
                JOIN roles r ON u.role_id = r.id
                LEFT JOIN user_profiles p ON u.id = p.user_id
                WHERE u.remember_token = :token AND u.status = 'Active'
                LIMIT 1
            ");
            $stmt->execute(['token' => $token]);
            $user = $stmt->fetch();
            return $user ?: null;
        }

        return null;
    }

    /**
     * Ensures the request is authenticated.
     */
    public static function requireAuth(): array {
        $user = self::user();
        if (!$user) {
            Response::error('Unauthorized. Please login to continue.', 401);
        }
        return $user;
    }

    /**
     * Ensures the authenticated user has one of the allowed roles.
     */
    public static function requireRole(array $allowedRoles): array {
        $user = self::requireAuth();
        if (!in_array($user['role_slug'], $allowedRoles) && $user['role_slug'] !== 'admin') {
            Response::error('Forbidden: You do not have permission to access this resource.', 403);
        }
        return $user;
    }

    /**
     * Logs an action into the audit_logs table.
     */
    public static function logAudit(string $action, string $details = '', ?int $userId = null): void {
        try {
            $db = Database::getConnection();
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI/Unknown';
            $stmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, details, ip_address, user_agent)
                VALUES (:user_id, :action, :details, :ip, :ua)
            ");
            $stmt->execute([
                'user_id' => $userId,
                'action'  => $action,
                'details' => $details,
                'ip'      => $ip,
                'ua'      => $ua
            ]);
        } catch (\Exception $e) {
            // Ignore audit logging errors to not break main transaction
        }
    }
}
