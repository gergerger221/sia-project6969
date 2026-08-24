<?php
// scratch/migrate_online_payment_cols.php
declare(strict_types=1);

require_once __DIR__ . '/../backend/config/Database.php';

$db = App\Config\Database::getConnection();

echo "=== MIGRATING ONLINE_PAYMENT_SUBMISSIONS COLUMNS ===\n";

// 1. Check existing columns
$cols = $db->query("DESCRIBE online_payment_submissions")->fetchAll(PDO::FETCH_COLUMN);

if (!in_array('amount_submitted', $cols)) {
    echo "Adding amount_submitted column...\n";
    $db->exec("ALTER TABLE online_payment_submissions ADD COLUMN amount_submitted DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER amount_paid");
    $db->exec("UPDATE online_payment_submissions SET amount_submitted = amount_paid WHERE amount_submitted = 0.00");
}

if (!in_array('account_name', $cols)) {
    echo "Adding account_name column...\n";
    $db->exec("ALTER TABLE online_payment_submissions ADD COLUMN account_name VARCHAR(150) NULL AFTER reference_no");
}

if (!in_array('account_number', $cols)) {
    echo "Adding account_number column...\n";
    $db->exec("ALTER TABLE online_payment_submissions ADD COLUMN account_number VARCHAR(100) NULL AFTER account_name");
}

echo "Columns verified and updated successfully!\n";

$updatedCols = $db->query("DESCRIBE online_payment_submissions")->fetchAll(PDO::FETCH_ASSOC);
print_r($updatedCols);
