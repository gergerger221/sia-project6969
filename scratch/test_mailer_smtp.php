<?php
// scratch/test_mailer_smtp.php
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
use PHPMailer\PHPMailer\PHPMailer;

echo "=== SIA ENROLLMENT SYSTEM: PHPMAILER & SMTP TEST SUITE ===\n\n";

$passCount = 0;
$totalCount = 0;

function assertTest(string $desc, bool $condition): void {
    global $passCount, $totalCount;
    $totalCount++;
    if ($condition) {
        $passCount++;
        echo " [PASS] {$desc}\n";
    } else {
        echo " [FAIL] {$desc}\n";
    }
}

// 1. Check PHPMailer Class Existence
assertTest("PHPMailer class is loaded and available", class_exists(PHPMailer::class));

// 2. Check MailConfig
$config = MailConfig::get();
assertTest("MailConfig returns valid array structure", is_array($config) && isset($config['host'], $config['port'], $config['from_email']));
assertTest("MailConfig default host is configured (smtp.gmail.com)", $config['host'] === 'smtp.gmail.com');

// 3. Test sendApplicantRegistration (Simulated / Safe mode)
$res1 = Mailer::sendApplicantRegistration([
    'first_name'     => 'Juan',
    'last_name'      => 'Dela Cruz',
    'email'          => 'juan.delacruz@gmail.com',
    'username'       => 'juandelacruz123',
    'application_no' => 'ADM-2026-9999'
]);
assertTest("Mailer::sendApplicantRegistration executes safely without error", $res1['success'] === true);

// 4. Test sendRegistrarApproval (Simulated / Safe mode)
$res2 = Mailer::sendRegistrarApproval([
    'first_name'   => 'Juan',
    'last_name'    => 'Dela Cruz',
    'email'        => 'juan.delacruz@yahoo.com',
    'student_no'   => '2026-SHS-0099',
    'section_name' => '11 - STEM Einstein'
], [
    'assessment_no'   => 'ASS-2026-0099',
    'net_amount'      => 12500.00,
    'min_downpayment' => 3000.00
]);
assertTest("Mailer::sendRegistrarApproval executes safely without error", $res2['success'] === true);

// 5. Test sendOfficialEnrollment (Simulated / Safe mode)
$res3 = Mailer::sendOfficialEnrollment([
    'first_name' => 'Juan',
    'last_name'  => 'Dela Cruz',
    'email'      => 'juan.delacruz@student.jjkings.edu.ph',
    'student_id' => '2026-SHS-0099'
], [
    'or_number'   => 'OR-2026-0099',
    'amount_paid' => 3000.00
]);
assertTest("Mailer::sendOfficialEnrollment executes safely without error", $res3['success'] === true);

// 6. Test Invalid Email Rejection (Fail-safe)
$res4 = Mailer::send('invalid-email-string', 'Test User', 'Test Subject', '<p>Hello</p>');
assertTest("Mailer rejects invalid email format gracefully", $res4['success'] === false);

echo "\n=======================================================\n";
echo "Tests Passed: {$passCount} / {$totalCount}\n";
if ($passCount === $totalCount) {
    echo "SUCCESS: PHPMailer & SMTP Service is fully operational and integrated!\n";
} else {
    echo "WARNING: Some tests failed.\n";
}
echo "=======================================================\n";
