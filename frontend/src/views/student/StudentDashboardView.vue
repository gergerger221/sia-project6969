<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- TOP WELCOME & SECTION BADGE HEADER -->
    <div class="no-print bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-2xs flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
      <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold uppercase tracking-wider mb-2.5">
          <Sparkles class="w-3.5 h-3.5 text-emerald-600" />
          <span>Student Portal • {{ dashboardData.enrollment?.school_year_name || 'SY 2026-2027' }}</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
          Welcome, {{ studentDisplayName }}!
        </h1>
        <p class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-2">
          <span>Student ID: <strong class="text-emerald-700 font-mono font-bold">{{ studentDisplayId }}</strong></span>
          <span class="text-slate-300">•</span>
          <span>LRN: <strong class="text-slate-700 font-mono">{{ studentDisplayLrn }}</strong></span>
          <span v-if="dashboardData.enrollment?.enrollment_no" class="text-slate-300">•</span>
          <span v-if="dashboardData.enrollment?.enrollment_no">Enr Ref: <strong class="text-slate-600 font-mono">{{ dashboardData.enrollment.enrollment_no }}</strong></span>
        </p>
      </div>

      <!-- ASSIGNED SECTION & CLASSROOM BADGE -->
      <div class="text-left md:text-right bg-slate-50 p-4 sm:p-4.5 rounded-xl border border-slate-200 min-w-[240px] shrink-0">
        <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-0.5 flex items-center md:justify-end space-x-1">
          <Layers class="w-3.5 h-3.5 text-emerald-600" />
          <span>Assigned Class Section</span>
        </div>
        <div class="text-base font-bold text-slate-900">
          {{ dashboardData.enrollment?.section_name || 'Class Section Pending' }}
        </div>
        <div class="text-xs text-emerald-700 font-semibold mt-0.5">
          {{ dashboardData.enrollment?.grade_level_name || 'Grade Level' }}
          <span v-if="dashboardData.enrollment?.strand_code"> • {{ dashboardData.enrollment.strand_code }}</span>
        </div>
        <div class="text-[11px] text-slate-500 mt-1 flex items-center md:justify-end space-x-1">
          <MapPin class="w-3.5 h-3.5 text-slate-400 shrink-0" />
          <span>{{ dashboardData.enrollment?.section_room || 'Designated Homeroom' }}</span>
        </div>
      </div>
    </div>

    <!-- MAIN DASHBOARD CONTENT GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- LEFT COLUMN: ENROLLED SUBJECTS & TIMETABLE (2 Cols on lg) -->
      <div v-show="activeTab === 'all' || activeTab === 'schedule'" class="space-y-6" :class="activeTab === 'schedule' ? 'lg:col-span-3' : 'lg:col-span-2'">
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-5">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div>
              <h2 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                <BookOpen class="w-4 h-4 text-emerald-600" />
                <span>Class Schedule & Enrolled Learning Areas</span>
              </h2>
              <p class="text-xs text-slate-500 mt-0.5">Official class schedule and teacher assignments for the semester</p>
            </div>
            <div class="flex items-center space-x-2">
              <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                {{ currentSemesterSubjects.length }} Subjects
              </span>
              <!-- Semester filter if SHS -->
              <select 
                v-if="isSHSStudent" 
                v-model="selectedSemesterFilter" 
                class="px-2.5 py-1 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700"
              >
                <option value="1st Semester">1st Semester</option>
                <option value="2nd Semester">2nd Semester</option>
                <option value="All">All Semesters</option>
              </select>
            </div>
          </div>

          <!-- SUBJECT SCHEDULE CARDS -->
          <div class="space-y-3.5">
            <div 
              v-for="sub in currentSemesterSubjects" 
              :key="sub.enrollment_subject_id || sub.subject_id"
              class="p-4 sm:p-5 rounded-2xl border border-slate-200/90 bg-slate-50/50 hover:bg-slate-50/90 hover:border-emerald-300 transition shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4 text-xs group"
            >
              <div class="space-y-1 min-w-0 flex-1">
                <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                  <span class="font-extrabold font-mono text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded text-[11px]">
                    {{ sub.subject_code }}
                  </span>
                  <span class="text-[10px] text-slate-500 font-bold uppercase bg-slate-200/70 px-2 py-0.5 rounded">
                    {{ sub.subject_category || 'Core' }}
                  </span>
                  <span v-if="sub.semester" class="text-[10px] text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded border border-blue-200/60">
                    {{ sub.semester }}
                  </span>
                  <span class="text-slate-400 text-[11px] font-mono">{{ sub.units || '1.0' }} Units</span>
                </div>

                <div class="font-bold text-sm text-slate-900 leading-snug pt-0.5 group-hover:text-emerald-900 transition">
                  {{ sub.subject_title }}
                </div>

                <!-- Assigned Teacher -->
                <div class="text-[11px] text-slate-600 flex items-center space-x-1.5 pt-0.5">
                  <User class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                  <span>Teacher: <strong class="text-slate-800 font-medium">{{ sub.teacher_first }} {{ sub.teacher_last }}</strong></span>
                </div>
              </div>

              <!-- Schedule Time Slot & Room Badge -->
              <div class="text-left md:text-right bg-white p-3 rounded-xl border border-slate-200/80 shrink-0 shadow-2xs space-y-0.5 min-w-[170px]">
                <div class="font-extrabold text-slate-900 flex items-center md:justify-end space-x-1 text-xs">
                  <Clock class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                  <span>{{ sub.day_of_week || 'Mon-Fri' }}</span>
                </div>
                <div class="text-[11px] text-slate-600 font-mono font-medium">
                  {{ formatTime(sub.time_start) }} - {{ formatTime(sub.time_end) }}
                </div>
                <div class="text-[10px] text-slate-500 flex items-center md:justify-end space-x-1 pt-0.5">
                  <MapPin class="w-3 h-3 text-slate-400 shrink-0" />
                  <span class="truncate">{{ sub.room || dashboardData.enrollment?.section_room || 'Homeroom' }}</span>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="currentSemesterSubjects.length === 0" class="text-center py-10 text-slate-400 text-xs bg-slate-50 rounded-2xl border border-dashed border-slate-200">
              <BookOpen class="w-8 h-8 text-slate-300 mx-auto mb-2 stroke-[1.5]" />
              <p class="font-medium text-slate-600">No enrolled subjects found for this selection.</p>
              <p class="text-[11px] text-slate-400 mt-0.5">Please consult the Registrar or Section Adviser if your schedule is not listed.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT COLUMN: FINANCIAL SOA, EVENTS CALENDAR & DRS (1 Col on lg) -->
      <div 
        class="space-y-6" 
        :class="activeTab === 'schedule' ? 'hidden' : (activeTab !== 'all' ? 'lg:col-span-3' : 'lg:col-span-1')"
      >
        <!-- STATEMENT OF ACCOUNT (SOA) -->
        <div v-show="activeTab === 'all' || activeTab === 'account'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm text-xs space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="text-base font-bold text-slate-900">Statement of Account</h2>
            <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase" :class="getPaymentBadge(dashboardData.enrollment?.payment_status)">
              {{ dashboardData.enrollment?.payment_status || 'Assessed' }}
            </span>
          </div>

          <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/90 font-mono space-y-2 text-[11px]">
            <div class="flex justify-between text-slate-600">
              <span>Gross Tuition & Fees:</span>
              <span class="font-bold text-slate-900">₱{{ Number(dashboardData.enrollment?.gross_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
            </div>
            <div v-if="Number(dashboardData.enrollment?.voucher_discount) > 0" class="flex justify-between text-emerald-700 font-bold">
              <span>Voucher Subsidy:</span>
              <span>- ₱{{ Number(dashboardData.enrollment?.voucher_discount).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
            </div>
            <div class="flex justify-between font-bold text-slate-900 border-t border-slate-200 pt-1.5 text-xs">
              <span>Total Net Payable:</span>
              <span>₱{{ Number(dashboardData.enrollment?.net_payable || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
            </div>
            <div class="flex justify-between text-emerald-600 font-bold">
              <span>Total Paid to Date:</span>
              <span>₱{{ Number(dashboardData.enrollment?.total_paid || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
            </div>
            <div class="flex justify-between text-rose-600 font-bold border-t border-slate-200 pt-1.5">
              <span>Remaining Balance:</span>
              <span>₱{{ Number(dashboardData.enrollment?.remaining_balance || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
            </div>
          </div>

          <!-- Official Receipts History -->
          <div v-if="dashboardData.payments && dashboardData.payments.length > 0" class="pt-2">
            <h3 class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider mb-2">Issued Receipts</h3>
            <div class="space-y-2">
              <div 
                v-for="p in dashboardData.payments" 
                :key="p.id"
                class="p-2.5 rounded-xl border border-slate-200 bg-white flex items-center justify-between text-[11px] font-mono shadow-2xs"
              >
                <div>
                  <strong class="text-slate-800">{{ p.or_number }}</strong>
                  <div class="text-[10px] text-slate-400">{{ p.payment_date }} • {{ p.payment_method }}</div>
                </div>
                <strong class="text-emerald-700">₱{{ Number(p.amount_paid).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</strong>
              </div>
            </div>
          </div>
        </div>

        <!-- SCHOOL EVENTS & ACADEMIC CALENDAR -->
        <div v-show="activeTab === 'all' || activeTab === 'events'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm text-xs space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="text-base font-bold text-slate-900 flex items-center space-x-1.5">
              <Calendar class="w-4 h-4 text-purple-700" />
              <span>School Events & Calendar</span>
            </h2>
            <span class="px-2.5 py-0.5 rounded bg-purple-50 text-purple-800 font-mono text-[10px] font-extrabold">
              {{ dashboardData.enrollment?.school_year_name || 'SY 2026-2027' }}
            </span>
          </div>

          <div class="space-y-3">
            <div 
              v-for="ev in eventsList" 
              :key="ev.id"
              class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/70 space-y-1.5 hover:border-purple-300 transition"
            >
              <div class="flex items-center justify-between text-[10px] font-bold">
                <span class="px-2 py-0.5 rounded uppercase" :class="getEventBadge(ev.event_category)">
                  {{ ev.event_category }}
                </span>
                <span class="text-slate-500 font-mono font-bold">{{ formatEvDateRange(ev.start_date, ev.end_date) }}</span>
              </div>
              <h4 class="font-bold text-slate-900 text-xs leading-snug">{{ ev.title }}</h4>
              <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ ev.description }}</p>
              <div v-if="ev.location" class="text-[10px] text-slate-500 font-medium flex items-center space-x-1 pt-0.5">
                <MapPin class="w-3 h-3 text-slate-400 shrink-0" />
                <span class="truncate">{{ ev.location }}</span>
              </div>
            </div>

            <div v-if="eventsList.length === 0" class="text-center py-6 text-slate-400 text-xs">
              No school events posted for this term.
            </div>
          </div>
        </div>

        <!-- OFFICIAL DOCUMENT REQUESTS (DRS) -->
        <div v-show="activeTab === 'all' || activeTab === 'records'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm text-xs space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="text-base font-bold text-slate-900 flex items-center space-x-1.5">
              <FileText class="w-4 h-4 text-blue-900" />
              <span>Official Document Requests</span>
            </h2>
            <button 
              @click="showStudentDocModal = true"
              class="px-3.5 py-1.5 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-semibold text-[11px] transition shadow-xs cursor-pointer"
            >
              + Request Document
            </button>
          </div>

          <!-- Requests List -->
          <div class="space-y-2.5">
            <div 
              v-for="dr in myDocRequests" 
              :key="dr.id" 
              class="p-3 rounded-2xl border border-slate-200 bg-slate-50/70 flex items-center justify-between text-xs hover:bg-slate-50 transition"
            >
              <div>
                <div class="font-bold text-slate-900">{{ dr.document_type }}</div>
                <div class="text-[10px] text-slate-400 font-mono">Control #: {{ dr.control_number || 'Pending' }} • {{ dr.copies }} Copy/Copies</div>
                <div v-if="dr.purpose" class="text-[10px] text-slate-500 italic mt-0.5">Purpose: {{ dr.purpose }}</div>
              </div>
              <span class="px-2.5 py-1 rounded-full text-[10px] font-bold" :class="getDRSBadge(dr.status)">
                {{ dr.status }}
              </span>
            </div>

            <div v-if="myDocRequests.length === 0" class="text-center py-6 text-slate-400 text-xs">
              You have no active document requests.
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL: SUBMIT NEW DOCUMENT REQUEST -->
    <div v-if="showStudentDocModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div>
            <h3 class="text-base font-extrabold text-slate-900">Request Official Document</h3>
            <p class="text-[11px] text-slate-500">Submitted directly to the School Records Custodian.</p>
          </div>
          <button @click="showStudentDocModal = false" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
        </div>

        <form @submit.prevent="submitStudentDocRequest" class="space-y-3">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Document Type *</label>
            <select v-model="studentDocForm.document_type" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white" required>
              <option value="Certificate of Enrollment">Certificate of Enrollment (COE)</option>
              <option value="Good Moral Character">Certificate of Good Moral Character</option>
              <option value="Certified True Copy of SF9 / Form 138">Certified True Copy of SF9 (Report Card)</option>
              <option value="Certificate of Academic Ranking">Certificate of Academic Ranking</option>
            </select>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Purpose / Intended Use *</label>
            <input 
              v-model="studentDocForm.purpose" 
              type="text" 
              placeholder="e.g. Scholarship Application / Passport Renewal / Transfer" 
              class="w-full px-3 py-2 rounded-xl border border-slate-300"
              required 
            />
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Number of Copies *</label>
            <input 
              v-model.number="studentDocForm.copies" 
              type="number" 
              min="1" 
              max="5" 
              class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono"
              required 
            />
          </div>

          <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
            <button type="button" @click="showStudentDocModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold cursor-pointer">Cancel</button>
            <button type="submit" class="px-5 py-2.5 rounded-xl font-semibold bg-blue-900 hover:bg-blue-800 text-white shadow-xs transition cursor-pointer">
              Submit Request
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { Calendar, MapPin, FileText, Clock, User, BookOpen, Layers, Sparkles, CreditCard } from 'lucide-vue-next';
import api from '../../services/api';

const route = useRoute();
const activeTab = ref('all');

watch(() => route.query.tab, (tab) => {
  if (tab && ['all', 'schedule', 'account', 'events', 'records'].includes(tab)) {
    activeTab.value = tab;
  }
}, { immediate: true });

const dashboardData = ref({
  user: null,
  enrollment: null,
  subjects: [],
  payments: [],
  events: []
});

const eventsList = ref([]);
const myDocRequests = ref([]);
const showStudentDocModal = ref(false);
const selectedSemesterFilter = ref('1st Semester');
const studentDocForm = ref({
  document_type: 'Certificate of Enrollment',
  purpose: '',
  copies: 1
});

const studentDisplayName = computed(() => {
  const u = dashboardData.value.user;
  const e = dashboardData.value.enrollment;
  if (u?.first_name || u?.last_name) {
    return `${u.first_name || ''} ${u.last_name || ''}`.trim();
  }
  if (e?.student_first_name || e?.student_last_name) {
    return `${e.student_first_name || ''} ${e.student_last_name || ''}`.trim();
  }
  return 'Student';
});

const studentDisplayId = computed(() => {
  return dashboardData.value.user?.student_id || dashboardData.value.enrollment?.student_no || dashboardData.value.enrollment?.official_student_no || 'Pending';
});

const studentDisplayLrn = computed(() => {
  return dashboardData.value.enrollment?.lrn || 'N/A';
});

const isSHSStudent = computed(() => {
  const gl = dashboardData.value.enrollment?.grade_level_id;
  const cat = dashboardData.value.enrollment?.grade_category;
  return cat === 'SHS' || gl >= 5;
});

const currentSemesterSubjects = computed(() => {
  const all = dashboardData.value.subjects || [];
  if (!isSHSStudent.value || selectedSemesterFilter.value === 'All') {
    return all;
  }
  return all.filter(s => !s.semester || s.semester === selectedSemesterFilter.value);
});

const formatTime = (t) => {
  if (!t) return '8:00 AM';
  const parts = t.split(':');
  if (parts.length < 2) return t;
  let h = parseInt(parts[0], 10);
  const m = parts[1];
  const ampm = h >= 12 ? 'PM' : 'AM';
  h = h % 12 || 12;
  return `${h}:${m} ${ampm}`;
};

const formatEvDateRange = (s, e) => {
  if (!s) return '';
  const d1 = new Date(s).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
  if (!e || e === s) return d1;
  const d2 = new Date(e).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
  return `${d1} - ${d2}`;
};

const getEventBadge = (cat) => {
  if (cat === 'Academic') return 'bg-purple-100 text-purple-800 border border-purple-200';
  if (cat === 'Examination') return 'bg-rose-100 text-rose-800 border border-rose-200';
  if (cat === 'Holiday') return 'bg-amber-100 text-amber-800 border border-amber-200';
  if (cat === 'Administrative') return 'bg-blue-100 text-blue-800 border border-blue-200';
  return 'bg-emerald-100 text-emerald-800 border border-emerald-200';
};

const getPaymentBadge = (status) => {
  if (status === 'Fully Paid') return 'bg-emerald-100 text-emerald-800 border border-emerald-200';
  if (status === 'Partially Paid') return 'bg-blue-100 text-blue-800 border border-blue-200';
  return 'bg-amber-100 text-amber-800 border border-amber-200';
};

const getDRSBadge = (status) => {
  switch (status) {
    case 'Released': return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
    case 'Ready for Pickup': return 'bg-cyan-50 text-cyan-700 border border-cyan-200';
    case 'Processing': return 'bg-blue-50 text-blue-700 border border-blue-200';
    case 'Pending': return 'bg-amber-50 text-amber-700 border border-amber-200';
    default: return 'bg-slate-100 text-slate-700';
  }
};

const loadDashboard = async () => {
  try {
    const res = await api.getStudentDashboard();
    dashboardData.value = res.data;
    if (res.data?.events && res.data.events.length > 0) {
      eventsList.value = res.data.events;
    }
  } catch (err) {
    console.error('Failed to load student dashboard:', err);
  }

  try {
    const evRes = await api.getEvents();
    if (evRes.data?.events && evRes.data.events.length > 0) {
      eventsList.value = evRes.data.events;
    }
  } catch (err) {
    console.error('Failed to load events:', err);
  }

  try {
    const docRes = await api.getDocumentRequests();
    myDocRequests.value = docRes.data || [];
  } catch (err) {
    console.error('Failed to load document requests:', err);
  }
};

const submitStudentDocRequest = async () => {
  try {
    await api.saveDocumentRequest(studentDocForm.value);
    showStudentDocModal.value = false;
    studentDocForm.value.purpose = '';
    const docRes = await api.getDocumentRequests();
    myDocRequests.value = docRes.data || [];
    alert('Document request submitted to School Records Custodian!');
  } catch (err) {
    alert(err.message || 'Failed to submit document request.');
  }
};

onMounted(() => {
  loadDashboard();
});
</script>

