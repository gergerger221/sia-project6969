<?php
// scratch/check_online_subs_cols.php
$pdo = new PDO("mysql:host=localhost;dbname=sia_highschool_db;charset=utf8mb4", 'root', '');
$stmt = $pdo->query("DESCRIBE online_payment_submissions");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "=== COLUMNS IN online_payment_submissions ===\n";
foreach ($cols as $c) {
    echo " - {$c['Field']} ({$c['Type']})\n";
}
