<?php
// backend/api/index.php
declare(strict_types=1);

// Set header defaults
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Vendor autoloader for PHPMailer and 3rd party packages
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Autoloader for App namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Config\Response;
use App\Controllers\AuthController;
use App\Controllers\AdmissionController;
use App\Controllers\RegistrarController;
use App\Controllers\CoordinatorController;
use App\Controllers\TreasuryController;
use App\Controllers\RecordsController;
use App\Controllers\StudentController;
use App\Controllers\AdminController;
use App\Controllers\ScheduleController;

// Extract action / route
$route = $_GET['route'] ?? $_GET['action'] ?? '';
if (!$route) {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    
    // Remove query string
    $path = explode('?', $requestUri)[0];
    
    // Check if path contains /api/
    if (strpos($path, '/api/') !== false) {
        $parts = explode('/api/', $path);
        $route = trim($parts[1] ?? '', '/');
    }
}

// Clean route if embedded query string exists (e.g. "registrar/queue?status=active" or "registrar/queue&status=active")
if (strpos($route, '?') !== false) {
    [$cleanRoute, $qs] = explode('?', $route, 2);
    $route = $cleanRoute;
    parse_str($qs, $extraParams);
    $_GET = array_merge($_GET, $extraParams);
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($route) {
        // --- AUTHENTICATION ---
        case 'auth/login':
            (new AuthController())->login();
            break;
        case 'auth/register-applicant':
            (new AuthController())->registerApplicant();
            break;
        case 'auth/me':
            (new AuthController())->me();
            break;
        case 'auth/logout':
            (new AuthController())->logout();
            break;

        // --- ADMISSION PORTAL ---
        case 'admission/my-application':
            (new AdmissionController())->getMyApplication();
            break;
        case 'admission/update':
            (new AdmissionController())->updateApplication();
            break;
        case 'admission/upload-document':
            (new AdmissionController())->uploadDocument();
            break;
        case 'admission/delete-document':
            (new AdmissionController())->deleteDocument();
            break;
        case 'admission/submit':
            (new AdmissionController())->submitApplication();
            break;
        case 'admission/academic-options':
            (new AdmissionController())->getAcademicOptions();
            break;
        case 'admission/checkout-payment':
            (new AdmissionController())->checkoutPayment();
            break;
        case 'admission/switch-payment-mode':
            (new AdmissionController())->switchPaymentMode();
            break;

        // --- REGISTRAR ---
        case 'registrar/applications':
            (new RegistrarController())->getApplications();
            break;
        case 'registrar/application-details':
            $id = (int)($_GET['id'] ?? 0);
            (new RegistrarController())->getApplicationDetails($id);
            break;
        case 'registrar/verify-document':
            (new RegistrarController())->verifyDocument();
            break;
        case 'registrar/approve-and-queue':
            (new RegistrarController())->approveAndQueue();
            break;
        case 'registrar/undo-approval':
            (new RegistrarController())->undoApproval();
            break;
        case 'registrar/queue':
            (new RegistrarController())->getQueue();
            break;

        // --- TREASURY & BILLING ---
        case 'treasury/assessments':
            (new TreasuryController())->getAssessments();
            break;
        case 'treasury/assessment-details':
            $id = (int)($_GET['id'] ?? 0);
            (new TreasuryController())->getAssessmentDetails($id);
            break;
        case 'treasury/process-payment':
            (new TreasuryController())->processPayment();
            break;
        case 'treasury/online-payments':
            (new TreasuryController())->getOnlinePaymentVerifications();
            break;
        case 'treasury/verify-online-payment':
            (new TreasuryController())->verifyOnlinePayment();
            break;
        case 'treasury/fee-structures':
            (new TreasuryController())->getFeeStructures();
            break;

        // --- ACADEMIC COORDINATOR ---
        case 'coordinator/curriculum':
            (new CoordinatorController())->getCurriculum();
            break;
        case 'coordinator/toggle-curriculum-lock':
            (new CoordinatorController())->toggleCurriculumLock();
            break;
        case 'coordinator/save-subject':
            (new CoordinatorController())->saveSubject();
            break;
        case 'coordinator/delete-subject':
            (new CoordinatorController())->deleteSubject();
            break;
        case 'coordinator/save-strand':
            (new CoordinatorController())->saveStrand();
            break;
        case 'coordinator/toggle-strand-status':
            (new CoordinatorController())->toggleStrandStatus();
            break;
        case 'coordinator/delete-strand':
            (new CoordinatorController())->deleteStrand();
            break;
        case 'coordinator/sections':
            (new CoordinatorController())->getSections();
            break;
        case 'coordinator/save-section':
            (new CoordinatorController())->saveSection();
            break;
        case 'coordinator/section-students':
            $secId = (int)($_GET['section_id'] ?? 0);
            (new CoordinatorController())->getSectionStudents($secId);
            break;
        case 'coordinator/transfer-section':
            (new CoordinatorController())->transferStudentSection();
            break;

        // --- SCHOOL RECORDS & ARCHIVES ---
        case 'records/students':
            (new RecordsController())->getStudentRecords();
            break;
        case 'records/transcript':
            $studentId = (int)($_GET['student_id'] ?? 0);
            (new RecordsController())->getStudentTranscript($studentId);
            break;
        case 'records/document-requests':
            (new RecordsController())->getDocumentRequests();
            break;
        case 'records/save-document-request':
            (new RecordsController())->saveDocumentRequest();
            break;
        case 'records/update-request-status':
            (new RecordsController())->updateRequestStatus();
            break;
        case 'records/school-form-1':
            (new RecordsController())->getSchoolForm1();
            break;
        case 'records/school-form-5':
            (new RecordsController())->getSchoolForm5();
            break;
        case 'records/honor-roll':
            (new RecordsController())->getHonorRoll();
            break;
        case 'records/update-transferee-f137':
            (new RecordsController())->updateTransfereeF137Status();
            break;

        // --- STUDENT PORTAL ---
        case 'student/dashboard':
            (new StudentController())->getDashboard();
            break;

        // --- MASTER SCHEDULER & EVENT CALENDAR ---
        case 'schedules/section':
            (new ScheduleController())->getSectionSchedule();
            break;
        case 'schedules/save':
            (new ScheduleController())->saveSchedule();
            break;
        case 'schedules/delete':
            (new ScheduleController())->deleteSchedule();
            break;
        case 'events/list':
            (new ScheduleController())->getEvents();
            break;
        case 'events/save':
            (new ScheduleController())->saveEvent();
            break;
        case 'events/delete':
            (new ScheduleController())->deleteEvent();
            break;

        // --- ADMIN ---
        case 'admin/stats':
            (new AdminController())->getDashboardStats();
            break;
        case 'admin/school-years':
            (new AdminController())->getSchoolYears();
            break;
        case 'admin/toggle-school-year-lock':
            (new AdminController())->toggleSchoolYearLock();
            break;
        case 'admin/toggle-curriculum-lock':
            (new AdminController())->toggleCurriculumLock();
            break;
        case 'admin/save-school-year':
            (new AdminController())->saveSchoolYear();
            break;
        case 'admin/set-active-school-year':
            (new AdminController())->setActiveSchoolYear();
            break;
        case 'admin/users':
            (new AdminController())->getUsers();
            break;
        case 'admin/save-user':
            (new AdminController())->saveUser();
            break;

        default:
            Response::error("Endpoint not found: {$route}", 404);
            break;
    }
} catch (\Throwable $e) {
    Response::error("Internal Server Error: " . $e->getMessage(), 500);
}
