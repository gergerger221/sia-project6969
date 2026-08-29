<?php
// scratch/check_db_import.php
declare(strict_types=1);

$sqlPath = 'C:/xampp/htdocs/changes/sia_highschool_complete_database.sql';
if (!file_exists($sqlPath)) {
    echo "SQL file not found at $sqlPath\n";
    exit(1);
}

echo "SQL File size: " . filesize($sqlPath) . " bytes\n";

// Connect to MySQL
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "Connected to MySQL successfully.\n";

    // Read first few lines of the SQL file
    $handle = fopen($sqlPath, 'r');
    $head = '';
    for ($i = 0; $i < 30; $i++) {
        $line = fgets($handle);
        if ($line === false) break;
        $head .= $line;
    }
    fclose($handle);
    echo "SQL Head:\n$head\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
