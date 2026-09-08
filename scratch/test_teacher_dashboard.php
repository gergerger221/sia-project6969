<?php
require_once __DIR__ . '/../backend/config/Database.php';
require_once __DIR__ . '/../backend/config/Response.php';
require_once __DIR__ . '/../backend/helpers/Auth.php';

$token = \App\Helpers\Auth::generateToken(6);
$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;

require_once __DIR__ . '/../backend/controllers/TeacherController.php';

$controller = new \App\Controllers\TeacherController();
echo "Testing getDashboard():\n";
ob_start();
$controller->getDashboard();
$output = ob_get_clean();

$res = json_decode($output, true);
if ($res && $res['success']) {
    echo "SUCCESS: Dashboard loaded!\n";
    echo "Workload 1st Sem: " . $res['data']['stats']['workload_1st_sem_hours'] . " hrs/week\n";
    echo "Workload 2nd Sem: " . $res['data']['stats']['workload_2nd_sem_hours'] . " hrs/week\n";
    echo "DepEd Max Hours: " . $res['data']['stats']['max_deped_hours'] . " hrs/week\n";
    echo "Advisory Section: " . ($res['data']['stats']['advisory_section']['section_name'] ?? 'None') . " (ID: " . ($res['data']['stats']['advisory_section']['id'] ?? 0) . ")\n";
} else {
    echo "FAILED:\n" . $output . "\n";
}

echo "\nTesting getAdvisorySection():\n";
ob_start();
$controller->getAdvisorySection();
$advOutput = ob_get_clean();

$advRes = json_decode($advOutput, true);
if ($advRes && $advRes['success']) {
    echo "SUCCESS: Advisory section loaded!\n";
    echo "Section: " . ($advRes['data']['section']['name'] ?? 'N/A') . "\n";
    echo "Total Learners: " . $advRes['data']['total_learners'] . "\n";
    if (!empty($advRes['data']['learners'])) {
        $first = $advRes['data']['learners'][0];
        echo "Learner 1: {$first['full_name']} | Status: {$first['academic_status']} | Failing: {$first['failing_subjects_count']}\n";
    }
} else {
    echo "FAILED:\n" . $advOutput . "\n";
}
