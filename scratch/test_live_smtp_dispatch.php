<?php
// scratch/test_live_smtp_dispatch.php
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

use App\Config\MailConfig;
use App\Helpers\Mailer;

echo "=== TESTING LIVE GMAIL SMTP DISPATCH ===\n";
echo "Host: smtp.gmail.com (587 TLS)\n";
echo "Account: ver.smtp221@gmail.com\n\n";

$targetEmail = 'ver.smtp221@gmail.com';

echo "Dispatching Applicant Registration Test Email to {$targetEmail}...\n";

$result = Mailer::sendApplicantRegistration([
    'first_name'     => 'Live Test',
    'last_name'      => 'Applicant',
    'email'          => $targetEmail,
    'username'       => 'livetest_app01',
    'application_no' => 'ADM-2026-LIVE'
]);

echo "\nResult:\n";
print_r($result);

if (!empty($result['success'])) {
    echo "\n[SUCCESS] Live email was accepted by Google SMTP servers and delivered to {$targetEmail}!\n";
} else {
    echo "\n[FAILED] SMTP error: " . ($result['message'] ?? 'Unknown error') . "\n";
}
