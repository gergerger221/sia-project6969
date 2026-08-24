<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-950 text-amber-400 border border-amber-500/30 text-xs font-bold uppercase tracking-wider mb-2">
          <span>Super Admin Control Center</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white">System Administration & School Year Control</h1>
        <p class="text-xs text-slate-400 mt-1">Manage user roles, toggle enrollment lock status, and monitor system metrics.</p>
      </div>

      <div class="flex items-center space-x-2">
        <button 
          @click="activeTab = 'stats'"
          :class="activeTab === 'stats' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
          class="px-4 py-2 rounded-xl text-xs transition"
        >
          Overview & Logs
        </button>
        <button 
          @click="activeTab = 'users'"
          :class="activeTab === 'users' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
          class="px-4 py-2 rounded-xl text-xs transition"
        >
          Staff & User Management
        </button>
        <button 
          @click="activeTab = 'school_years'"
          :class="activeTab === 'school_years' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
          class="px-4 py-2 rounded-xl text-xs transition"
        >
          School Year Lock
        </button>
      </div>
    </div>

    <!-- Alert -->
    <div v-if="successMessage" class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500 text-emerald-300 text-xs mb-6 flex items-center justify-between">
      <span>{{ successMessage }}</span>
      <button @click="successMessage = ''" class="font-bold">✕</button>
    </div>

    <!-- TAB 1: OVERVIEW & STATS -->
    <div v-if="activeTab === 'stats'" class="space-y-6">
      <!-- 4 Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
          <span class="text-xs font-bold uppercase text-slate-400">Total Applicants</span>
          <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ stats.total_applicants || 0 }}</div>
          <span class="text-[11px] text-amber-600 font-semibold">{{ stats.pending_review || 0 }} Under Review</span>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
          <span class="text-xs font-bold uppercase text-slate-400">Enrolled Students</span>
          <div class="text-2xl font-extrabold text-emerald-700 mt-1">{{ (stats.enrolled_jhs || 0) + (stats.enrolled_shs || 0) }}</div>
          <span class="text-[11px] text-slate-500 font-medium">JHS: {{ stats.enrolled_jhs || 0 }} • SHS: {{ stats.enrolled_shs || 0 }}</span>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
          <span class="text-xs font-bold uppercase text-slate-400">Tuition Collections</span>
          <div class="text-2xl font-extrabold text-slate-900 mt-1">
            ₱{{ Number(stats.total_revenue || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}
          </div>
          <span class="text-[11px] text-emerald-600 font-semibold">Treasury Verified</span>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
          <span class="text-xs font-bold uppercase text-slate-400">Active Staff</span>
          <div class="text-2xl font-extrabold text-purple-700 mt-1">{{ stats.total_staff || 0 }}</div>
          <span class="text-[11px] text-slate-500 font-medium">Registrar, Treasury, Coordinator</span>
        </div>
      </div>

      <!-- Audit Logs Trail -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
        <h2 class="text-base font-bold text-slate-800 mb-4">System Audit Trail & Security Logs</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-xs text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
                <th class="p-3">Timestamp</th>
                <th class="p-3">User</th>
                <th class="p-3">Action</th>
                <th class="p-3">Activity Details</th>
                <th class="p-3 font-mono">IP Address</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="log in stats.recent_logs" :key="log.id" class="hover:bg-slate-50">
                <td class="p-3 text-slate-500 font-mono text-[11px]">{{ log.created_at }}</td>
                <td class="p-3 font-bold text-slate-800">{{ log.username || 'System' }}</td>
                <td class="p-3 font-bold text-amber-700">{{ log.action }}</td>
                <td class="p-3 text-slate-600">{{ log.details }}</td>
                <td class="p-3 font-mono text-slate-400">{{ log.ip_address }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 2: STAFF & USER MANAGEMENT -->
    <div v-if="activeTab === 'users'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div>
          <h2 class="text-base font-bold text-slate-800">Staff & System Accounts</h2>
          <p class="text-xs text-slate-500">Manage credentials and roles for Administrator, Coordinator, Registrar, Treasury, and Records.</p>
        </div>
        <button @click="openUserModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-500 text-white shadow-md transition flex items-center space-x-1.5">
          <Plus class="w-4 h-4" />
          <span>Create User Account</span>
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
              <th class="p-3.5">Name / Username</th>
              <th class="p-3.5">Email</th>
              <th class="p-3.5">Role</th>
              <th class="p-3.5">Status</th>
              <th class="p-3.5 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="u in usersList.users" :key="u.id" class="hover:bg-slate-50 transition">
              <td class="p-3.5">
                <div class="font-bold text-slate-900">{{ u.first_name }} {{ u.last_name }}</div>
                <div class="text-[11px] font-mono text-slate-400">@{{ u.username }}</div>
              </td>
              <td class="p-3.5 text-slate-600">{{ u.email }}</td>
              <td class="p-3.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase" :class="getRoleClass(u.role_slug)">
                  {{ u.role_name }}
                </span>
              </td>
              <td class="p-3.5">
                <span :class="u.status === 'Active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                  {{ u.status }}
                </span>
              </td>
              <td class="p-3.5 text-right">
                <button @click="openUserModal(u)" class="px-2.5 py-1 rounded text-xs font-semibold text-amber-600 hover:bg-amber-50">
                  Edit
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 3: SCHOOL YEAR LOCK / UNLOCK -->
    <div v-if="activeTab === 'school_years'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
          <h2 class="text-base font-bold text-slate-900">School Year Academic & Curriculum Governance</h2>
          <p class="text-xs text-slate-500">Manage enrollment admission intake gates and DepEd curriculum blueprint freezes for each academic cycle.</p>
        </div>
        <button 
          @click="openSchoolYearModal()" 
          class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-500 text-white shadow-md transition flex items-center space-x-1.5 shrink-0"
        >
          <Plus class="w-4 h-4" />
          <span>Add School Year</span>
        </button>
      </div>

      <div class="space-y-4">
        <div v-for="sy in schoolYears" :key="sy.id" class="p-6 rounded-3xl border border-slate-200 bg-slate-50/60 space-y-4 shadow-2xs">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200/70 pb-3">
            <div>
              <div class="flex items-center space-x-2">
                <h3 class="font-extrabold text-base text-slate-900">{{ sy.name }} ({{ sy.code }})</h3>
                <span v-if="sy.is_active" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                  ACTIVE SCHOOL YEAR
                </span>
                <span v-else class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-700 border border-slate-300">
                  STANDBY / UPCOMING
                </span>
              </div>
              <p class="text-xs text-slate-500 mt-0.5">Duration: {{ sy.start_date }} to {{ sy.end_date }} • Active Term: {{ sy.active_semester }}</p>
            </div>

            <div class="flex items-center space-x-2">
              <button 
                v-if="!sy.is_active"
                @click="openActiveSyModal(sy)" 
                class="px-3 py-1.5 rounded-xl font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs transition"
              >
                ⭐ Set as Active
              </button>
              <button 
                @click="openSchoolYearModal(sy)" 
                class="px-3 py-1.5 rounded-xl font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition"
              >
                Edit
              </button>
            </div>
          </div>

          <!-- DUAL CONTROLS GRID: ENROLLMENT GATE & CURRICULUM BLUEPRINT -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- 1. ENROLLMENT INTAKE LOCK -->
            <div class="p-4 rounded-2xl bg-white border border-slate-200 flex items-center justify-between gap-3">
              <div>
                <div class="text-[10px] font-extrabold uppercase text-slate-400">Admission & Enrollment Gate</div>
                <div class="font-bold text-xs text-slate-800 mt-0.5">
                  <span v-if="!sy.is_active" class="text-slate-500 font-extrabold">
                    🔒 INACTIVE CYCLE (INTAKE CLOSED)
                  </span>
                  <span v-else :class="sy.is_locked ? 'text-rose-700 font-extrabold' : 'text-emerald-700 font-extrabold'">
                    {{ sy.is_locked ? '🔒 INTAKE CLOSED' : '🔓 OPEN FOR ADMISSION' }}
                  </span>
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">
                  {{ !sy.is_active ? 'Admission intake can only be opened when this cycle is Set as Active' : (sy.is_locked ? 'New applications & payment assessments closed' : 'Accepting new admission & enrollment entries') }}
                </div>
              </div>

              <!-- Active vs Inactive Intake Controls -->
              <button 
                v-if="sy.is_active"
                @click="toggleLock(sy.id)"
                :class="sy.is_locked ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-rose-600 hover:bg-rose-500'"
                class="px-3.5 py-2 rounded-xl text-xs font-bold text-white shadow-xs transition shrink-0"
              >
                {{ sy.is_locked ? 'Unlock Intake' : 'Lock Intake' }}
              </button>
              <button 
                v-else
                disabled
                class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed shadow-none shrink-0"
                title="Must be the Active School Year to unlock admission intake"
              >
                Intake Locked
              </button>
            </div>

            <!-- 2. CURRICULUM BLUEPRINT LOCK -->
            <div class="p-4 rounded-2xl bg-white border border-slate-200 flex items-center justify-between gap-3">
              <div>
                <div class="text-[10px] font-extrabold uppercase text-slate-400">DepEd Curriculum Blueprint</div>
                <div class="font-bold text-xs text-slate-800 mt-0.5">
                  <span :class="sy.curriculum_locked ? 'text-emerald-700 font-extrabold' : 'text-amber-700 font-extrabold'">
                    {{ sy.curriculum_locked ? '🔒 DECLARED & LOCKED' : '🟡 DRAFT / SETUP MODE' }}
                  </span>
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">
                  {{ !sy.is_active ? 'Drafting permitted. Official DepEd freeze requires Active School Year status' : (sy.curriculum_locked ? 'Subjects & Strands frozen (records protected)' : 'Subjects & Strands editable') }}
                </div>
              </div>

              <!-- Active vs Inactive Curriculum Controls -->
              <button 
                v-if="sy.is_active"
                @click="openCurriculumLockModal(sy)"
                :class="sy.curriculum_locked ? 'bg-slate-800 hover:bg-slate-700 text-slate-200' : 'bg-emerald-600 hover:bg-emerald-500 text-white'"
                class="px-3.5 py-2 rounded-xl text-xs font-bold shadow-xs transition shrink-0"
              >
                {{ sy.curriculum_locked ? 'Unlock Setup' : 'Declare & Lock' }}
              </button>
              <button 
                v-else
                disabled
                class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed shadow-none shrink-0"
                title="Must be the Active School Year to declare and lock curriculum"
              >
                Draft Mode
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- USER CREATE / EDIT MODAL -->
    <div v-if="showUserModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 text-xs">
        <h3 class="text-base font-bold text-slate-900 mb-4">{{ userForm.id ? 'Edit User Account' : 'Create User Account' }}</h3>
        <form @submit.prevent="saveUser" class="space-y-4">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">First Name *</label>
              <input v-model="userForm.first_name" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-300" />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Last Name *</label>
              <input v-model="userForm.last_name" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-300" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Username *</label>
              <input v-model="userForm.username" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono" />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Role *</label>
              <select v-model="userForm.role_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white">
                <option v-for="r in usersList.roles" :key="r.id" :value="r.id">{{ r.name }}</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Email Address *</label>
            <input v-model="userForm.email" type="email" required class="w-full px-3 py-2 rounded-xl border border-slate-300" />
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Password {{ userForm.id ? '(Leave blank to keep unchanged)' : '*' }}</label>
            <input v-model="userForm.password" type="password" :required="!userForm.id" class="w-full px-3 py-2 rounded-xl border border-slate-300" />
          </div>

          <div class="flex justify-end space-x-3 pt-3">
            <button type="button" @click="showUserModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100">Cancel</button>
            <button type="submit" class="px-5 py-2 rounded-xl font-bold bg-amber-600 hover:bg-amber-500 text-white shadow-md">Save Account</button>
          </div>
        </form>
      </div>
    </div>

    <!-- ADMIN CURRICULUM DECLARATION & LOCK CONFIRMATION POPUP MODAL -->
    <div v-if="curriculumLockModal.isOpen" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 text-xs space-y-4 animate-in fade-in zoom-in duration-150">
        
        <!-- Header with Dynamic Icon and Alert Styling -->
        <div class="flex items-start space-x-3.5 border-b border-slate-100 pb-4">
          <div 
            class="p-3 rounded-2xl shrink-0"
            :class="curriculumLockModal.sy?.curriculum_locked ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200'"
          >
            <Unlock v-if="curriculumLockModal.sy?.curriculum_locked" class="w-6 h-6 text-amber-700" />
            <Lock v-else class="w-6 h-6 text-emerald-700" />
          </div>
          <div>
            <div 
              class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase font-mono tracking-wider mb-1"
              :class="curriculumLockModal.sy?.curriculum_locked ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-emerald-100 text-emerald-900 border border-emerald-200'"
            >
              <span>{{ curriculumLockModal.sy?.curriculum_locked ? 'Unlock for Drafting' : 'Official DepEd Academic Freeze' }}</span>
            </div>
            <h3 class="text-base font-extrabold text-slate-900 leading-snug">
              {{ curriculumLockModal.sy?.curriculum_locked ? 'Switch Curriculum to Draft / Setup Mode?' : 'Officially Declare & Lock SY Curriculum?' }}
            </h3>
            <p class="text-[11px] text-slate-500 mt-0.5">
              School Year: <strong>{{ curriculumLockModal.sy?.name }} ({{ curriculumLockModal.sy?.code }})</strong>
            </p>
          </div>
        </div>

        <!-- Explanatory Box -->
        <div 
          class="p-4 rounded-2xl border text-xs space-y-2 leading-relaxed"
          :class="curriculumLockModal.sy?.curriculum_locked ? 'bg-amber-50/80 border-amber-200 text-amber-950' : 'bg-emerald-50/80 border-emerald-200 text-emerald-950'"
        >
          <div class="font-bold text-slate-900 flex items-center space-x-1.5">
            <AlertCircle class="w-4 h-4" :class="curriculumLockModal.sy?.curriculum_locked ? 'text-amber-700' : 'text-emerald-700'" />
            <span>{{ curriculumLockModal.sy?.curriculum_locked ? 'Administrator Safeguard Notice:' : 'Curriculum Lock Consequences:' }}</span>
          </div>

          <template v-if="curriculumLockModal.sy?.curriculum_locked">
            <ul class="list-disc list-inside space-y-1.5 text-[11px] text-slate-700">
              <li>Re-enables editing, adding, and deleting learning area subjects and strands in Coordinator dashboard.</li>
              <li><strong class="text-amber-900">Warning:</strong> Only unlock if academic adjustments are genuinely required prior to student enrollment processing.</li>
              <li>Re-lock once edits are complete to protect permanent student records.</li>
            </ul>
          </template>

          <template v-else>
            <ul class="list-disc list-inside space-y-1.5 text-[11px] text-slate-700">
              <li>Freezes all 119 subjects and 8 strands from modification or deletion across all dashboards.</li>
              <li>Protects student permanent records (SF10), quarterly report cards (SF9), and section timetables.</li>
              <li>Any DepEd updates will take effect in subsequent school year cycles.</li>
            </ul>
          </template>
        </div>

        <!-- Footer Action Buttons -->
        <div class="flex items-center justify-end space-x-2.5 pt-3 border-t border-slate-100">
          <button 
            type="button" 
            @click="curriculumLockModal.isOpen = false" 
            :disabled="curriculumLockModal.isProcessing"
            class="px-4 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs"
          >
            Cancel
          </button>
          
          <button 
            type="button" 
            @click="executeAdminCurriculumLock()" 
            :disabled="curriculumLockModal.isProcessing"
            class="px-5 py-2.5 rounded-xl font-bold text-white shadow-md transition flex items-center space-x-2 text-xs"
            :class="curriculumLockModal.sy?.curriculum_locked ? 'bg-amber-600 hover:bg-amber-500' : 'bg-emerald-600 hover:bg-emerald-500'"
          >
            <span v-if="curriculumLockModal.isProcessing" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <Unlock v-else-if="curriculumLockModal.sy?.curriculum_locked" class="w-3.5 h-3.5 text-white" />
            <Lock v-else class="w-3.5 h-3.5 text-white" />
            <span>{{ curriculumLockModal.isProcessing ? 'Updating Status...' : (curriculumLockModal.sy?.curriculum_locked ? 'Confirm Switch to Draft Mode' : 'Confirm Declare & Lock') }}</span>
          </button>
        </div>

      </div>
    </div>

    <!-- SCHOOL YEAR CREATE / EDIT MODAL -->
    <div v-if="showSchoolYearModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 text-xs">
        <h3 class="text-base font-bold text-slate-900 mb-4">{{ schoolYearForm.id ? 'Edit School Year' : 'Create New School Year' }}</h3>
        <form @submit.prevent="saveSchoolYear" class="space-y-4">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Code * (e.g. 2027-2028)</label>
              <input v-model="schoolYearForm.code" type="text" required placeholder="2027-2028" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold" />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Active Semester *</label>
              <select v-model="schoolYearForm.active_semester" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white font-semibold">
                <option value="1st Semester">1st Semester</option>
                <option value="2nd Semester">2nd Semester</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Full Name * (e.g. School Year 2027-2028)</label>
            <input v-model="schoolYearForm.name" type="text" required placeholder="School Year 2027-2028" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-medium" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Start Date *</label>
              <input v-model="schoolYearForm.start_date" type="date" required class="w-full px-3 py-2 rounded-xl border border-slate-300" />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">End Date *</label>
              <input v-model="schoolYearForm.end_date" type="date" required class="w-full px-3 py-2 rounded-xl border border-slate-300" />
            </div>
          </div>

          <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-900">
            <strong>Note:</strong> New school years are created with admission gate locked and curriculum in Setup / Draft mode so the academic coordinator can prepare offerings.
          </div>

          <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100">
            <button type="button" @click="showSchoolYearModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold">Cancel</button>
            <button type="submit" :disabled="isSavingSy" class="px-5 py-2 rounded-xl font-bold bg-amber-600 hover:bg-amber-500 text-white shadow-md transition flex items-center space-x-1.5">
              <span v-if="isSavingSy" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              <span>{{ isSavingSy ? 'Saving...' : 'Save School Year' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- SET ACTIVE SCHOOL YEAR CONFIRMATION POPUP MODAL -->
    <div v-if="activeSyModal.isOpen" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 text-xs space-y-4 animate-in fade-in zoom-in duration-150">
        
        <div class="flex items-start space-x-3.5 border-b border-slate-100 pb-3.5">
          <div class="p-3 rounded-2xl bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
            <Sparkles class="w-6 h-6 text-emerald-700" />
          </div>
          <div>
            <div class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase font-mono tracking-wider bg-emerald-100 text-emerald-900 border border-emerald-200 mb-1">
              <span>Academic Cycle Switch</span>
            </div>
            <h3 class="text-base font-extrabold text-slate-900 leading-snug">
              Set as Active School Year?
            </h3>
            <p class="text-[11px] text-slate-500 mt-0.5">
              Target: <strong>{{ activeSyModal.sy?.name }} ({{ activeSyModal.sy?.code }})</strong>
            </p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2 leading-relaxed text-slate-700">
          <div class="font-bold text-slate-900 flex items-center space-x-1.5">
            <AlertCircle class="w-4 h-4 text-emerald-700" />
            <span>Active School Year Transition Notice:</span>
          </div>
          <ul class="list-disc list-inside space-y-1 text-[11px]">
            <li>New student registrations will default to this school year cycle.</li>
            <li>All dashboards and student portals will reflect <strong>{{ activeSyModal.sy?.name }}</strong>.</li>
            <li>Historical enrollment and payment records of previous school years remain safely archived and searchable.</li>
          </ul>
        </div>

        <div class="flex items-center justify-end space-x-2.5 pt-2 border-t border-slate-100">
          <button 
            type="button" 
            @click="activeSyModal.isOpen = false" 
            :disabled="activeSyModal.isProcessing"
            class="px-4 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs"
          >
            Cancel
          </button>
          
          <button 
            type="button" 
            @click="executeSetActiveSy()" 
            :disabled="activeSyModal.isProcessing"
            class="px-5 py-2.5 rounded-xl font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md transition flex items-center space-x-2 text-xs"
          >
            <span v-if="activeSyModal.isProcessing" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <Sparkles v-else class="w-3.5 h-3.5 text-white" />
            <span>{{ activeSyModal.isProcessing ? 'Activating...' : 'Confirm Set Active' }}</span>
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Plus, Lock, Unlock, AlertCircle, Sparkles } from 'lucide-vue-next';
import api from '../../services/api';

const activeTab = ref('stats');
const stats = ref({ recent_logs: [] });
const usersList = ref({ users: [], roles: [] });
const schoolYears = ref([]);
const successMessage = ref('');

const showUserModal = ref(false);
const userForm = ref({
  id: null,
  role_id: 2,
  username: '',
  email: '',
  password: '',
  first_name: '',
  last_name: '',
  status: 'Active'
});

const getRoleClass = (slug) => {
  if (slug === 'admin') return 'bg-amber-100 text-amber-800';
  if (slug === 'coordinator') return 'bg-purple-100 text-purple-800';
  if (slug === 'registrar') return 'bg-blue-100 text-blue-800';
  if (slug === 'treasury') return 'bg-emerald-100 text-emerald-800';
  if (slug === 'records') return 'bg-cyan-100 text-cyan-800';
  return 'bg-slate-100 text-slate-800';
};

const loadStats = async () => {
  try {
    const res = await api.getDashboardStats();
    stats.value = res.data;
  } catch (err) {
    console.error('Failed to load stats:', err);
  }
};

const loadUsers = async () => {
  try {
    const res = await api.getUsers();
    usersList.value = res.data;
  } catch (err) {
    console.error('Failed to load users:', err);
  }
};

const loadSchoolYears = async () => {
  try {
    const res = await api.getSchoolYears();
    schoolYears.value = res.data;
  } catch (err) {
    console.error('Failed to load school years:', err);
  }
};

const toggleLock = async (syId) => {
  try {
    const res = await api.toggleSchoolYearLock(syId);
    successMessage.value = res.message;
    await loadSchoolYears();
  } catch (err) {
    alert(err.message || 'Failed to toggle school year lock.');
  }
};

const curriculumLockModal = ref({
  isOpen: false,
  sy: null,
  isProcessing: false
});

const openCurriculumLockModal = (sy) => {
  curriculumLockModal.value = {
    isOpen: true,
    sy: sy,
    isProcessing: false
  };
};

const executeAdminCurriculumLock = async () => {
  if (!curriculumLockModal.value.sy) return;
  curriculumLockModal.value.isProcessing = true;
  try {
    const res = await api.toggleAdminCurriculumLock(curriculumLockModal.value.sy.id);
    successMessage.value = res.message;
    curriculumLockModal.value.isOpen = false;
    await loadSchoolYears();
  } catch (err) {
    alert(err.message || 'Failed to toggle curriculum lock.');
  } finally {
    curriculumLockModal.value.isProcessing = false;
  }
};

// --- SCHOOL YEAR FORM & ACTIVE CYCLE HANDLERS ---
const showSchoolYearModal = ref(false);
const isSavingSy = ref(false);
const schoolYearForm = ref({
  id: null,
  code: '',
  name: '',
  start_date: '',
  end_date: '',
  active_semester: '1st Semester',
  is_active: 0
});

const openSchoolYearModal = (sy = null) => {
  if (sy) {
    schoolYearForm.value = {
      id: sy.id,
      code: sy.code,
      name: sy.name,
      start_date: sy.start_date,
      end_date: sy.end_date,
      active_semester: sy.active_semester || '1st Semester',
      is_active: sy.is_active
    };
  } else {
    schoolYearForm.value = {
      id: null,
      code: '2028-2029',
      name: 'School Year 2028-2029',
      start_date: '2028-08-01',
      end_date: '2029-05-31',
      active_semester: '1st Semester',
      is_active: 0
    };
  }
  showSchoolYearModal.value = true;
};

const saveSchoolYear = async () => {
  isSavingSy.value = true;
  try {
    const res = await api.saveSchoolYear(schoolYearForm.value);
    successMessage.value = res.message || 'School year saved successfully!';
    showSchoolYearModal.value = false;
    await loadSchoolYears();
  } catch (err) {
    alert(err.message || 'Failed to save school year.');
  } finally {
    isSavingSy.value = false;
  }
};

const activeSyModal = ref({
  isOpen: false,
  sy: null,
  isProcessing: false
});

const openActiveSyModal = (sy) => {
  activeSyModal.value = {
    isOpen: true,
    sy: sy,
    isProcessing: false
  };
};

const executeSetActiveSy = async () => {
  if (!activeSyModal.value.sy) return;
  activeSyModal.value.isProcessing = true;
  try {
    const res = await api.setActiveSchoolYear(activeSyModal.value.sy.id);
    successMessage.value = res.message || 'Active school year changed successfully!';
    activeSyModal.value.isOpen = false;
    await loadSchoolYears();
    await loadStats();
  } catch (err) {
    alert(err.message || 'Failed to change active school year.');
  } finally {
    activeSyModal.value.isProcessing = false;
  }
};

const openUserModal = (u = null) => {
  if (u) {
    userForm.value = {
      id: u.id,
      role_id: u.role_id,
      username: u.username,
      email: u.email,
      password: '',
      first_name: u.first_name,
      last_name: u.last_name,
      status: u.status
    };
  } else {
    userForm.value = {
      id: null,
      role_id: 2,
      username: '',
      email: '',
      password: 'password123',
      first_name: '',
      last_name: '',
      status: 'Active'
    };
  }
  showUserModal.value = true;
};

const saveUser = async () => {
  try {
    await api.saveUser(userForm.value);
    successMessage.value = 'User account saved successfully!';
    showUserModal.value = false;
    await loadUsers();
  } catch (err) {
    alert(err.message || 'Failed to save user.');
  }
};

onMounted(() => {
  loadStats();
  loadUsers();
  loadSchoolYears();
});
</script>
