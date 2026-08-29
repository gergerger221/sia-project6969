<?php
// scratch/list_all_tables_now.php
$pdo = new PDO("mysql:host=localhost;dbname=sia_highschool_db;charset=utf8mb4", 'root', '');
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "=== TABLES IN SIA_HIGHSCHOOL_DB (" . count($tables) . ") ===\n";
foreach ($tables as $t) {
    $c = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    echo sprintf(" - %-35s (%d rows)\n", $t, $c);
}
