<?php
require_once __DIR__ . '/../backend/config/Database.php';

$db = (new \App\Config\Database())->getConnection();
$tbl = $db->query("SHOW TABLES LIKE 'online_payment_submissions'")->fetch();

if (!$tbl) {
    echo "Creating online_payment_submissions table...\n";
    $sql = "
    CREATE TABLE IF NOT EXISTS `online_payment_submissions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `application_id` int(11) NOT NULL,
      `enrollment_id` int(11) NOT NULL,
      `assessment_id` int(11) NOT NULL,
      `payment_channel` varchar(50) NOT NULL DEFAULT 'GCash',
      `reference_no` varchar(100) NOT NULL,
      `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
      `payment_date` date NOT NULL,
      `receipt_file_path` varchar(255) DEFAULT NULL,
      `receipt_original_name` varchar(255) DEFAULT NULL,
      `status` enum('Pending Verification','Approved','Rejected') NOT NULL DEFAULT 'Pending Verification',
      `rejection_reason` text DEFAULT NULL,
      `verified_by` int(11) DEFAULT NULL,
      `verified_at` datetime DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `application_id` (`application_id`),
      KEY `assessment_id` (`assessment_id`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $db->exec($sql);
    echo "Table created successfully.\n";
} else {
    echo "Table online_payment_submissions already exists.\n";
}
