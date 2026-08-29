<?php
// scratch/patch_online_subs.php
$pdo = new PDO("mysql:host=localhost;dbname=sia_highschool_db;charset=utf8mb4", 'root', '');

// Add amount_paid and payment_date if not exists
$cols = $pdo->query("DESCRIBE online_payment_submissions")->fetchAll(PDO::FETCH_COLUMN);

if (!in_array('amount_paid', $cols)) {
    echo "Adding amount_paid column...\n";
    $pdo->exec("ALTER TABLE online_payment_submissions ADD COLUMN amount_paid DECIMAL(10,2) NULL AFTER payment_channel");
}

if (!in_array('payment_date', $cols)) {
    echo "Adding payment_date column...\n";
    $pdo->exec("ALTER TABLE online_payment_submissions ADD COLUMN payment_date DATE NULL AFTER amount_submitted");
}

// Populate amount_paid with amount_submitted
$pdo->exec("UPDATE online_payment_submissions SET amount_paid = amount_submitted WHERE amount_paid IS NULL AND amount_submitted IS NOT NULL");

echo "Columns verified and patched successfully!\n";
