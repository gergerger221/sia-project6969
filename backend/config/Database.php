<?php
// backend/config/Database.php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    private string $host = '127.0.0.1';
    private string $db_name = 'sia_highschool_db';
    private string $username = 'root';
    private string $password = '';
    private string $charset = 'utf8mb4';

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $db = new self();
            self::$instance = $db->connect();
        }
        return self::$instance;
    }

    private function connect(): PDO {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => true,
        ];

        try {
            return new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage() . '. Please ensure MySQL is running in XAMPP and `sia_highschool_db` database is imported.'
            ]);
            exit;
        }
    }
}
