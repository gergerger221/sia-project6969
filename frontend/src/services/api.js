// frontend/src/services/api.js

// Determine base API URL: dynamically detect project root folder (e.g. sia-project, sia-project2, etc.)
const getRootFolder = () => {
  const match = window.location.pathname.match(/^\/([^/]+)/);
  return match ? match[1] : 'sia-project';
};

const rootFolder = getRootFolder();
const isDev = window.location.hostname === 'localhost' && window.location.port === '5173';

const API_BASE = isDev
  ? `http://localhost/${rootFolder}/backend/api/index.php`
  : `/${rootFolder}/backend/api/index.php`;

export const BASE_URL = `${window.location.origin}/${rootFolder}/backend/`;


export async function apiRequest(endpoint, options = {}) {
  const token = localStorage.getItem('sia_auth_token');
  const headers = {
    ...(options.headers || {})
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  // If payload is FormData (file uploads), do NOT manually set Content-Type
  if (!(options.body instanceof FormData) && options.body && typeof options.body === 'object') {
    headers['Content-Type'] = 'application/json';
    options.body = JSON.stringify(options.body);
  }

  const url = `${API_BASE}?route=${endpoint}`;
  try {
    const response = await fetch(url, {
      ...options,
      headers
    });

    const data = await response.json();

    if (!response.ok || !data.success) {
      if (response.status === 401) {
        localStorage.removeItem('sia_auth_token');
        localStorage.removeItem('sia_auth_user');
      }
      throw new Error(data.message || 'An error occurred during the request.');
    }

    return data;
  } catch (error) {
    console.error(`API Error on [${endpoint}]:`, error);
    throw error;
  }
}

export const getFileUrl = (filePath) => {
  if (!filePath) return '';
  if (filePath.startsWith('http://') || filePath.startsWith('https://')) return filePath;
  return `${BASE_URL}${filePath.replace(/^\/+/, '')}`;
};

export default {
  // Helpers
  getFileUrl,

  // Auth
  login: (credentials) => apiRequest('auth/login', { method: 'POST', body: credentials }),
  registerApplicant: (data) => apiRequest('auth/register-applicant', { method: 'POST', body: data }),
  getMe: () => apiRequest('auth/me'),
  logout: () => apiRequest('auth/logout', { method: 'POST' }),

  // Admission
  getMyApplication: () => apiRequest('admission/my-application'),
  updateApplication: (data) => apiRequest('admission/update', { method: 'POST', body: data }),
  uploadDocument: (formData) => apiRequest('admission/upload-document', { method: 'POST', body: formData }),
  deleteDocument: (documentId) => apiRequest('admission/delete-document', { method: 'POST', body: { document_id: documentId } }),
  submitApplication: () => apiRequest('admission/submit', { method: 'POST' }),
  getAcademicOptions: () => apiRequest('admission/academic-options'),
  checkoutPayment: (data) => apiRequest('admission/checkout-payment', { method: 'POST', body: data }),
  switchPaymentMode: (data) => apiRequest('admission/switch-payment-mode', { method: 'POST', body: data }),

  // Registrar
  getApplications: (params = '') => apiRequest(`registrar/applications${params ? '&' + params : ''}`),
  getApplicationDetails: (id) => apiRequest(`registrar/application-details&id=${id}`),
  verifyDocument: (data) => apiRequest('registrar/verify-document', { method: 'POST', body: data }),
  approveAndQueue: (data) => apiRequest('registrar/approve-and-queue', { method: 'POST', body: data }),
  undoApproval: (applicationId) => apiRequest('registrar/undo-approval', { method: 'POST', body: { application_id: applicationId } }),
  getEnrollmentQueue: (params = '') => apiRequest(`registrar/queue${params ? '&' + params : ''}`),

  // Treasury
  getAssessments: (params = '') => apiRequest(`treasury/assessments${params ? '&' + params : ''}`),
  getAssessmentDetails: (id) => apiRequest(`treasury/assessment-details&id=${id}`),
  processPayment: (data) => apiRequest('treasury/process-payment', { method: 'POST', body: data }),
  getOnlinePaymentVerifications: (params = '') => apiRequest(`treasury/online-payments${params ? '&' + params : ''}`),
  verifyOnlinePayment: (data) => apiRequest('treasury/verify-online-payment', { method: 'POST', body: data }),
  getFeeStructures: () => apiRequest('treasury/fee-structures'),

  // Coordinator & Sectioning
  getCurriculum: () => apiRequest('coordinator/curriculum'),
  toggleCurriculumLock: (syId) => apiRequest('coordinator/toggle-curriculum-lock', { method: 'POST', body: { school_year_id: syId } }),
  saveSubject: (data) => apiRequest('coordinator/save-subject', { method: 'POST', body: data }),
  deleteSubject: (id) => apiRequest('coordinator/delete-subject', { method: 'POST', body: { id } }),
  saveStrand: (data) => apiRequest('coordinator/save-strand', { method: 'POST', body: data }),
  toggleStrandStatus: (data) => apiRequest('coordinator/toggle-strand-status', { method: 'POST', body: data }),
  deleteStrand: (id) => apiRequest('coordinator/delete-strand', { method: 'POST', body: { id } }),
  getSections: () => apiRequest('coordinator/sections'),
  saveSection: (data) => apiRequest('coordinator/save-section', { method: 'POST', body: data }),
  getSectionStudents: (sectionId) => apiRequest(`coordinator/section-students&section_id=${sectionId}`),
  transferStudentSection: (data) => apiRequest('coordinator/transfer-section', { method: 'POST', body: data }),

  // Records & Archives
  getStudentRecords: (params = '') => apiRequest(`records/students${params ? '&' + params : ''}`),
  getStudentTranscript: (studentId) => apiRequest(`records/transcript&student_id=${studentId}`),
  getDocumentRequests: () => apiRequest('records/document-requests'),
  saveDocumentRequest: (data) => apiRequest('records/save-document-request', { method: 'POST', body: data }),
  updateRequestStatus: (data) => apiRequest('records/update-request-status', { method: 'POST', body: data }),
  getSchoolForm1: (sectionId) => apiRequest(`records/school-form-1&section_id=${sectionId}`),
  getSchoolForm5: (sectionId) => apiRequest(`records/school-form-5&section_id=${sectionId}`),
  getHonorRoll: (params = '') => apiRequest(`records/honor-roll${params ? '&' + params : ''}`),
  updateTransfereeF137: (data) => apiRequest('records/update-transferee-f137', { method: 'POST', body: data }),

  // Student
  getStudentDashboard: () => apiRequest('student/dashboard'),

  // Scheduler & School Events
  getSectionSchedule: (sectionId, semester = '') => apiRequest(`schedules/section&section_id=${sectionId}${semester ? '&semester=' + encodeURIComponent(semester) : ''}`),
  saveSchedule: (data) => apiRequest('schedules/save', { method: 'POST', body: data }),
  deleteSchedule: (scheduleId) => apiRequest('schedules/delete', { method: 'POST', body: { id: scheduleId } }),
  getEvents: (params = '') => apiRequest(`events/list${params ? '&' + params : ''}`),
  saveEvent: (data) => apiRequest('events/save', { method: 'POST', body: data }),
  deleteEvent: (eventId) => apiRequest('events/delete', { method: 'POST', body: { id: eventId } }),

  // Admin
  getDashboardStats: () => apiRequest('admin/stats'),
  getSchoolYears: () => apiRequest('admin/school-years'),
  toggleSchoolYearLock: (syId) => apiRequest('admin/toggle-school-year-lock', { method: 'POST', body: { school_year_id: syId } }),
  toggleAdminCurriculumLock: (syId) => apiRequest('admin/toggle-curriculum-lock', { method: 'POST', body: { school_year_id: syId } }),
  saveSchoolYear: (data) => apiRequest('admin/save-school-year', { method: 'POST', body: data }),
  setActiveSchoolYear: (syId) => apiRequest('admin/set-active-school-year', { method: 'POST', body: { school_year_id: syId } }),
  getUsers: (role = '') => apiRequest(`admin/users${role ? '&role=' + role : ''}`),
  saveUser: (data) => apiRequest('admin/save-user', { method: 'POST', body: data }),

  // SMTP Testing Simulator
  getSmtpConfig: () => apiRequest('auth/smtp-config'),
  testSmtp: (data) => apiRequest('auth/test-smtp', { method: 'POST', body: data })
};
