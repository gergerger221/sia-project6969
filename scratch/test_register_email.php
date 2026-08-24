<?php
// scratch/test_register_email.php
declare(strict_types=1);

require_once __DIR__ . '/../backend/vendor/autoload.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../backend/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Config\Database;
use App\Config\MailConfig;
use App\Helpers\Mailer;

echo "=== TESTING ACTUAL ADMISSION ACCOUNT CREATION EMAIL DISPATCH ===\n";

$testEmail = 'benjoven221@gmail.com';
$randomSuffix = rand(1000, 9999);
$firstName = 'Juan';
$lastName = 'Tester' . $randomSuffix;
$username = 'juantester' . $randomSuffix;
$appNo = 'ADM-2026-' . $randomSuffix;

echo "Simulating Registration for: {$testEmail} (App: {$appNo})...\n";

$res = Mailer::sendApplicantRegistration([
    'first_name'     => $firstName,
    'last_name'      => $lastName,
    'email'          => $testEmail,
    'username'       => $username,
    'application_no' => $appNo
]);

echo "Dispatch Result:\n";
print_r($res);

if (!empty($res['success'])) {
    echo "\n[SUCCESS] Registration email for {$appNo} was successfully sent via live Google SMTP to {$testEmail}!\n";
} else {
    echo "\n[ERROR] Failed to send: " . ($res['message'] ?? 'Unknown error') . "\n";
}
