<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Top Header Banner -->
    <div class="no-print bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div>
        <div class="flex items-center space-x-3">
          <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-950 text-emerald-400 border border-emerald-500/30">
            Application No: {{ application?.application_no || 'Loading...' }}
          </span>
          <span :class="statusBadgeClass" class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
            {{ application?.status || 'Pending' }}
          </span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-white mt-2 tracking-tight">JJKINGS Biringan School Admission & Enrollment Wizard</h1>
        <p class="text-xs text-slate-400 mt-1">
          Complete the steps below, upload your DepEd credentials, settle your downpayment, and secure your official Certificate of Registration.
        </p>
      </div>

      <div v-if="application?.status === 'Enrolled'" class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500/40 text-left shrink-0">
        <div class="text-[10px] uppercase font-bold text-emerald-400">Enrollment Completed!</div>
        <div class="text-sm font-bold text-white mt-0.5">Permanent ID: {{ application?.student_no || application?.assessment_info?.student_number || 'Generated' }}</div>
        <router-link to="/student" class="inline-block mt-2 px-3.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 transition">
          Go to Student Portal →
        </router-link>
      </div>
    </div>

    <!-- OFFICIALLY ENROLLED CELEBRATORY BANNER -->
    <div v-if="application?.status === 'Enrolled' || application?.assessment_info?.enrollment_status === 'Officially Enrolled'" class="no-print p-6 rounded-3xl bg-gradient-to-br from-emerald-950 via-slate-900 to-emerald-900 border-2 border-emerald-500 text-white shadow-2xl mb-8 animate-in fade-in slide-in-from-top-4 duration-300">
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-start space-x-4">
          <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-slate-950 flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/30">
            <GraduationCap class="w-8 h-8" />
          </div>
          <div>
            <div class="inline-flex items-center space-x-1.5 px-3 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 text-[11px] font-bold uppercase tracking-wider mb-1.5">
              <CheckCircle class="w-3.5 h-3.5" />
              <span>Officially Enrolled & Validated</span>
            </div>
            <h3 class="text-xl font-extrabold text-white tracking-tight">Congratulations! Your Official Student Account is Ready!</h3>
            <p class="text-xs text-slate-300 mt-1 max-w-xl">
              Your payment has been successfully credited and official Certificate of Registration issued. You can now access your schedule, grades, and school timetable in the Student Portal.
            </p>
          </div>
        </div>

        <div class="bg-slate-950/90 p-4 rounded-2xl border border-emerald-500/50 w-full md:w-auto min-w-[280px] font-mono text-xs space-y-2 shrink-0 shadow-xl">
          <div class="text-[10px] uppercase font-bold text-emerald-400 tracking-wider flex items-center justify-between">
            <span>Student Portal Credentials</span>
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
          </div>
          <div class="flex justify-between border-b border-slate-800 pb-1.5 pt-1">
            <span class="text-slate-400">Student ID / Username:</span>
            <strong class="text-white font-bold text-sm text-emerald-300">{{ application?.student_no || application?.assessment_info?.student_number || 'Generated' }}</strong>
          </div>
          <div class="flex justify-between border-b border-slate-800 pb-1.5">
            <span class="text-slate-400">Default Password:</span>
            <strong class="text-amber-300 font-bold tracking-wider">{{ (application?.last_name || '').toUpperCase() }}</strong>
          </div>
          <div class="text-[10px] text-slate-400 italic pt-0.5">Password is your Last Name in ALL CAPS.</div>
          <button 
            @click="goToStudentLogin" 
            class="w-full mt-2 py-2.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs transition flex items-center justify-center space-x-1.5 shadow-md shadow-emerald-500/30 cursor-pointer"
          >
            <span>Proceed to Student Portal Login</span>
            <ArrowRight class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- DEFICIENT DOCUMENTS ALERT BANNER -->
    <div v-if="deficientDocs.length > 0" class="no-print p-4 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-950 text-xs mb-6 shadow-sm flex items-center justify-between gap-4">
      <div class="flex items-center space-x-3">
        <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm">
          <AlertTriangle class="w-5 h-5 animate-pulse" />
        </div>
        <div>
          <h4 class="font-extrabold text-sm text-rose-950">Action Required: {{ deficientDocs.length }} Document(s) Marked as Deficient</h4>
          <p class="text-xs text-rose-800 mt-0.5">
            The Registrar has reviewed your requirements and marked <span class="font-bold underline">{{ deficientDocs.map(d => d.document_type).join(', ') }}</span> as deficient. Please re-upload clearer copies in Step 3.
          </p>
        </div>
      </div>
      <button @click="activeStep = 3" class="whitespace-nowrap px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-sm transition flex items-center space-x-1.5 shrink-0 cursor-pointer">
        <span>Fix Deficient Files</span>
        <ArrowRight class="w-3.5 h-3.5" />
      </button>
    </div>

    <!-- Alert / Notifications (Hidden in Print) -->
    <div v-if="successMessage" class="no-print p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500 text-emerald-300 text-xs mb-6 flex items-center justify-between">
      <div class="flex items-center space-x-2">
        <CheckCircle class="w-4 h-4 text-emerald-400" />
        <span>{{ successMessage }}</span>
      </div>
      <button @click="successMessage = ''" class="text-emerald-400 hover:text-white font-bold">✕</button>
    </div>

    <div v-if="errorMessage" class="no-print p-4 rounded-2xl bg-rose-950/80 border border-rose-800 text-rose-300 text-xs mb-6 flex items-center justify-between">
      <div class="flex items-center space-x-2">
        <AlertCircle class="w-4 h-4 text-rose-400" />
        <span>{{ errorMessage }}</span>
      </div>
      <button @click="errorMessage = ''" class="text-rose-400 hover:text-white font-bold">✕</button>
    </div>

    <!-- MAIN RESPONSIVE TWO-COLUMN WIZARD LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- LEFT SIDEBAR: STEP NAVIGATION & ADMISSION SUMMARY (4 cols on lg) -->
      <aside class="no-print lg:col-span-4 space-y-5 lg:sticky lg:top-6">
        <!-- Sidebar Step Stepper Card -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-4">
          <!-- Stepper Header -->
          <div class="border-b border-slate-100 pb-3.5">
            <div class="flex items-center justify-between">
              <span class="text-xs font-extrabold uppercase tracking-wider text-slate-900 flex items-center space-x-1.5">
                <Activity class="w-4 h-4 text-emerald-600" />
                <span>Admission Steps</span>
              </span>
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold font-mono bg-emerald-100 text-emerald-900">
                Step {{ activeStep }} of 5
              </span>
            </div>
            <!-- Animated Progress Bar -->
            <div class="w-full h-2 rounded-full bg-slate-100 mt-3 overflow-hidden">
              <div 
                class="h-full rounded-full bg-gradient-to-r from-emerald-600 via-teal-500 to-emerald-400 transition-all duration-500 shadow-sm"
                :style="{ width: ((activeStep / 5) * 100) + '%' }"
              ></div>
            </div>
          </div>

          <!-- Step Lock Notice (If User Clicked A Locked Step) -->
          <div v-if="stepLockNotice" class="p-3 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs flex items-start space-x-2 animate-in fade-in duration-200 shadow-sm">
            <AlertCircle class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
            <div class="flex-1">
              <span class="font-bold block text-amber-950">Step Locked:</span>
              <span class="text-[11px] leading-tight text-amber-900">{{ stepLockNotice }}</span>
            </div>
            <button @click="stepLockNotice = ''" class="font-bold text-amber-700 hover:text-amber-950 text-xs">✕</button>
          </div>

          <!-- Vertical Step List -->
          <div class="space-y-2">
            <button 
              v-for="st in steps" 
              :key="st.id"
              type="button"
              @click="selectStep(st.id)"
              :class="getSidebarStepClass(st.id)"
              class="w-full p-3 rounded-2xl transition flex items-center justify-between text-left group"
            >
              <div class="flex items-center space-x-3 min-w-0">
                <!-- Step Number / Status Icon -->
                <div 
                  class="w-8 h-8 rounded-xl flex items-center justify-center font-extrabold text-xs shrink-0 transition"
                  :class="getSidebarIconClass(st.id)"
                >
                  <Check v-if="isStepDone(st.id) && activeStep !== st.id" class="w-4 h-4 text-emerald-600 stroke-[3]" />
                  <Lock v-else-if="!canAccessStep(st.id)" class="w-3.5 h-3.5 text-slate-400" />
                  <span v-else>{{ st.id }}</span>
                </div>

                <!-- Step Text -->
                <div class="min-w-0 flex-1">
                  <div class="font-bold text-xs truncate leading-tight" :class="activeStep === st.id ? 'text-white' : 'text-slate-800'">
                    {{ st.title }}
                  </div>
                  <div class="text-[10px] truncate leading-tight mt-0.5" :class="activeStep === st.id ? 'text-emerald-300' : 'text-slate-400'">
                    {{ getStepSubtitle(st.id) }}
                  </div>
                </div>
              </div>

              <!-- Right Arrow Indicator -->
              <ChevronRight 
                class="w-4 h-4 shrink-0 transition" 
                :class="activeStep === st.id ? 'text-emerald-400 translate-x-0.5' : 'text-slate-300 group-hover:text-slate-500'" 
              />
            </button>
          </div>
        </div>

        <!-- Mini Application Summary Widget -->
        <div class="bg-slate-900 rounded-3xl p-5 border border-slate-800 text-white shadow-md text-xs space-y-3 font-mono">
          <div class="text-[10px] uppercase font-bold text-emerald-400 tracking-wider flex items-center justify-between border-b border-slate-800 pb-2">
            <span>Application Summary</span>
            <span class="w-2 h-2 rounded-full" :class="application?.status === 'Enrolled' ? 'bg-emerald-400' : 'bg-blue-400 animate-pulse'"></span>
          </div>

          <div class="space-y-2 text-[11px]">
            <div class="flex justify-between">
              <span class="text-slate-400">Reference No:</span>
              <strong class="text-white font-bold">{{ application?.application_no || 'Pending' }}</strong>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-400">Program:</span>
              <strong class="text-emerald-300 font-bold">
                {{ isStep2Completed && application?.grade_level_name ? (application.grade_level_name + (isSHS && application.strand_code ? ' (' + application.strand_code + ')' : '')) : 'Not Selected' }}
              </strong>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-slate-400">Status:</span>
              <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase" :class="statusBadgeClass">
                {{ application?.status || 'Draft' }}
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-400">School Year:</span>
              <span class="text-slate-300">{{ application?.school_year_name || 'SY 2026-2027' }}</span>
            </div>
          </div>
        </div>
      </aside>

      <!-- RIGHT WORKSPACE: ACTIVE STEP FORM (8 cols on lg) -->
      <main class="lg:col-span-8 space-y-6">
        
        <!-- STEP 1: PERSONAL & DEMOGRAPHIC DETAILS -->
        <div v-if="activeStep === 1" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
          <div class="border-b border-slate-100 pb-4 mb-6">
            <h2 class="text-lg font-bold text-slate-800">Step 1: Student Demographics & Learner Reference Number (LRN)</h2>
            <p class="text-xs text-slate-500 mt-1">Please provide complete and accurate information matching your PSA Birth Certificate.</p>
          </div>

          <form @submit.prevent="saveApplicationDetails(false)" class="space-y-6">
            <!-- LRN & Basic Info -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">12-Digit DepEd LRN *</label>
                <input 
                  v-model="form.lrn" 
                  type="text" 
                  maxlength="12" 
                  required 
                  @keydown="blockNonNumeric($event)"
                  @input="handleNumericInput('lrn', $event, 12)"
                  placeholder="e.g. 102938475611" 
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-bold focus:ring-2 focus:ring-emerald-500" 
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Applicant Type *</label>
                <select v-model="form.applicant_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-white">
                  <option value="New Student">New Student (Fresh Enrollee)</option>
                  <option value="Transferee">Transferee from Other School</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Contact Mobile Number *</label>
                <input 
                  v-model="form.contact_number" 
                  type="tel" 
                  required 
                  maxlength="11"
                  @keydown="blockNonNumeric($event)"
                  @input="handleNumericInput('contact_number', $event, 11)"
                  placeholder="09171234567" 
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono" 
                />
              </div>
            </div>

            <!-- Full Name -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">First Name *</label>
                <input 
                  v-model="form.first_name" 
                  type="text" 
                  required 
                  @keydown="blockNonAlphabetic($event)"
                  @input="handleAlphabeticInput('first_name', $event)"
                  placeholder="Given Name" 
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium" 
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Middle Name</label>
                <input 
                  v-model="form.middle_name" 
                  type="text" 
                  @keydown="blockNonAlphabetic($event)"
                  @input="handleAlphabeticInput('middle_name', $event)"
                  placeholder="Middle Name" 
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium" 
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Last Name *</label>
                <input 
                  v-model="form.last_name" 
                  type="text" 
                  required 
                  @keydown="blockNonAlphabetic($event)"
                  @input="handleAlphabeticInput('last_name', $event)"
                  placeholder="Family Name" 
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium" 
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Suffix</label>
                <input 
                  v-model="form.suffix" 
                  type="text" 
                  @keydown="blockNonAlphabetic($event)"
                  @input="handleAlphabeticInput('suffix', $event)"
                  placeholder="Jr., III (Optional)" 
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs" 
                />
              </div>
            </div>

            <!-- Gender & Birth Details -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Biological Gender *</label>
                <select v-model="form.gender" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-white">
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Date of Birth (Min 11 y/o) *</label>
                <input v-model="form.birthdate" type="date" :min="minBirthdate" :max="maxBirthdate" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Place of Birth *</label>
                <input v-model="form.birthplace" type="text" placeholder="City / Municipality" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs" />
              </div>
            </div>

            <!-- Address Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Barangay *</label>
                <input v-model="form.address_barangay" type="text" required placeholder="e.g. Barangay 405" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">City / Municipality *</label>
                <input v-model="form.address_city" type="text" required placeholder="e.g. Manila" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Province *</label>
                <input v-model="form.address_province" type="text" required placeholder="e.g. Metro Manila" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs" />
              </div>
            </div>

            <!-- Guardian Info -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-100">
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Parent / Guardian Full Name *</label>
                <input 
                  v-model="form.guardian_name" 
                  type="text" 
                  required 
                  @keydown="blockNonAlphabetic($event)"
                  @input="handleAlphabeticInput('guardian_name', $event)"
                  placeholder="Guardian's Name" 
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs" 
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Relationship to Student *</label>
                <input 
                  v-model="form.guardian_relationship" 
                  type="text" 
                  required 
                  @keydown="blockNonAlphabetic($event)"
                  @input="handleAlphabeticInput('guardian_relationship', $event)"
                  placeholder="e.g. Mother, Father, Aunt" 
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs" 
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Guardian Contact Number *</label>
                <input 
                  v-model="form.guardian_contact" 
                  type="tel" 
                  required 
                  maxlength="11"
                  @keydown="blockNonNumeric($event)"
                  @input="handleNumericInput('guardian_contact', $event, 11)"
                  placeholder="09171234567" 
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono" 
                />
              </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
              <button type="submit" class="px-6 py-2.5 rounded-xl font-bold bg-emerald-600 hover:bg-emerald-500 text-white text-sm shadow-md transition flex items-center space-x-2 cursor-pointer">
                <span>Save Demographics & Proceed to Step 2</span>
                <ArrowRight class="w-4 h-4" />
              </button>
            </div>
          </form>
        </div>

        <!-- STEP 2: GRADE LEVEL & ACADEMIC STRAND -->
        <div v-if="activeStep === 2" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
          <div class="border-b border-slate-100 pb-4 mb-6">
            <h2 class="text-lg font-bold text-slate-800">Step 2: Academic Program & DepEd Voucher Subsidy</h2>
            <p class="text-xs text-slate-500 mt-1">Select your desired Junior High School grade or Senior High School strand.</p>
          </div>

          <form @submit.prevent="saveApplicationDetails(true)" class="space-y-6">
            <!-- Grade Level Selector -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Grade Level *</label>
              <select v-model="form.grade_level_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-white font-medium">
                <option :value="null">-- Select Grade Level --</option>
                <optgroup label="Junior High School (JHS)">
                  <option v-for="gl in jhsLevels" :key="gl.id" :value="gl.id">{{ gl.name }} ({{ gl.code }})</option>
                </optgroup>
                <optgroup label="Senior High School (SHS)">
                  <option v-for="gl in shsLevels" :key="gl.id" :value="gl.id">{{ gl.name }} ({{ gl.code }})</option>
                </optgroup>
              </select>
            </div>

            <!-- Track & Strand (If SHS) -->
            <div v-if="isSHS" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200 animate-in fade-in duration-200">
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Senior High Track *</label>
                <select v-model="form.track_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-white">
                  <option :value="null">-- Select Track --</option>
                  <option v-for="tr in academicOptions.tracks" :key="tr.id" :value="tr.id">{{ tr.name }} ({{ tr.code }})</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Senior High Academic Strand *</label>
                <select v-model="form.strand_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-white font-medium">
                  <option :value="null">-- Select Strand --</option>
                  <option v-for="st in filteredStrands" :key="st.id" :value="st.id">{{ st.name }} ({{ st.code }})</option>
                </select>
              </div>
            </div>

            <!-- Voucher Status & Previous School -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">DepEd Voucher Category *</label>
                <select v-model="form.voucher_status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-white">
                  <option value="None">None (Regular Full Tuition)</option>
                  <option value="Public JHS Completer (100%)">Public JHS Completer (100% Voucher)</option>
                  <option value="Private ESC Grantee (80%)">Private ESC Grantee (80% Voucher)</option>
                  <option value="Private Non-ESC Voucher (50%)">Private Non-ESC Voucher (50% Subsidy)</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Last School Attended *</label>
                <input v-model="form.last_school_attended" type="text" required placeholder="Name of previous school" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Previous School Type *</label>
                <select v-model="form.last_school_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-white">
                  <option value="Public">Public School</option>
                  <option value="Private">Private Institution</option>
                </select>
              </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
              <button type="button" @click="activeStep = 1" class="px-5 py-2.5 rounded-xl font-semibold text-slate-600 hover:bg-slate-100 text-sm transition">
                ← Back to Step 1
              </button>
              <button type="submit" class="px-6 py-2.5 rounded-xl font-bold bg-emerald-600 hover:bg-emerald-500 text-white text-sm shadow-md transition flex items-center space-x-2 cursor-pointer">
                <span>Save Program & Proceed to Step 3</span>
                <ArrowRight class="w-4 h-4" />
              </button>
            </div>
          </form>
        </div>

        <!-- STEP 3: DOCUMENT REQUIREMENTS & UPLOAD -->
        <div v-if="activeStep === 3" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
          <div class="border-b border-slate-100 pb-4 mb-6">
            <h2 class="text-lg font-bold text-slate-800">Step 3: Upload DepEd Admission Requirements</h2>
            <p class="text-xs text-slate-500 mt-1">Upload clear PDF or Image copies (JPG/PNG). The Registrar will evaluate authenticity.</p>
          </div>

          <div class="space-y-4">
            <div 
              v-for="req in requiredDocsList" 
              :key="req.type" 
              class="p-4 rounded-2xl border transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
              :class="getDocUploaded(req.type) ? 'border-emerald-200 bg-emerald-50/40' : (req.required ? 'border-amber-200 bg-amber-50/20' : 'border-slate-200 bg-white')"
            >
              <div class="flex items-start space-x-3.5">
                <div 
                  class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                  :class="getDocUploaded(req.type) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500'"
                >
                  <FileCheck v-if="getDocUploaded(req.type)" class="w-5 h-5" />
                  <FileText v-else class="w-5 h-5" />
                </div>
                <div>
                  <div class="flex items-center space-x-2">
                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm">{{ req.type }}</h4>
                    <span v-if="req.required" class="px-2 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800">Required</span>
                    <span v-else class="px-2 py-0.5 rounded text-[9px] font-medium bg-slate-100 text-slate-500">Optional</span>
                  </div>
                  <p class="text-[11px] text-slate-500 mt-0.5">{{ req.desc }}</p>

                  <!-- Document Verification Status if Uploaded -->
                  <div v-if="getDocUploaded(req.type)" class="mt-2 flex items-center space-x-2">
                    <span :class="getDocStatusBadge(getDocUploaded(req.type).status)" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                      {{ getDocUploaded(req.type).status }}
                    </span>
                    <span class="text-[10px] font-mono text-slate-400">
                      {{ getDocUploaded(req.type).original_filename }} ({{ ((getDocUploaded(req.type).file_size || 0) / 1024).toFixed(0) }} KB)
                    </span>
                  </div>
                </div>
              </div>

              <!-- Action Controls -->
              <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                <template v-if="getDocUploaded(req.type)">
                  <button 
                    type="button" 
                    @click="openPreviewModal(getDocUploaded(req.type))" 
                    class="px-3 py-2 rounded-xl text-xs font-bold bg-white text-emerald-700 border border-emerald-300 hover:bg-emerald-50 flex items-center space-x-1 shadow-sm transition cursor-pointer"
                  >
                    <Eye class="w-3.5 h-3.5" />
                    <span>View</span>
                  </button>

                  <label class="cursor-pointer inline-flex items-center justify-center px-3 py-2 rounded-xl text-xs font-bold bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 shadow-sm transition">
                    <Upload class="w-3.5 h-3.5 mr-1 text-slate-500" />
                    <span>Replace</span>
                    <input type="file" @change="handleFileUpload($event, req.type)" class="hidden" accept=".pdf,.png,.jpg,.jpeg,.webp" />
                  </label>

                  <button 
                    type="button" 
                    @click="handleDeleteDocument(getDocUploaded(req.type))" 
                    class="p-2 rounded-xl text-xs font-bold bg-white text-rose-700 border border-rose-200 hover:bg-rose-50 shadow-sm transition cursor-pointer"
                    title="Remove file"
                  >
                    <Trash2 class="w-4 h-4 text-rose-600" />
                  </button>
                </template>

                <template v-else>
                  <label class="cursor-pointer inline-flex items-center justify-center w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-bold bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 shadow-sm transition">
                    <Upload class="w-4 h-4 mr-1.5 text-slate-500" />
                    <span>Upload File</span>
                    <input type="file" @change="handleFileUpload($event, req.type)" class="hidden" accept=".pdf,.png,.jpg,.jpeg,.webp" />
                  </label>
                </template>
              </div>
            </div>
          </div>

          <!-- Mandatory Warning -->
          <div v-if="!hasAllMandatoryDocs" class="mt-6 p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs flex items-start space-x-2.5 shadow-sm">
            <AlertTriangle class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
            <div>
              <span class="font-bold block text-amber-950">Mandatory Documents Required:</span>
              <p class="text-[11px] text-amber-800 mt-0.5">
                Please upload clear copies of <strong class="underline">{{ missingMandatoryDocs.join(' and ') }}</strong> before submitting for Registrar review.
              </p>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row items-center justify-between pt-6 mt-6 border-t border-slate-100 gap-3">
            <button type="button" @click="activeStep = 2" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-semibold text-slate-600 hover:bg-slate-100 text-sm transition cursor-pointer">
              ← Back to Step 2
            </button>
            <button 
              @click="openSubmitReviewModal" 
              :disabled="isSubmitting || !hasAllMandatoryDocs"
              :class="[
                hasAllMandatoryDocs 
                  ? 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-md cursor-pointer' 
                  : 'bg-slate-200 text-slate-400 border border-slate-300 cursor-not-allowed'
              ]"
              class="w-full sm:w-auto px-6 py-2.5 rounded-xl font-bold text-sm transition flex items-center justify-center space-x-2"
            >
              <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              <span>Submit Requirements for Review →</span>
            </button>
          </div>
        </div>

        <!-- STEP 4: ASSESSMENT & PAYMENT (WALK-IN / ONLINE PAYMONGO) -->
        <div v-if="activeStep === 4" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl print:p-0 print:m-0 print:border-none print:shadow-none print:rounded-none">
          <!-- Action Bar (Hidden in Print) -->
          <div class="no-print flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-200 gap-4">
            <div>
              <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase tracking-wider mb-1">
                <span>Tuition Assessment & Payment Portal</span>
              </div>
              <h2 class="text-lg font-bold text-slate-800">Step 4: Official Assessment Slip & Payment Options</h2>
              <p class="text-xs text-slate-500">Choose between Walk-in Cashier Payment Ticket or Instant Online PayMongo (GCash / Maya).</p>
            </div>

            <div class="flex items-center space-x-2">
              <button @click="printSlip" class="px-4 py-2.5 rounded-xl font-bold bg-slate-900 hover:bg-slate-800 text-white text-xs shadow-md transition flex items-center space-x-1.5 cursor-pointer">
                <Printer class="w-4 h-4" />
                <span>Print Assessment Form</span>
              </button>
            </div>
          </div>

          <!-- Printable Assessment Sheet -->
          <div class="border-2 border-slate-800 p-6 print:p-3.5 rounded-2xl bg-white text-slate-900 font-sans mb-8">
            <!-- Header -->
            <div class="text-center border-b-2 border-slate-800 pb-3 mb-4">
              <div class="text-[10px] font-bold tracking-widest uppercase text-slate-600">Department of Education • Republic of the Philippines</div>
              <h2 class="text-lg font-black tracking-tight uppercase mt-0.5 text-slate-950">JJKINGS BIRINGAN SCHOOL (JHS & SHS)</h2>
              <p class="text-[11px] text-slate-600">Academic Boulevard, Biringan City, Samar • DepEd School ID: 405621</p>
              <div class="inline-block mt-2 px-3 py-0.5 rounded-full bg-slate-900 text-white text-[11px] font-extrabold font-mono uppercase tracking-wider">
                OFFICIAL ASSESSMENT & ENROLLMENT SLIP (SY 2026-2027)
              </div>
            </div>

            <!-- Student Info Matrix -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs mb-4 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
              <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Applicant / Student ID:</span>
                <span class="font-bold font-mono text-emerald-800">{{ application?.student_no || application?.assessment_info?.student_number || 'Pending Final OR' }}</span>
              </div>
              <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Student Full Name:</span>
                <span class="font-bold">{{ application?.last_name }}, {{ application?.first_name }} {{ application?.middle_name || '' }}</span>
              </div>
              <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Grade & Track / Strand:</span>
                <span class="font-bold text-slate-900">{{ application?.grade_level_name }} {{ application?.strand_code ? '(' + application.strand_code + ')' : '' }}</span>
              </div>
              <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Voucher Category:</span>
                <span class="font-bold text-emerald-700">{{ application?.voucher_status || 'None' }}</span>
              </div>
            </div>

            <!-- Fee Assessment Calculation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-mono mb-4 pt-2 border-t border-slate-200">
              <div class="space-y-1 bg-slate-50 p-3 rounded-xl border border-slate-200">
                <div class="font-bold text-slate-800 uppercase font-sans text-[11px] mb-1">DepEd Subsidy Credit:</div>
                <div class="flex justify-between">
                  <span>Gross Tuition & Fees:</span>
                  <span>₱{{ (estimatedAssessment.tuition + estimatedAssessment.misc).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
                </div>
                <div class="flex justify-between text-emerald-700 font-bold">
                  <span>Less Voucher Subsidy:</span>
                  <span>- ₱{{ estimatedAssessment.voucherDiscount.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
                </div>
              </div>

              <div class="space-y-1 bg-emerald-50 p-3 rounded-xl border border-emerald-200">
                <div class="font-bold text-emerald-950 uppercase font-sans text-[11px] mb-1">Payable Summary:</div>
                <div class="flex justify-between font-extrabold text-sm text-slate-900">
                  <span>Net Payable:</span>
                  <span>₱{{ estimatedAssessment.netPayable.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
                </div>
                <div class="flex justify-between text-emerald-800 font-bold border-t border-emerald-300 pt-1">
                  <span>Minimum Downpayment:</span>
                  <span>₱{{ estimatedAssessment.downpayment.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- PAYMENT STATUS BANNERS & WORKFLOW -->
          <div class="no-print space-y-6">

            <!-- PAYMENT LOCKED UNTIL REGISTRAR APPROVAL -->
            <div v-if="!['Approved', 'Queued for Enrollment', 'Assessed', 'Enrolled'].includes(application?.status)" class="p-8 rounded-3xl bg-amber-50 border-2 border-amber-300 text-amber-950 text-center space-y-4 my-4 shadow-sm animate-in fade-in">
              <div class="w-16 h-16 rounded-full bg-amber-100 border border-amber-300 text-amber-700 flex items-center justify-center mx-auto shadow-inner">
                <Lock class="w-8 h-8" />
              </div>
              <div class="max-w-xl mx-auto space-y-2">
                <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-amber-200 text-amber-900 text-[10px] font-extrabold uppercase tracking-wider">
                  <span>Payment Locked — Awaiting Registrar Evaluation</span>
                </div>
                <h3 class="text-lg font-black text-amber-950">Application Must Be Approved by the Registrar First</h3>
                <p class="text-xs text-amber-900/90 leading-relaxed">
                  Your admission application is currently <strong>{{ application?.status || 'Under Review' }}</strong>. In accordance with institutional enrollment policies, all uploaded credentials and LRN records must first be authenticated and approved by the Office of the Registrar before tuition downpayment can be accepted.
                </p>
              </div>
              <div class="pt-2 flex items-center justify-center space-x-3">
                <button @click="activeStep = 3" class="px-5 py-2.5 rounded-xl font-bold bg-amber-700 hover:bg-amber-600 text-white text-xs shadow-md transition inline-flex items-center space-x-1.5 cursor-pointer">
                  <span>Review Uploaded Documents (Step 3)</span>
                </button>
              </div>
            </div>

            <!-- 1. ENROLLED / PAYMENT VERIFIED STATUS -->
            <div v-else-if="isEnrolled" class="p-6 sm:p-8 rounded-3xl bg-emerald-950/90 border-2 border-emerald-500 text-white shadow-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-in fade-in duration-200">
              <div class="flex items-start space-x-3.5">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/30">
                  <CheckCircle class="w-6 h-6" />
                </div>
                <div>
                  <div class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 uppercase mb-1">
                    Payment Verified & Officially Enrolled
                  </div>
                  <h4 class="font-extrabold text-base text-white">Enrollment Confirmed ✓</h4>
                  <p class="text-xs text-emerald-200/80 mt-0.5">
                    Your downpayment has been confirmed by the Treasury. Your official Student ID and Certificate of Registration are now available.
                  </p>
                </div>
              </div>
              <button 
                @click="activeStep = 5" 
                class="px-6 py-3.5 rounded-xl font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs shadow-lg transition shrink-0 flex items-center justify-center space-x-2 cursor-pointer"
              >
                <span>View Official COR & Schedule</span>
                <ArrowRight class="w-4 h-4" />
              </button>
            </div>

            <!-- 2. PENDING TREASURY VERIFICATION STATUS (LOCKED PAYMENT STATE) -->
            <div v-else-if="isAwaitingVerification" class="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-amber-950 via-slate-900 to-amber-950 border-2 border-amber-500 text-white shadow-2xl space-y-5 animate-in fade-in duration-300">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-amber-500/30 pb-4">
                <div class="flex items-center space-x-3.5">
                  <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 border border-amber-500/40">
                    <Clock class="w-6 h-6 animate-pulse" />
                  </div>
                  <div>
                    <div class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 uppercase mb-1">
                      Awaiting Treasury Verification
                    </div>
                    <h4 class="font-extrabold text-base text-white">Online Payment Proof Submitted</h4>
                    <p class="text-xs text-amber-200/80 mt-0.5">
                      Your transaction details have been logged and sent to the Treasury Office for verification.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Transaction Summary Card -->
              <div class="p-4 rounded-2xl bg-slate-950/90 border border-amber-500/30 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-xs font-mono">
                <div>
                  <span class="block text-[10px] text-slate-400 uppercase font-sans font-bold">Payment Method:</span>
                  <strong class="text-white">{{ onlineSubmission?.payment_channel || 'GCash' }}</strong>
                </div>
                <div>
                  <span class="block text-[10px] text-slate-400 uppercase font-sans font-bold">Reference Number:</span>
                  <strong class="text-amber-400 font-bold">{{ onlineSubmission?.reference_no || application?.assessment_info?.reference_no || 'Submitted' }}</strong>
                </div>
                <div>
                  <span class="block text-[10px] text-slate-400 uppercase font-sans font-bold">Amount Submitted:</span>
                  <strong class="text-emerald-400 font-bold">₱{{ Number(onlineSubmission?.amount_submitted || estimatedAssessment.downpayment).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</strong>
                </div>
                <div>
                  <span class="block text-[10px] text-slate-400 uppercase font-sans font-bold">Status:</span>
                  <strong class="text-amber-300 uppercase">Under Verification</strong>
                </div>
              </div>

              <div v-if="onlineSubmission?.receipt_file_path" class="p-3 rounded-xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                <div class="flex items-center space-x-2 text-xs text-slate-300 truncate">
                  <FileText class="w-4 h-4 text-emerald-400 shrink-0" />
                  <span class="truncate">{{ onlineSubmission.receipt_original_name || 'Payment Receipt Proof' }}</span>
                </div>
                <button 
                  type="button"
                  @click="openPreviewModal({ file_path: onlineSubmission.receipt_file_path, original_filename: onlineSubmission.receipt_original_name, document_type: 'Payment Receipt Proof' })"
                  class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-400 font-bold text-xs flex items-center space-x-1.5 transition cursor-pointer"
                >
                  <Eye class="w-3.5 h-3.5" />
                  <span>View Proof</span>
                </button>
              </div>
            </div>

            <!-- 3. PAYMENT REJECTED / NEEDS REVIEW -->
            <div v-else-if="isPaymentFailed" class="p-6 sm:p-8 rounded-3xl bg-rose-950/90 border-2 border-rose-500 text-white shadow-2xl space-y-4 animate-in fade-in duration-200">
              <div class="flex items-start space-x-3.5 border-b border-rose-500/30 pb-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/40">
                  <AlertTriangle class="w-6 h-6" />
                </div>
                <div class="space-y-1">
                  <div class="inline-block px-3 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40 uppercase tracking-wider">
                    Payment Verification Failed – Needs Review
                  </div>
                  <h4 class="font-extrabold text-lg text-white">Payment Could Not Be Verified</h4>
                  <p class="text-xs text-rose-200/90 leading-relaxed">
                    The Treasury staff reviewed your transaction details and flagged an issue. Please read the reason below and choose how to proceed.
                  </p>
                </div>
              </div>

              <!-- Rejection Reason Card -->
              <div class="p-4 rounded-2xl bg-black/40 border border-rose-500/40 text-xs space-y-1">
                <span class="text-rose-400 font-bold uppercase tracking-wider text-[10px] block">Treasury Rejection Reason:</span>
                <p class="text-rose-100 font-medium text-sm leading-relaxed">{{ onlineSubmission?.rejection_reason || application?.rejection_reason || 'The submitted payment reference number or proof could not be verified in merchant records.' }}</p>
              </div>

              <!-- Post-Rejection Actions: Resubmit Online OR Switch to Walk-In -->
              <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                <button 
                  @click="openPaymongoModal" 
                  class="w-full sm:w-auto px-6 py-3 rounded-xl font-extrabold bg-rose-600 hover:bg-rose-500 text-white text-xs shadow-lg shadow-rose-600/30 transition flex items-center justify-center space-x-2 cursor-pointer"
                >
                  <CreditCard class="w-4 h-4" />
                  <span>Resubmit Online Payment Proof / Details</span>
                </button>
                <button 
                  @click="switchToWalkinMode" 
                  class="w-full sm:w-auto px-6 py-3 rounded-xl font-extrabold bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-700 text-xs shadow-md transition flex items-center justify-center space-x-2 cursor-pointer"
                >
                  <Building class="w-4 h-4" />
                  <span>Switch to Walk-In Cashier Payment →</span>
                </button>
              </div>
            </div>

            <!-- 4 & 5. BEFORE PAYMENT SUBMISSION (UNLOCKED / CAN SWITCH FREELY) -->
            <div v-else class="space-y-6">

              <!-- ACTIVE WALK-IN PAYMENT SLIP (IF SCHEDULED) -->
              <div v-if="isWalkinScheduled" class="p-6 sm:p-8 rounded-3xl bg-slate-900 border-2 border-indigo-500 text-white shadow-2xl space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center border border-indigo-500/30">
                      <Ticket class="w-6 h-6" />
                    </div>
                    <div>
                      <h4 class="font-extrabold text-base text-white">Official Walk-in Payment Slip</h4>
                      <p class="text-[11px] text-slate-400">Cashier Window Payment Instructions & Blue Form</p>
                    </div>
                  </div>
                  <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-950 text-indigo-300 border border-indigo-500/40 text-[10px] font-bold uppercase">
                    <Clock class="w-3.5 h-3.5" />
                    <span>Walk-in Blue Form Issued / Awaiting Payment</span>
                  </div>
                </div>

                <!-- Printable Ticket Details -->
                <div class="p-4 rounded-2xl bg-slate-950/90 border border-slate-800 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-xs font-mono">
                  <div>
                    <span class="block text-[10px] text-slate-400 uppercase font-sans font-bold">Blue Form / Ticket Number:</span>
                    <strong class="text-white text-sm font-black text-indigo-300">{{ walkinTicket?.ticket_number || application?.assessment_info?.walkin_ticket_no || application?.assessment_info?.payment_ticket || 'PAY-2026-0101' }}</strong>
                  </div>
                  <div>
                    <span class="block text-[10px] text-slate-400 uppercase font-sans font-bold">Payment Location:</span>
                    <strong class="text-emerald-400 font-bold">Main Cashier Office, Bldg A</strong>
                  </div>
                  <div>
                    <span class="block text-[10px] text-slate-400 uppercase font-sans font-bold">Campus Address:</span>
                    <strong class="text-slate-200 text-[11px]">123 Education Blvd, U-Belt, Manila</strong>
                  </div>
                </div>

                <!-- Fixed Payment Instructions Box -->
                <div class="p-4 rounded-2xl bg-indigo-950/40 border border-indigo-500/30 text-xs text-indigo-200 space-y-2">
                  <div class="flex items-center space-x-2 font-bold text-white">
                    <Building class="w-4 h-4 text-indigo-400" />
                    <span>PAYMENT - Where to Send Payment</span>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 text-[11px]">
                    <div class="p-2.5 rounded-xl bg-slate-950/80 border border-slate-800">
                      <strong class="text-blue-300 block">GCash Transfer:</strong>
                      <span class="font-mono font-bold text-white">0912-345-6789</span>
                      <span class="text-[10px] text-slate-400 block">(State Univ Cashier)</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-950/80 border border-slate-800">
                      <strong class="text-emerald-300 block">Landbank Bank Account:</strong>
                      <span class="font-mono font-bold text-white">1234-5678-90</span>
                      <span class="text-[10px] text-slate-400 block">(State Univ Trust Fund)</span>
                    </div>
                  </div>
                  <div class="p-2.5 rounded-xl bg-slate-950/80 border border-slate-800 text-[11px]">
                    <strong class="text-amber-300 block">Walk-in Payment:</strong>
                    <span class="text-slate-300">Present printed Blue Form at the Main Cashier Window.</span>
                  </div>
                </div>

                <!-- Walk-in Slip Actions: Print Slip & SWITCH TO ONLINE PAYMENT -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-800">
                  <button 
                    @click="printSlip" 
                    class="px-5 py-2.5 rounded-xl font-bold bg-indigo-600 hover:bg-indigo-500 text-white text-xs shadow-md transition flex items-center space-x-1.5 cursor-pointer"
                  >
                    <Printer class="w-4 h-4" />
                    <span>Print Walk-in Payment Slip (Blue Form)</span>
                  </button>

                  <!-- SWITCH TO ONLINE PAYMENT BUTTON (EXPLICIT SWITCH BEFORE SUBMISSION) -->
                  <button 
                    @click="openPaymongoModal" 
                    class="px-5 py-2.5 rounded-xl font-extrabold bg-emerald-600 hover:bg-emerald-500 text-white text-xs shadow-lg shadow-emerald-600/30 transition flex items-center space-x-2 cursor-pointer"
                  >
                    <CreditCard class="w-4 h-4" />
                    <span>Switch to Online Payment (Pay with PayMongo) →</span>
                  </button>
                </div>
              </div>

              <!-- PAYMENT METHOD SELECTION & SWITCHING PANEL -->
              <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-lg space-y-6">
                <div class="text-center max-w-xl mx-auto">
                  <div class="inline-block px-3 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-900 mb-2">
                    Payment Method Selection
                  </div>
                  <h3 class="text-lg font-black text-slate-900">Choose or Switch Your Payment Method</h3>
                  <p class="text-xs text-slate-500 mt-1">
                    You can choose between <strong>Online Payment via PayMongo</strong> (GCash / Maya / Card) or <strong>Walk-in Cashier Payment</strong>. You may switch between them freely before payment completion.
                  </p>
                </div>

                <!-- SIDE-BY-SIDE METHOD CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                  <!-- OPTION 1: ONLINE PAYMENT (PAYMONGO) -->
                  <div class="p-6 rounded-3xl border-2 border-emerald-500 bg-gradient-to-br from-emerald-50/60 via-teal-50/40 to-white shadow-md flex flex-col justify-between space-y-4 hover:shadow-xl transition">
                    <div class="space-y-3">
                      <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/30">
                          <CreditCard class="w-6 h-6" />
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-900 border border-emerald-300">
                          Instant Online
                        </span>
                      </div>

                      <div>
                        <h4 class="font-extrabold text-base text-slate-900">Option 1: Online Payment (PayMongo)</h4>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                          Pay securely via <strong>GCash, Maya, or Credit/Debit Card</strong>. Submit your transaction reference number and receipt for Treasury verification.
                        </p>
                      </div>

                      <div class="p-3.5 rounded-2xl bg-white/90 border border-emerald-200 text-xs font-mono space-y-1 shadow-sm">
                        <div class="flex justify-between text-slate-600">
                          <span>Minimum Downpayment:</span>
                          <strong class="text-slate-900">₱{{ estimatedAssessment.downpayment.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</strong>
                        </div>
                        <div class="flex justify-between text-slate-600">
                          <span>Supported Channels:</span>
                          <strong class="text-emerald-700">GCash • Maya • Visa/MC</strong>
                        </div>
                      </div>
                    </div>

                    <button 
                      @click="openPaymongoModal" 
                      class="w-full py-3.5 px-4 rounded-xl font-extrabold bg-emerald-600 hover:bg-emerald-500 text-white text-xs shadow-lg shadow-emerald-600/30 transition flex items-center justify-center space-x-2 cursor-pointer mt-2"
                    >
                      <CreditCard class="w-4 h-4" />
                      <span>{{ isWalkinScheduled ? 'Switch & Pay Online with PayMongo →' : 'Proceed with PayMongo Online Payment →' }}</span>
                    </button>
                  </div>

                  <!-- OPTION 2: WALK-IN CASHIER PAYMENT -->
                  <div class="p-6 rounded-3xl border-2 border-slate-200 hover:border-slate-400 bg-slate-50 transition flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                      <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-md">
                          <Building class="w-6 h-6" />
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-200 text-slate-800">
                          On-Campus Cashier
                        </span>
                      </div>

                      <div>
                        <h4 class="font-extrabold text-base text-slate-900">Option 2: Walk-In Cashier Payment</h4>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                          Settle your tuition downpayment at the <strong>Main Cashier Office</strong> on campus.
                        </p>
                      </div>

                      <!-- FIXED PAYMENT INSTRUCTIONS -->
                      <div class="p-4 rounded-2xl bg-white border border-slate-200 text-xs space-y-2.5 shadow-inner">
                        <div class="font-bold text-slate-900 uppercase tracking-wider text-[10px] border-b border-slate-100 pb-1 flex items-center space-x-1.5">
                          <CreditCard class="w-3.5 h-3.5 text-blue-700" />
                          <span>PAYMENT - Where to Send Payment:</span>
                        </div>
                        <div class="space-y-1.5 text-[11px]">
                          <div class="p-2 rounded-xl bg-blue-50/70 border border-blue-100 flex items-start justify-between">
                            <div>
                              <strong class="text-blue-950 block">GCash Transfer:</strong>
                              <span class="text-blue-800 font-mono font-bold">0912-345-6789</span>
                            </div>
                            <span class="text-[10px] text-blue-700">(State Univ Cashier)</span>
                          </div>

                          <div class="p-2 rounded-xl bg-emerald-50/70 border border-emerald-100 flex items-start justify-between">
                            <div>
                              <strong class="text-emerald-950 block">Landbank Bank Account:</strong>
                              <span class="text-emerald-800 font-mono font-bold">1234-5678-90</span>
                            </div>
                            <span class="text-[10px] text-emerald-700">(State Univ Trust Fund)</span>
                          </div>

                          <div class="p-2 rounded-xl bg-slate-100 border border-slate-200 text-slate-800">
                            <strong class="block text-slate-900">Walk-in Payment:</strong>
                            <span>Present printed Blue Form at the Main Cashier Window.</span>
                          </div>

                          <div class="p-2 rounded-xl bg-amber-50/80 border border-amber-200 text-amber-950">
                            <strong class="block text-amber-900">School Campus Address:</strong>
                            <span>Main Cashier Office, Bldg A, 123 Education Blvd, U-Belt, Manila</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <button 
                      @click="generateWalkinTicket" 
                      class="w-full py-3.5 px-4 rounded-xl font-bold bg-slate-900 hover:bg-slate-800 text-white text-xs shadow-md transition flex items-center justify-center space-x-2 cursor-pointer mt-2"
                    >
                      <Ticket class="w-4 h-4" />
                      <span>{{ isWalkinScheduled ? 'View Printed Blue Form Slip' : 'Generate Walk-In Blue Form Slip →' }}</span>
                    </button>
                  </div>

                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- STEP 5: OFFICIAL REGISTRATION & CERTIFICATE OF ENROLLMENT (COR) -->
        <div v-if="activeStep === 5" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl">
          <div class="no-print border-b border-slate-100 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase tracking-wider mb-1">
                <CheckCircle class="w-3 h-3" />
                <span>Final Academic Confirmation</span>
              </div>
              <h2 class="text-lg font-bold text-slate-800">Step 5: Official Certificate of Registration (COR) & Schedule</h2>
              <p class="text-xs text-slate-500 mt-1">Official section seating, subject timetable, and student portal credentials.</p>
            </div>

            <div class="flex items-center space-x-2">
              <button @click="printSlip" class="px-5 py-2.5 rounded-xl font-bold bg-slate-900 hover:bg-slate-800 text-white text-xs shadow-md transition flex items-center space-x-2 cursor-pointer">
                <Printer class="w-4 h-4" />
                <span>Print Official COR Form</span>
              </button>
            </div>
          </div>

          <!-- IF NOT YET PAID: PROMPT TO PAY IN STEP 4 -->
          <div v-if="application?.status !== 'Enrolled'" class="p-8 rounded-3xl bg-amber-50 border-2 border-amber-200 text-amber-950 text-center space-y-4 my-4">
            <div class="w-16 h-16 rounded-full bg-amber-100 border border-amber-300 text-amber-700 flex items-center justify-center mx-auto shadow-sm">
              <Clock class="w-8 h-8 animate-pulse" />
            </div>
            <div class="max-w-md mx-auto">
              <h3 class="text-lg font-black text-amber-950">Payment Required to Finalize Official Registration</h3>
              <p class="text-xs text-amber-900 mt-1 leading-relaxed">
                Your admission application is approved and section seating reserved. Please complete your tuition downpayment in <strong>Step 4</strong> via Walk-in Cashier or Online PayMongo to receive your permanent Student ID.
              </p>
            </div>
            <button @click="activeStep = 4" class="px-6 py-3 rounded-xl font-extrabold bg-emerald-600 hover:bg-emerald-500 text-white text-xs shadow-lg shadow-emerald-600/30 transition inline-flex items-center space-x-2 cursor-pointer">
              <span>Go to Step 4 (Assessment & Payment) →</span>
            </button>
          </div>

          <!-- IF OFFICIALLY ENROLLED: DISPLAY FULL OFFICIAL COR -->
          <div v-else class="border-2 border-slate-900 p-6 sm:p-8 rounded-2xl bg-white text-slate-900 font-sans print:p-0 print:border-none">
            <!-- Institutional Header -->
            <div class="text-center border-b-2 border-slate-900 pb-4 mb-4">
              <div class="text-[10px] font-bold uppercase tracking-widest text-slate-600">Department of Education • Region VIII (Eastern Visayas)</div>
              <h2 class="text-xl font-black uppercase text-slate-950">JJKINGS BIRINGAN SCHOOL (JHS & SHS)</h2>
              <p class="text-xs text-slate-600 font-medium">Academic Boulevard, Biringan City, Samar • DepEd ID: 405621</p>
              <div class="inline-block mt-2.5 px-4 py-1 rounded-full bg-slate-900 text-white text-xs font-black font-mono uppercase tracking-widest">
                OFFICIAL CERTIFICATE OF REGISTRATION (COR)
              </div>
            </div>

            <!-- Student Profile Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-300 text-xs font-mono mb-4">
              <div>
                <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Permanent Student ID:</span>
                <strong class="text-emerald-800 text-sm font-black">{{ application?.student_no || application?.assessment_info?.student_number }}</strong>
              </div>
              <div>
                <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Student Full Name:</span>
                <strong class="text-slate-900">{{ application?.last_name }}, {{ application?.first_name }} {{ application?.middle_name || '' }}</strong>
              </div>
              <div>
                <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Assigned Class Section:</span>
                <strong class="text-blue-800">{{ application?.queue_info?.section_name || 'Grade 11 - STEM A' }}</strong>
              </div>
              <div>
                <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Classroom / Wing:</span>
                <strong class="text-slate-900">{{ application?.queue_info?.room || 'Science Wing - Room 501' }}</strong>
              </div>
            </div>

            <!-- Subject Timetable -->
            <div class="mb-4">
              <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">Curriculum Learning Areas & Class Timetable</h4>
              <table class="w-full text-xs text-left border-collapse border border-slate-300">
                <thead>
                  <tr class="bg-slate-100 text-slate-800 font-bold border-b border-slate-300">
                    <th class="p-2 border-r border-slate-300 font-mono">Code</th>
                    <th class="p-2 border-r border-slate-300">Descriptive Learning Area</th>
                    <th class="p-2 border-r border-slate-300 text-center">Units</th>
                    <th class="p-2 border-r border-slate-300">Schedule Day & Time</th>
                    <th class="p-2">Assigned Room</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                  <tr v-for="sub in sampleSubjects" :key="sub.code">
                    <td class="p-2 font-mono font-bold border-r border-slate-200 text-emerald-800">{{ sub.code }}</td>
                    <td class="p-2 font-semibold border-r border-slate-200">{{ sub.title }}</td>
                    <td class="p-2 text-center font-bold border-r border-slate-200">{{ sub.units }}</td>
                    <td class="p-2 border-r border-slate-200 font-mono text-[11px] text-slate-600">Mon & Thu 08:00 AM - 09:30 AM</td>
                    <td class="p-2 font-mono text-[11px]">{{ application?.queue_info?.room || 'Science Wing 501' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Official Seal & Signatures -->
            <div class="grid grid-cols-3 gap-6 text-center text-xs pt-6 mt-6 border-t-2 border-slate-300">
              <div>
                <div class="border-b border-slate-900 pb-1 font-bold text-slate-900">{{ application?.first_name }} {{ application?.last_name }}</div>
                <span class="text-[9px] text-slate-500 uppercase">Student Enrollee</span>
              </div>
              <div>
                <div class="border-b border-slate-900 pb-1 font-bold text-slate-900">Office of the Registrar</div>
                <span class="text-[9px] text-slate-500 uppercase">Evaluated & Registered</span>
              </div>
              <div>
                <div class="border-b border-slate-900 pb-1 font-bold text-slate-900">Finance & Treasury</div>
                <span class="text-[9px] text-slate-500 uppercase">Official Receipt Settle</span>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- SUBMIT FOR REVIEW CONFIRMATION MODAL -->
    <div v-if="showSubmitReviewConfirm" class="no-print fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150 text-slate-900">
        <div class="w-12 h-12 rounded-2xl bg-emerald-100 border border-emerald-200 text-emerald-600 flex items-center justify-center mx-auto mb-4 shadow-sm">
          <UploadCloud class="w-6 h-6" />
        </div>
        <div class="text-center">
          <h3 class="text-base font-extrabold text-slate-900">Submit Requirements for Review?</h3>
          <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
            Your uploaded documents will be submitted to the Office of the Registrar for official authenticity and LRN validation.
          </p>
        </div>

        <!-- Checklist of Uploaded Docs -->
        <div class="my-4 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
          <div class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">Attached Requirements:</div>
          <div v-for="req in requiredDocsList" :key="req.type" class="flex items-center justify-between text-[11px]">
            <span class="text-slate-600">{{ req.type }}:</span>
            <span v-if="getDocUploaded(req.type)" class="text-emerald-700 font-bold flex items-center space-x-1">
              <Check class="w-3.5 h-3.5" />
              <span>Attached</span>
            </span>
            <span v-else class="text-slate-400 italic">Not Uploaded</span>
          </div>
        </div>

        <div class="flex items-center space-x-2.5 mt-6">
          <button 
            type="button" 
            @click="showSubmitReviewConfirm = false" 
            class="w-1/2 py-2.5 px-4 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer"
          >
            Review Files Again
          </button>
          <button 
            type="button" 
            @click="confirmSubmitForEvaluation" 
            :disabled="isSubmitting"
            class="w-1/2 py-2.5 px-4 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white shadow-md transition flex items-center justify-center space-x-1.5 cursor-pointer"
          >
            <span v-if="isSubmitting" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span v-else>Confirm & Submit</span>
          </button>
        </div>
      </div>
    </div>

    <!-- PAYMONGO ONLINE PAYMENT CHECKOUT MODAL -->
    <div v-if="showPaymongoModal" class="no-print fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-200 text-slate-900 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black">
              PM
            </div>
            <div>
              <h3 class="font-extrabold text-base text-slate-900">PayMongo Online Payment</h3>
              <p class="text-[11px] text-slate-500">Secure E-Wallet & Card Payment Submission</p>
            </div>
          </div>
          <button @click="showPaymongoModal = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center font-bold text-slate-500 transition cursor-pointer">
            ✕
          </button>
        </div>

        <!-- Payment Form -->
        <form @submit.prevent="submitOnlinePayment" class="space-y-4 text-xs">
          <!-- Error alert inside modal -->
          <div v-if="paymongoError" class="p-3 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-xs flex items-center justify-between animate-in fade-in duration-150">
            <div class="flex items-center space-x-2">
              <AlertCircle class="w-4 h-4 text-rose-600 shrink-0" />
              <span>{{ paymongoError }}</span>
            </div>
            <button type="button" @click="paymongoError = ''" class="text-rose-600 font-bold ml-2 hover:text-rose-900">✕</button>
          </div>

          <!-- Channel selector -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">Select Payment Channel *</label>
            <div class="grid grid-cols-3 gap-2">
              <button 
                type="button" 
                @click="paymongoForm.channel = 'GCash'"
                :class="paymongoForm.channel === 'GCash' ? 'bg-blue-600 text-white font-bold border-blue-600 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                class="p-2.5 rounded-xl border text-center transition cursor-pointer"
              >
                GCash
              </button>
              <button 
                type="button" 
                @click="paymongoForm.channel = 'Maya'"
                :class="paymongoForm.channel === 'Maya' ? 'bg-emerald-600 text-white font-bold border-emerald-600 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                class="p-2.5 rounded-xl border text-center transition cursor-pointer"
              >
                Maya
              </button>
              <button 
                type="button" 
                @click="paymongoForm.channel = 'Card'"
                :class="paymongoForm.channel === 'Card' ? 'bg-slate-900 text-white font-bold border-slate-900 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                class="p-2.5 rounded-xl border text-center transition cursor-pointer"
              >
                Visa / MC
              </button>
            </div>
          </div>

          <!-- Transaction Reference Number / ID -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">
              {{ paymongoForm.channel }} Payment Reference Number / Transaction ID *
            </label>
            <input 
              v-model="paymongoForm.reference_no" 
              type="text" 
              required 
              :placeholder="paymongoForm.channel === 'GCash' ? 'e.g. 102938475611 (10-13 digits)' : (paymongoForm.channel === 'Maya' ? 'e.g. 9823471029' : 'e.g. TXN-CARD-847294')" 
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500" 
            />
            <span class="text-[10px] text-slate-500 block mt-0.5">Found on your {{ paymongoForm.channel }} confirmation SMS or e-receipt.</span>
          </div>

          <!-- Account Name & Phone -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Sender Account Name</label>
              <input 
                v-model="paymongoForm.account_name" 
                type="text" 
                placeholder="Juan Dela Cruz"
                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-emerald-500" 
              />
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Sender Mobile / Card No.</label>
              <input 
                v-model="paymongoForm.mobile" 
                type="text" 
                placeholder="0995 244 2435" 
                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-emerald-500" 
              />
            </div>
          </div>

          <!-- Amount to Settle -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block font-bold text-slate-700">Amount Settle (PHP) *</label>
              <button 
                type="button" 
                @click="paymongoForm.amount = estimatedAssessment.netPayable" 
                class="text-[10px] text-emerald-700 font-bold underline cursor-pointer"
              >
                Pay Full ₱{{ estimatedAssessment.netPayable.toLocaleString('en-US', {minimumFractionDigits: 2}) }}
              </button>
            </div>
            <input 
              v-model.number="paymongoForm.amount" 
              type="number" 
              step="any" 
              :min="Math.min(estimatedAssessment.downpayment, estimatedAssessment.netPayable)"
              :max="estimatedAssessment.netPayable"
              required 
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-black text-emerald-800 text-sm focus:ring-2 focus:ring-emerald-500" 
            />
            <div class="flex items-center justify-between text-[10px] mt-1">
              <span class="text-slate-500">Min: ₱{{ Math.min(estimatedAssessment.downpayment, estimatedAssessment.netPayable).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
              <span class="text-emerald-700 font-bold">Max (Full Balance): ₱{{ estimatedAssessment.netPayable.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
            </div>
          </div>

          <!-- Proof of Payment / Receipt File Upload -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block font-bold text-slate-700">
                Upload Payment Receipt Proof (JPG, PNG, PDF) *
              </label>
              <span class="text-[10px] text-emerald-700 font-bold">Required for Verification</span>
            </div>
            <input 
              type="file" 
              accept=".png,.jpg,.jpeg,.pdf"
              @change="handleReceiptFileChange"
              class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500 cursor-pointer"
            />
            <div v-if="paymongoForm.receipt_file" class="text-[11px] text-emerald-700 font-bold mt-1 flex items-center space-x-1">
              <CheckCircle class="w-3.5 h-3.5" />
              <span>Attached: {{ paymongoForm.receipt_file.name }} ({{ (paymongoForm.receipt_file.size / 1024).toFixed(1) }} KB)</span>
            </div>
            <span v-else class="text-[10px] text-slate-500 block mt-0.5">
              Upload your GCash / Maya confirmation receipt or e-wallet screenshot.
            </span>
          </div>

          <!-- Notice Box -->
          <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 space-y-1">
            <div class="font-bold flex items-center space-x-1 text-amber-800">
              <Clock class="w-3.5 h-3.5 shrink-0" />
              <span>Treasury Verification Notice:</span>
            </div>
            <p class="leading-relaxed text-amber-800/90 text-[10px]">
              Upon submitting, your transaction will be placed in the Treasury Verification Queue. Authorized finance staff will verify the reference number and amount before issuing your Official Student ID and COR.
            </p>
          </div>

          <button 
            type="submit" 
            :disabled="isPayingOnline" 
            class="w-full py-3.5 px-4 rounded-xl font-black bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white text-xs shadow-lg shadow-emerald-600/30 transition flex items-center justify-center space-x-2 cursor-pointer mt-2"
          >
            <span v-if="isPayingOnline" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span v-else class="flex items-center space-x-1.5">
              <span>Submit Payment (₱{{ Number(paymongoForm.amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}) →</span>
            </span>
          </button>
        </form>
      </div>
    </div>

    <!-- DOCUMENT PREVIEW POP-UP MODAL (PDF & IMAGE VIEWER) -->
    <div v-if="previewDoc" class="no-print fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="p-4 sm:px-6 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
          <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-950 border border-emerald-500/30 text-emerald-400 flex items-center justify-center shrink-0">
              <FileText class="w-5 h-5" />
            </div>
            <div>
              <h3 class="font-bold text-sm text-white">{{ previewDoc.document_type }}</h3>
              <p class="text-[11px] text-slate-400 font-mono">{{ previewDoc.original_filename }}</p>
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <a 
              :href="getFileUrl(previewDoc.file_path)" 
              target="_blank" 
              download
              class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center space-x-1.5 transition"
            >
              <Download class="w-3.5 h-3.5" />
              <span>Download</span>
            </a>
            <button 
              @click="previewDoc = null" 
              class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center font-bold transition cursor-pointer"
            >
              ✕
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="flex-1 p-4 sm:p-6 overflow-y-auto bg-slate-100 flex items-center justify-center min-h-[400px]">
          <iframe 
            v-if="isPdf(previewDoc.file_path)" 
            :src="getFileUrl(previewDoc.file_path)" 
            class="w-full h-[70vh] rounded-xl border border-slate-300 bg-white shadow-inner"
          ></iframe>
          <div v-else class="max-h-[70vh] overflow-auto flex items-center justify-center">
            <img 
              :src="getFileUrl(previewDoc.file_path)" 
              :alt="previewDoc.document_type" 
              class="max-w-full max-h-[68vh] rounded-xl shadow-lg object-contain border border-slate-300 bg-white"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { 
  CheckCircle, AlertCircle, FileText, Upload, Check, Printer, Eye, Trash2, Download,
  AlertTriangle, Clock, ArrowRight, FileCheck, GraduationCap, CreditCard,
  Lock, ChevronRight, User, BookOpen, UploadCloud, Activity, Building, Ticket
} from 'lucide-vue-next';
import api, { getFileUrl } from '../../services/api';

const router = useRouter();
const activeStep = ref(1);
const stepLockNotice = ref('');
const application = ref(null);
const academicOptions = ref({ grade_levels: [], tracks: [], strands: [] });
const isLoading = ref(false);
const isSubmitting = ref(false);
const isPayingOnline = ref(false);
const showPaymongoModal = ref(false);
const paymongoError = ref('');
const walkinTicket = ref(null);
const successMessage = ref('');
const errorMessage = ref('');

const walkinForm = ref({
  scheduled_date: '',
  time_slot: '08:00 AM - 11:30 AM (Morning Batch)'
});

const paymongoForm = ref({
  channel: 'GCash',
  reference_no: '',
  account_name: '',
  mobile: '',
  amount: 3000,
  receipt_file: null
});

const onlineSubmission = computed(() => application.value?.online_payment_submission || null);

const isEnrolled = computed(() => {
  return application.value?.status === 'Enrolled' || application.value?.assessment_info?.enrollment_status === 'Officially Enrolled';
});

const isAwaitingVerification = computed(() => {
  if (isEnrolled.value) return false;
  return application.value?.status === 'Payment Submitted – Awaiting Verification' || 
         onlineSubmission.value?.status === 'Pending Verification';
});

const isPaymentFailed = computed(() => {
  if (isEnrolled.value || isAwaitingVerification.value) return false;
  return application.value?.status === 'Payment Verification Failed' || 
         onlineSubmission.value?.status === 'Rejected';
});

const isWalkinScheduled = computed(() => {
  if (isEnrolled.value || isAwaitingVerification.value || isPaymentFailed.value) return false;
  return application.value?.status === 'Walk-in Payment Scheduled' || 
         !!walkinTicket.value || 
         !!application.value?.assessment_info?.walkin_ticket_no;
});

const handleReceiptFileChange = (e) => {
  const file = e.target.files?.[0];
  if (file) {
    paymongoForm.value.receipt_file = file;
  }
};

const switchToWalkinMode = async () => {
  if (isAwaitingVerification.value) return;
  errorMessage.value = '';
  try {
    await api.switchPaymentMode({ payment_mode: 'walkin' });
    await loadData();
    successMessage.value = 'Switched to Walk-In Cashier Payment mode.';
  } catch (err) {
    errorMessage.value = err.message || 'Failed to switch payment mode.';
  }
};

const switchToOnlineMode = async () => {
  if (isAwaitingVerification.value) return;
  errorMessage.value = '';
  try {
    await api.switchPaymentMode({ payment_mode: 'online' });
    await loadData();
    openPaymongoModal();
  } catch (err) {
    errorMessage.value = err.message || 'Failed to switch payment mode.';
  }
};

const goToStudentLogin = () => {
  localStorage.removeItem('sia_auth_token');
  localStorage.removeItem('sia_auth_user');
  window.dispatchEvent(new Event('auth-changed'));
  router.push('/login');
};

const deficientDocs = computed(() => {
  return application.value?.documents?.filter(d => d.status === 'Deficient' || d.status === 'Rejected') || [];
});

const steps = [
  { id: 1, title: 'Personal Demographics', subtitle: 'LRN, Name & Contact' },
  { id: 2, title: 'Grade Level & Strand', subtitle: 'Academic Program Selection' },
  { id: 3, title: 'Send Requirements', subtitle: 'Upload PSA, SF9 & Credentials' },
  { id: 4, title: 'Assessment & Payment', subtitle: 'Walk-in Ticket / Online PayMongo' },
  { id: 5, title: 'Official Registration (COR)', subtitle: 'Permanent ID, Section & Schedule' }
];

const normalizeDocName = (t) => {
  if (!t) return '';
  const trimmed = t.trim();
  if (trimmed === 'Certificate of Good Moral' || trimmed === 'Good Moral Certificate') return 'Certificate of Good Moral Character';
  if (trimmed === '2x2 ID Photo' || trimmed === 'ID Photo' || trimmed === 'ID Picture') return '2x2 ID Picture';
  if (trimmed === 'JHS Completion Certificate') return 'Certificate of JHS Completion';
  return trimmed;
};

const requiredDocsList = computed(() => {
  const glId = application.value?.grade_level_id || form.value.grade_level_id;
  const gl = academicOptions.value?.grade_levels?.find(g => g.id === glId);
  const isSHSLevel = gl?.category === 'SHS' || (glId >= 5);
  const isTransferee = (application.value?.applicant_type || form.value.applicant_type) === 'Transferee';
  const voucher = application.value?.voucher_status || form.value.voucher_status;
  const hasVoucher = isSHSLevel && voucher && voucher !== 'None';

  const list = [
    { type: 'PSA Birth Certificate', desc: 'Original Philippine Statistics Authority (PSA) copy', required: true },
    { type: 'SF9 / Form 138 (Report Card)', desc: 'Previous grade report card signed by Principal', required: true },
    { type: 'Certificate of Good Moral Character', desc: 'Issued by Guidance Counselor or Principal', required: true },
    { type: '2x2 ID Picture', desc: 'Recent formal photo with white background', required: true }
  ];

  if (isSHSLevel && !isTransferee) {
    list.push({ type: 'Certificate of JHS Completion', desc: 'Official Junior High School Completion Diploma / Certificate', required: true });
  }

  if (isTransferee) {
    list.push({ type: 'Certificate of Transfer Credential / Honorable Dismissal', desc: 'Official Transfer Credential / Honorable Dismissal from previous school', required: true });
  }

  if (hasVoucher) {
    list.push({ type: 'ESC Certificate / Voucher Cert', desc: 'DepEd PEAC ESC Certificate or Qualified Voucher Applicant (QVR) Cert', required: true });
  }

  return list;
});

const mandatoryDocs = computed(() => {
  return requiredDocsList.value.filter(r => r.required).map(r => r.type);
});

const hasAllMandatoryDocs = computed(() => {
  const uploaded = (application.value?.documents || [])
    .filter(d => d.status !== 'Rejected' && d.status !== 'Deficient')
    .map(d => normalizeDocName(d.document_type));
  return mandatoryDocs.value.length > 0 && mandatoryDocs.value.every(m => uploaded.includes(normalizeDocName(m)));
});

const missingMandatoryDocs = computed(() => {
  const uploaded = (application.value?.documents || [])
    .filter(d => d.status !== 'Rejected' && d.status !== 'Deficient')
    .map(d => normalizeDocName(d.document_type));
  return mandatoryDocs.value.filter(m => !uploaded.includes(normalizeDocName(m)));
});

const isStep1Completed = computed(() => {
  return !!(application.value?.lrn && application.value?.first_name && application.value?.last_name);
});

const isStep2Completed = computed(() => {
  if (!isStep1Completed.value) return false;
  if (!application.value?.grade_level_id) return false;
  const gl = academicOptions.value.grade_levels.find(g => g.id === application.value.grade_level_id);
  const isSHSLevel = gl?.category === 'SHS' || (application.value.grade_level_id >= 5);
  if (isSHSLevel) {
    return !!application.value?.strand_id;
  }
  return true;
});

const isStep3Completed = computed(() => {
  if (!isStep2Completed.value) return false;
  const status = application.value?.status;
  return hasAllMandatoryDocs.value && status && !['Draft', 'Pending'].includes(status);
});

const isStep4Completed = computed(() => {
  return application.value?.status === 'Enrolled';
});

const isStep5Completed = computed(() => {
  return application.value?.status === 'Enrolled';
});

const isStepDone = (stepId) => {
  if (stepId === 1) return isStep1Completed.value;
  if (stepId === 2) return isStep2Completed.value;
  if (stepId === 3) return isStep3Completed.value;
  if (stepId === 4) return isStep4Completed.value;
  if (stepId === 5) return isStep5Completed.value;
  return false;
};

const canAccessStep = (stepNumber) => {
  if (stepNumber === 1) return true;
  if (stepNumber === 2) return isStep1Completed.value;
  if (stepNumber === 3) return isStep1Completed.value && isStep2Completed.value;
  if (stepNumber === 4) return isStep3Completed.value;
  if (stepNumber === 5) return isStep4Completed.value;
  return false;
};

const selectStep = (stepNumber) => {
  if (canAccessStep(stepNumber)) {
    activeStep.value = stepNumber;
    stepLockNotice.value = '';
  } else {
    if (stepNumber === 2) {
      stepLockNotice.value = 'Please complete Demographics (Step 1) first.';
    } else if (stepNumber === 3) {
      stepLockNotice.value = 'Please select your Grade Level & Strand (Step 2) first.';
    } else if (stepNumber === 4) {
      if (!hasAllMandatoryDocs.value) {
        stepLockNotice.value = `Please upload mandatory documents (${missingMandatoryDocs.value.join(' and ')}) in Step 3 first.`;
      } else {
        stepLockNotice.value = 'Please submit your requirements for evaluation in Step 3.';
      }
    } else if (stepNumber === 5) {
      if (application.value?.status === 'Payment Submitted – Awaiting Verification') {
        stepLockNotice.value = 'Your payment is currently awaiting Treasury verification. Step 5 will unlock automatically once approved.';
      } else if (application.value?.status === 'Walk-in Payment Scheduled') {
        stepLockNotice.value = 'Please settle your downpayment at the Treasury Cashier (Windows 1 & 2) to unlock Step 5.';
      } else {
        stepLockNotice.value = 'Step 5 (Official Registration & COR) is locked until your downpayment is verified and confirmed by the Treasury.';
      }
    }
  }
};

const getSidebarStepClass = (stepId) => {
  if (activeStep.value === stepId) {
    return 'bg-slate-900 text-white border-2 border-emerald-500 shadow-md ring-2 ring-emerald-500/20';
  }
  if (isStepDone(stepId)) {
    return 'bg-white text-slate-800 border border-emerald-200 hover:bg-emerald-50/40 hover:border-emerald-300 cursor-pointer shadow-sm';
  }
  if (!canAccessStep(stepId)) {
    return 'bg-slate-50 text-slate-400 border border-slate-200 opacity-60 cursor-not-allowed';
  }
  return 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 cursor-pointer';
};

const getSidebarIconClass = (stepId) => {
  if (activeStep.value === stepId) {
    return 'bg-emerald-500 text-slate-950 shadow-sm';
  }
  if (isStepDone(stepId)) {
    return 'bg-emerald-100 text-emerald-800';
  }
  if (!canAccessStep(stepId)) {
    return 'bg-slate-200 text-slate-400';
  }
  return 'bg-slate-100 text-slate-700';
};

const getStepSubtitle = (stepId) => {
  if (activeStep.value === stepId) return 'Currently Working';
  if (stepId === 1) return isStep1Completed.value ? 'Completed ✓' : 'Ready to Start';
  if (stepId === 2) return isStep2Completed.value ? 'Completed ✓' : (!canAccessStep(2) ? 'Locked 🔒' : 'Ready to Start');
  if (stepId === 3) return isStep3Completed.value ? 'Submitted ✓' : (!canAccessStep(3) ? 'Locked 🔒' : (hasAllMandatoryDocs.value ? 'Ready to Submit' : 'Uploads Incomplete'));
  if (stepId === 4) return isStep3Completed.value ? (isStep4Completed.value ? 'Assessment Ready ✓' : 'Pre-Assessment') : 'Locked 🔒';
  if (stepId === 5) {
    if (!isStep3Completed.value) return 'Locked 🔒';
    if (application.value?.status === 'Enrolled') return 'Enrolled ✓';
    return 'Pending Payment';
  }
  return 'Ready to Start';
};

const form = ref({
  applicant_type: 'New Student',
  lrn: '',
  first_name: '',
  middle_name: '',
  last_name: '',
  suffix: '',
  gender: 'Male',
  birthdate: '2010-01-01',
  birthplace: '',
  contact_number: '',
  email: '',
  address_barangay: '',
  address_city: '',
  address_province: '',
  guardian_name: '',
  guardian_relationship: '',
  guardian_contact: '',
  grade_level_id: null,
  track_id: null,
  strand_id: null,
  voucher_status: 'None',
  last_school_attended: '',
  last_school_type: 'Public'
});

const blockNonNumeric = (e) => {
  if (
    ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'].includes(e.key) ||
    (e.ctrlKey || e.metaKey)
  ) {
    return;
  }
  if (!/^[0-9]$/.test(e.key)) {
    e.preventDefault();
  }
};

const blockNonAlphabetic = (e) => {
  if (
    ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End', ' '].includes(e.key) ||
    (e.ctrlKey || e.metaKey)
  ) {
    return;
  }
  if (/^[0-9]$/.test(e.key) || !/^[a-zA-ZñÑ\-\.\']$/.test(e.key)) {
    e.preventDefault();
  }
};

const handleNumericInput = (field, e, maxLen = null) => {
  let val = (e.target.value || '').replace(/\D/g, '');
  if (maxLen && val.length > maxLen) {
    val = val.slice(0, maxLen);
  }
  form.value[field] = val;
  e.target.value = val;
};

const handleAlphabeticInput = (field, e) => {
  let val = (e.target.value || '').replace(/[^a-zA-ZñÑ\s\-\.\']/g, '');
  form.value[field] = val;
  e.target.value = val;
};

// Reactive sanitizers (runs on every reactive state change)
watch(() => form.value.lrn, (v) => { if (v && /\D/.test(v)) form.value.lrn = v.replace(/\D/g, '').slice(0, 12); });
watch(() => form.value.contact_number, (v) => { if (v && /\D/.test(v)) form.value.contact_number = v.replace(/\D/g, '').slice(0, 11); });
watch(() => form.value.guardian_contact, (v) => { if (v && /\D/.test(v)) form.value.guardian_contact = v.replace(/\D/g, '').slice(0, 11); });
watch(() => form.value.first_name, (v) => { if (v && /[^a-zA-ZñÑ\s\-\.\']/g.test(v)) form.value.first_name = v.replace(/[^a-zA-ZñÑ\s\-\.\']/g, ''); });
watch(() => form.value.middle_name, (v) => { if (v && /[^a-zA-ZñÑ\s\-\.\']/g.test(v)) form.value.middle_name = v.replace(/[^a-zA-ZñÑ\s\-\.\']/g, ''); });
watch(() => form.value.last_name, (v) => { if (v && /[^a-zA-ZñÑ\s\-\.\']/g.test(v)) form.value.last_name = v.replace(/[^a-zA-ZñÑ\s\-\.\']/g, ''); });
watch(() => form.value.suffix, (v) => { if (v && /[^a-zA-ZñÑ\s\-\.\']/g.test(v)) form.value.suffix = v.replace(/[^a-zA-ZñÑ\s\-\.\']/g, ''); });
watch(() => form.value.guardian_name, (v) => { if (v && /[^a-zA-ZñÑ\s\-\.\']/g.test(v)) form.value.guardian_name = v.replace(/[^a-zA-ZñÑ\s\-\.\']/g, ''); });
watch(() => form.value.guardian_relationship, (v) => { if (v && /[^a-zA-ZñÑ\s\-\.\']/g.test(v)) form.value.guardian_relationship = v.replace(/[^a-zA-ZñÑ\s\-\.\']/g, ''); });

const minBirthdate = computed(() => {
  const d = new Date();
  d.setFullYear(d.getFullYear() - 40);
  return d.toISOString().split('T')[0];
});

const maxBirthdate = computed(() => {
  const d = new Date();
  d.setFullYear(d.getFullYear() - 11);
  return d.toISOString().split('T')[0];
});

const isSHS = computed(() => {
  const gl = academicOptions.value.grade_levels.find(g => g.id === form.value.grade_level_id);
  return gl?.category === 'SHS' || (form.value.grade_level_id && form.value.grade_level_id >= 5);
});

const jhsLevels = computed(() => {
  return academicOptions.value.grade_levels.filter(g => g.category === 'JHS' || g.id <= 4);
});

const shsLevels = computed(() => {
  return academicOptions.value.grade_levels.filter(g => g.category === 'SHS' || g.id >= 5);
});

const filteredStrands = computed(() => {
  if (!form.value.track_id) return academicOptions.value.strands;
  return academicOptions.value.strands.filter(s => s.track_id === form.value.track_id);
});

const statusBadgeClass = computed(() => {
  const s = application.value?.status;
  if (s === 'Enrolled') return 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40';
  if (['Approved', 'Queued for Enrollment', 'Assessed'].includes(s)) return 'bg-blue-500/20 text-blue-300 border border-blue-500/40';
  if (s === 'Under Review') return 'bg-amber-500/20 text-amber-300 border border-amber-500/40';
  if (s === 'Requirements Deficient' || s === 'Deficient') return 'bg-rose-500/20 text-rose-300 border border-rose-500/40';
  return 'bg-slate-800 text-slate-300 border border-slate-700';
});

const getDocUploaded = (docType) => {
  const target = normalizeDocName(docType);
  return application.value?.documents?.find(d => normalizeDocName(d.document_type) === target);
};

const getDocStatusBadge = (status) => {
  if (status === 'Verified') return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
  if (status === 'Deficient' || status === 'Rejected') return 'bg-rose-600 text-white shadow-sm';
  return 'bg-amber-100 text-amber-800 border border-amber-300';
};

const sampleSubjects = computed(() => {
  const glId = application.value?.grade_level_id || form.value.grade_level_id || 1;
  const strandId = application.value?.strand_id || form.value.strand_id;
  const allSubs = academicOptions.value?.subjects || [];

  if (allSubs.length > 0) {
    if (isSHS.value) {
      const filtered = allSubs.filter(sub => {
        const matchesGl = sub.grade_level_id === glId;
        const matchesSem = sub.semester === '1st Semester';
        const matchesStrand = !sub.strand_id || sub.strand_id === strandId;
        return matchesGl && matchesSem && matchesStrand;
      });
      if (filtered.length > 0) return filtered;
    } else {
      const filtered = allSubs.filter(sub => sub.grade_level_id === glId);
      if (filtered.length > 0) return filtered;
    }
  }

  if (isSHS.value) {
    return [
      { code: 'SHS-ORAL', title: 'Oral Communication in Context', category: 'SHS Core', units: '1.0' },
      { code: 'SHS-GENMATH', title: 'General Mathematics', category: 'SHS Core', units: '1.0' },
      { code: 'SHS-ELS', title: 'Earth and Life Science', category: 'SHS Core', units: '1.0' },
      { code: 'SHS-EAPP', title: 'English for Academic & Professional Purposes', category: 'SHS Applied', units: '1.0' },
      { code: 'SHS-EMPTECH', title: 'Empowerment Technologies (ICT)', category: 'SHS Applied', units: '1.0' }
    ];
  }
  return [
    { code: 'ENG-7', title: 'English 7 (Philippine Literature)', category: 'JHS Core', units: '1.0' },
    { code: 'FIL-7', title: 'Filipino 7 (Ibong Adarna)', category: 'JHS Core', units: '1.0' },
    { code: 'MATH-7', title: 'Mathematics 7 (Algebra & Numbers)', category: 'JHS Core', units: '1.0' },
    { code: 'SCI-7', title: 'Science 7 (Integrated Science)', category: 'JHS Core', units: '1.0' },
    { code: 'AP-7', title: 'Araling Panlipunan 7 (Araling Asyano)', category: 'JHS Core', units: '1.0' }
  ];
});

const estimatedAssessment = computed(() => {
  if (application.value?.assessment_info) {
    const info = application.value.assessment_info;
    const tuition = parseFloat(info.total_tuition || 0) || 12500;
    const misc = (parseFloat(info.total_miscellaneous || 0) + parseFloat(info.total_laboratory || 0) + parseFloat(info.total_other_fees || 0)) || 4500;
    const voucherDiscount = parseFloat(info.voucher_discount || 0);
    const netPayable = parseFloat(info.net_payable || (tuition + misc - voucherDiscount));
    const downpayment = parseFloat(info.minimum_downpayment || Math.min(3000, netPayable));
    return {
      tuition,
      misc,
      voucherDiscount,
      netPayable,
      downpayment
    };
  }

  const tuition = 12500;
  const misc = 4500;
  const gross = tuition + misc;
  let voucherDiscount = 0;

  const vStatus = application.value?.voucher_status || form.value.voucher_status;
  const gl = academicOptions.value.grade_levels.find(g => g.id === (application.value?.grade_level_id || form.value.grade_level_id));
  const isSHSLevel = gl?.category === 'SHS' || (application.value?.grade_level_id >= 5) || (form.value.grade_level_id >= 5);

  if (isSHSLevel) {
    if (vStatus === 'Public JHS Completer (100%)') {
      voucherDiscount = 12500;
    } else if (vStatus === 'Private ESC Grantee (80%)') {
      voucherDiscount = 10000;
    } else if (vStatus === 'Private Non-ESC Voucher (50%)') {
      voucherDiscount = 6250;
    }
  }

  const netPayable = Math.max(0, gross - voucherDiscount);

  return {
    tuition,
    misc,
    voucherDiscount,
    netPayable,
    downpayment: Math.min(3000, netPayable)
  };
});

const loadData = async () => {
  isLoading.value = true;
  try {
    const [appRes, optRes] = await Promise.all([
      api.getMyApplication(),
      api.getAcademicOptions()
    ]);

    application.value = appRes.data;
    academicOptions.value = optRes.data;

    // Populate form
    Object.assign(form.value, {
      applicant_type: appRes.data.applicant_type || 'New Student',
      lrn: appRes.data.lrn || '',
      first_name: appRes.data.first_name || '',
      middle_name: appRes.data.middle_name || '',
      last_name: appRes.data.last_name || '',
      suffix: appRes.data.suffix || '',
      gender: appRes.data.gender || 'Male',
      birthdate: appRes.data.birthdate || '2010-01-01',
      birthplace: appRes.data.birthplace || '',
      contact_number: appRes.data.contact_number || '',
      email: appRes.data.email || '',
      address_barangay: appRes.data.address_barangay || '',
      address_city: appRes.data.address_city || '',
      address_province: appRes.data.address_province || '',
      guardian_name: appRes.data.guardian_name || '',
      guardian_relationship: appRes.data.guardian_relationship || '',
      guardian_contact: appRes.data.guardian_contact || '',
      grade_level_id: appRes.data.grade_level_id || null,
      track_id: appRes.data.track_id || null,
      strand_id: appRes.data.strand_id || null,
      voucher_status: appRes.data.voucher_status || 'None',
      last_school_attended: appRes.data.last_school_attended || '',
      last_school_type: appRes.data.last_school_type || 'Public'
    });

    // Auto-populate pre-selected strand if applicant enrolled from HomeView modal
    const savedStrandJson = sessionStorage.getItem('selected_enroll_strand') || localStorage.getItem('selected_enroll_strand');
    if (savedStrandJson) {
      try {
        const savedStrand = JSON.parse(savedStrandJson);
        if (savedStrand.grade_level_id) form.value.grade_level_id = savedStrand.grade_level_id;
        if (savedStrand.track_id) form.value.track_id = savedStrand.track_id;
        if (savedStrand.strand_id) form.value.strand_id = savedStrand.strand_id;
      } catch (e) {
        // Ignore parse error
      }
    }

    if (appRes.data.status === 'Enrolled') {
      activeStep.value = 5;
    } else if (['Approved', 'Queued for Enrollment', 'Assessed'].includes(appRes.data.status)) {
      activeStep.value = 4;
    }
  } catch (err) {
    errorMessage.value = err.message || 'Failed to load admission details.';
  } finally {
    isLoading.value = false;
  }
};

const saveApplicationDetails = async (isStep2 = false) => {
  errorMessage.value = '';

  if (!isStep2) {
    const cleanContact = (form.value.contact_number || '').replace(/\D/g, '');
    if (!/^09\d{9}$/.test(cleanContact)) {
      errorMessage.value = 'Must be an 11-digit Philippine mobile number starting with 09 (e.g. 09123456789).';
      return;
    }

    if (form.value.guardian_contact) {
      const cleanGuardian = form.value.guardian_contact.replace(/\D/g, '');
      if (!/^09\d{9}$/.test(cleanGuardian)) {
        errorMessage.value = 'Guardian contact must be an 11-digit Philippine mobile number starting with 09.';
        return;
      }
    }

    if (form.value.lrn) {
      const cleanLrn = form.value.lrn.replace(/\D/g, '');
      if (cleanLrn.length !== 12) {
        errorMessage.value = 'DepEd Learner Reference Number (LRN) must be exactly 12 numeric digits.';
        return;
      }
    }

    if (form.value.birthdate) {
      const bdate = new Date(form.value.birthdate);
      const today = new Date();
      let age = today.getFullYear() - bdate.getFullYear();
      const m = today.getMonth() - bdate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < bdate.getDate())) {
        age--;
      }
      if (age < 11) {
        errorMessage.value = 'Applicant must be at least 11 years of age for secondary school admission.';
        return;
      }
    }
  }

  try {
    await api.updateApplication(form.value);
    successMessage.value = 'Application saved successfully!';
    activeStep.value = isStep2 ? 3 : 2;
    await loadData();
  } catch (err) {
    errorMessage.value = err.message || 'Failed to save application.';
  }
};

const handleFileUpload = async (event, docType) => {
  const file = event.target.files?.[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('document_type', docType);
  formData.append('file', file);

  errorMessage.value = '';
  try {
    await api.uploadDocument(formData);
    successMessage.value = `${docType} uploaded successfully!`;
    await loadData();
  } catch (err) {
    errorMessage.value = err.message || 'Upload failed.';
  }
};

const showSubmitReviewConfirm = ref(false);

const openSubmitReviewModal = () => {
  if (!hasAllMandatoryDocs.value) return;
  showSubmitReviewConfirm.value = true;
};

const confirmSubmitForEvaluation = async () => {
  isSubmitting.value = true;
  errorMessage.value = '';
  try {
    await api.submitApplication();
    showSubmitReviewConfirm.value = false;
    successMessage.value = 'Application successfully submitted for Registrar review!';
    activeStep.value = 4;
    await loadData();
  } catch (err) {
    errorMessage.value = err.message || 'Submission failed. Make sure all required documents are uploaded.';
  } finally {
    isSubmitting.value = false;
  }
};

const generateWalkinTicket = async () => {
  errorMessage.value = '';
  try {
    const res = await api.checkoutPayment({
      payment_type: 'walkin',
      walkin_date: walkinForm.value.scheduled_date,
      time_slot: walkinForm.value.time_slot,
      amount: estimatedAssessment.value.downpayment
    });
    walkinTicket.value = res.data;
    successMessage.value = 'Walk-in Cashier Payment Slip generated successfully! Present this at Window 1 or 2.';
    await loadData();
  } catch (err) {
    errorMessage.value = err.message || 'Failed to generate walk-in ticket.';
  }
};

const openPaymongoModal = () => {
  paymongoError.value = '';
  paymongoForm.value.amount = estimatedAssessment.value.downpayment;
  paymongoForm.value.mobile = application.value?.contact_number || '';
  paymongoForm.value.account_name = `${application.value?.first_name || ''} ${application.value?.last_name || ''}`.trim();
  paymongoForm.value.reference_no = '';
  paymongoForm.value.receipt_file = null;
  showPaymongoModal.value = true;
};

const submitOnlinePayment = async () => {
  paymongoError.value = '';
  if (!paymongoForm.value.reference_no?.trim()) {
    paymongoError.value = 'Please enter your payment reference number / transaction ID.';
    return;
  }
  if (!paymongoForm.value.amount || paymongoForm.value.amount <= 0) {
    paymongoError.value = 'Please enter a valid payment amount.';
    return;
  }
  const minRequired = Math.min(estimatedAssessment.value.downpayment || 3000, estimatedAssessment.value.netPayable || 0);
  const maxAllowed = estimatedAssessment.value.netPayable || 0;

  if (paymongoForm.value.amount < minRequired) {
    paymongoError.value = `Payment amount (₱${Number(paymongoForm.value.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}) is below the required minimum of ₱${Number(minRequired).toLocaleString('en-US', {minimumFractionDigits: 2})}.`;
    return;
  }

  if (maxAllowed > 0 && paymongoForm.value.amount > maxAllowed) {
    paymongoError.value = `Payment amount (₱${Number(paymongoForm.value.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}) exceeds the maximum settle limit / remaining balance of ₱${Number(maxAllowed).toLocaleString('en-US', {minimumFractionDigits: 2})}.`;
    return;
  }
  if (!paymongoForm.value.receipt_file && !onlineSubmission.value?.receipt_file_path) {
    paymongoError.value = 'Please upload your official payment receipt screenshot or PDF for Treasury verification.';
    return;
  }
  isPayingOnline.value = true;
  errorMessage.value = '';
  try {
    const formData = new FormData();
    formData.append('payment_type', 'online');
    formData.append('payment_channel', paymongoForm.value.channel || 'GCash');
    formData.append('reference_no', paymongoForm.value.reference_no.trim());
    formData.append('account_name', (paymongoForm.value.account_name || '').trim());
    formData.append('account_number', (paymongoForm.value.mobile || '').trim());
    formData.append('amount', paymongoForm.value.amount);
    if (paymongoForm.value.receipt_file) {
      formData.append('receipt', paymongoForm.value.receipt_file);
    }

    const res = await api.checkoutPayment(formData);

    showPaymongoModal.value = false;
    successMessage.value = res.message || 'Payment submitted successfully! Awaiting Treasury verification.';
    await loadData();
  } catch (err) {
    paymongoError.value = err.message || 'Online payment submission failed. Please check your reference number.';
    errorMessage.value = err.message || 'Online payment submission failed.';
  } finally {
    isPayingOnline.value = false;
  }
};

const printSlip = () => {
  window.print();
};

const previewDoc = ref(null);

const openPreviewModal = (doc) => {
  if (!doc) return;
  previewDoc.value = doc;
};

const isPdf = (filePath) => {
  if (!filePath) return false;
  return filePath.toLowerCase().endsWith('.pdf');
};

const handleDeleteDocument = async (doc) => {
  if (!doc || !doc.id) return;
  
  const confirmed = window.confirm(`Are you sure you want to remove "${doc.original_filename}"? You can upload a new file afterward.`);
  if (!confirmed) return;

  errorMessage.value = '';
  try {
    const res = await api.deleteDocument(doc.id);
    successMessage.value = res.message || `${doc.document_type} removed successfully.`;
    await loadData();
  } catch (err) {
    errorMessage.value = err.message || 'Failed to remove document.';
  }
};

onMounted(() => {
  loadData();
});
</script>
