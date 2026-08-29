<?php
// scratch/check_scholastic_cols.php
$pdo = new PDO("mysql:host=localhost;dbname=sia_highschool_db;charset=utf8mb4", 'root', '');
$stmt = $pdo->query("DESCRIBE scholastic_records");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "=== COLUMNS IN scholastic_records ===\n";
foreach ($cols as $c) {
    echo " - {$c['Field']} ({$c['Type']})\n";
}
