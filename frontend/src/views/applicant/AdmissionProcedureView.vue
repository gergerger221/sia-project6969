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
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white mt-2">Admission & Enrollment Wizard</h1>
        <p class="text-xs text-slate-400 mt-1">
          Complete the steps below, upload your DepEd credentials, and track your admission progress.
        </p>
      </div>

      <div v-if="application?.status === 'Enrolled'" class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500/40 text-left">
        <div class="text-[10px] uppercase font-bold text-emerald-400">Enrollment Completed!</div>
        <div class="text-sm font-bold text-white mt-0.5">Permanent ID: {{ application?.assessment_info?.student_number || 'Generated' }}</div>
        <router-link to="/student" class="inline-block mt-2 px-3.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 transition">
          Go to Student Portal →
        </router-link>
      </div>
    </div>

    <!-- OFFICIALLY ENROLLED & STUDENT ACCOUNT GENERATED CELEBRATORY BANNER -->
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
              Your payment has been processed by the Treasury. A separate official student portal account has been created for your academic term.
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
            class="w-full mt-2 py-2.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs transition flex items-center justify-center space-x-1.5 shadow-md shadow-emerald-500/30"
          >
            <span>Proceed to Student Portal Login</span>
            <ArrowRight class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- APPLICATION APPROVED: PROCEED TO TREASURY OFFICE GUIDANCE BANNER -->
    <div 
      v-else-if="['Queued for Enrollment', 'Approved', 'Assessed'].includes(application?.status)" 
      class="no-print p-6 rounded-3xl bg-gradient-to-br from-blue-950 via-slate-900 to-indigo-950 border-2 border-blue-400 text-white shadow-2xl mb-8 animate-in fade-in slide-in-from-top-4 duration-300"
    >
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-start space-x-4">
          <div class="w-14 h-14 rounded-2xl bg-blue-500 text-slate-950 flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/30">
            <CreditCard class="w-8 h-8" />
          </div>
          <div>
            <div class="inline-flex items-center space-x-1.5 px-3 py-0.5 rounded-full bg-blue-500/20 border border-blue-400/40 text-blue-300 text-[11px] font-bold uppercase tracking-wider mb-1.5">
              <CheckCircle class="w-3.5 h-3.5" />
              <span>Application Approved by Registrar</span>
            </div>
            <h3 class="text-xl font-extrabold text-white tracking-tight">Your Application is Approved! Please Proceed to the Treasury Office</h3>
            <p class="text-xs text-slate-300 mt-1 max-w-xl">
              Your admission requirements have been verified by the Registrar. You have been placed in <strong class="text-white">{{ application?.queue_info?.section_name || 'Assigned Section' }}</strong> with permanent Student Number <strong class="text-blue-300">{{ application?.student_no || 'Generated' }}</strong>.
            </p>
            <p class="text-xs text-amber-300 font-semibold mt-2 flex items-center space-x-1.5">
              <span><strong>Next Step:</strong> Please proceed to the <strong>Treasury / Cashier Office (Window 1 or 2)</strong> to pay your minimum downpayment of ₱3,000.00.</span>
            </p>
          </div>
        </div>

        <div class="bg-slate-950/90 p-4 rounded-2xl border border-blue-500/40 w-full md:w-auto min-w-[280px] font-mono text-xs space-y-2 shrink-0 shadow-xl">
          <div class="text-[10px] uppercase font-bold text-blue-400 tracking-wider flex items-center justify-between">
            <span>Queue & Payment Info</span>
            <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 text-[10px] font-bold">Queue #{{ application?.queue_info?.queue_number || 1 }}</span>
          </div>
          <div class="flex justify-between border-b border-slate-800 pb-1.5 pt-1">
            <span class="text-slate-400">Student ID:</span>
            <strong class="text-white font-bold">{{ application?.student_no || 'Generated' }}</strong>
          </div>
          <div class="flex justify-between border-b border-slate-800 pb-1.5">
            <span class="text-slate-400">Net Assessment:</span>
            <strong class="text-emerald-400 font-bold">₱{{ Number(application?.assessment_info?.net_payable || 19500).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</strong>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400">Required Downpayment:</span>
            <strong class="text-amber-400 font-bold">₱3,000.00</strong>
          </div>
          <button 
            @click="activeStep = 4" 
            class="w-full mt-2 py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-xs transition flex items-center justify-center space-x-1.5 shadow-md shadow-blue-600/30"
          >
            <Printer class="w-4 h-4" />
            <span>View & Print Slip for Treasury</span>
          </button>
        </div>
      </div>
    </div>

    <!-- DEFICIENCY / VALIDATION NOTIFICATION BANNER (Hidden in Print) -->
    <div v-if="deficientDocs.length > 0" class="no-print p-4 sm:p-5 rounded-2xl bg-rose-50 border-2 border-rose-400 text-rose-950 text-xs mb-6 shadow-md flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 animate-in fade-in duration-200">
      <div class="flex items-start space-x-3">
        <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-md">
          <AlertTriangle class="w-5 h-5 animate-pulse" />
        </div>
        <div>
          <h4 class="font-extrabold text-sm text-rose-950">Action Required: {{ deficientDocs.length }} Document(s) Marked as Deficient</h4>
          <p class="text-xs text-rose-800 mt-0.5">
            The Registrar has reviewed your requirements and marked <span class="font-bold underline">{{ deficientDocs.map(d => d.document_type).join(', ') }}</span> as deficient. Please see the remarks and re-upload clear copies.
          </p>
        </div>
      </div>
      <button @click="activeStep = 3" class="whitespace-nowrap px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-sm transition flex items-center space-x-1.5 shrink-0">
        <span>Fix Deficient Files</span>
        <ArrowRight class="w-3.5 h-3.5" />
      </button>
    </div>

    <!-- ALL DOCUMENTS VERIFIED NOTIFICATION BANNER -->
    <div v-else-if="verifiedDocs.length >= 2 && verifiedDocs.length === (application?.documents?.length || 0)" class="no-print p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-950 text-xs mb-6 shadow-sm flex items-center space-x-3">
      <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
        <CheckCircle class="w-5 h-5" />
      </div>
      <div>
        <h4 class="font-extrabold text-sm text-emerald-950">Requirements Successfully Verified!</h4>
        <p class="text-xs text-emerald-800 mt-0.5">All your uploaded documents have been verified and approved by the Registrar.</p>
      </div>
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
              placeholder="e.g. 109283746501" 
              class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm font-mono"
            />
            <span class="text-[10px] text-slate-400">Found on previous DepEd SF9 / Form 138</span>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">First Name *</label>
            <input v-model="form.first_name" type="text" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm" />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Last Name *</label>
            <input v-model="form.last_name" type="text" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Middle Name *</label>
            <input v-model="form.middle_name" type="text" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Suffix (Jr, III)</label>
            <input v-model="form.suffix" type="text" placeholder="e.g. Jr." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Gender *</label>
            <select v-model="form.gender" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Birthdate *</label>
            <input 
              v-model="form.birthdate" 
              type="date" 
              :min="minBirthdate"
              :max="maxBirthdate"
              required 
              class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm" 
            />
            <span class="text-[10px] text-slate-400">Applicant must be at least 11 years of age</span>
          </div>
        </div>

        <!-- Address -->
        <div class="pt-4 border-t border-slate-100">
          <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Residential Address</h3>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Barangay *</label>
              <input v-model="form.address_barangay" type="text" required placeholder="e.g. Brgy. 102" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">City / Municipality *</label>
              <input v-model="form.address_city" type="text" required placeholder="e.g. Manila" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Province *</label>
              <input v-model="form.address_province" type="text" required placeholder="e.g. Metro Manila" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm" />
            </div>
          </div>
        </div>

        <!-- Parent / Guardian -->
        <div class="pt-4 border-t border-slate-100">
          <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Parent / Legal Guardian Information</h3>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Guardian Name *</label>
              <input v-model="form.guardian_name" type="text" required placeholder="Full Name" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Relationship *</label>
              <input v-model="form.guardian_relationship" type="text" required placeholder="e.g. Mother, Father, Guardian" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Guardian Contact Number *</label>
              <input 
                v-model="form.guardian_contact" 
                type="tel" 
                required 
                maxlength="11"
                pattern="[0-9]{11}"
                @input="form.guardian_contact = form.guardian_contact.replace(/\D/g, '').slice(0, 11)"
                placeholder="09171234567" 
                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm font-mono" 
              />
              <span class="text-[10px] text-slate-400">11-digit guardian mobile (e.g. 09171234567)</span>
            </div>
          </div>
        </div>

        <!-- Student / Applicant Personal Contact -->
        <div class="pt-4 border-t border-slate-100">
          <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Applicant / Student Personal Contact</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Student's Own Mobile Number *</label>
              <input 
                v-model="form.contact_number" 
                type="tel" 
                required 
                maxlength="11"
                pattern="[0-9]{11}"
                @input="form.contact_number = form.contact_number.replace(/\D/g, '').slice(0, 11)"
                placeholder="09171234567" 
                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm font-mono" 
              />
              <span class="text-[10px] text-slate-400">11-digit student mobile for enrollment and SMS alerts</span>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Account Login Email Address</label>
              <div class="relative">
                <input 
                  v-model="form.email" 
                  type="email" 
                  readonly
                  disabled
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-100 text-slate-500 font-mono text-sm cursor-not-allowed select-none" 
                />
                <span class="absolute right-2.5 top-2 px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-slate-200 text-slate-600">
                  Login Email
                </span>
              </div>
              <span class="text-[10px] text-slate-400">Permanent email used for admission portal login (Non-editable)</span>
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-slate-100 space-x-3">
          <button type="submit" class="px-6 py-2.5 rounded-xl font-bold bg-emerald-600 hover:bg-emerald-500 text-white text-sm shadow-md transition">
            Save & Continue to Step 2 →
          </button>
        </div>
      </form>
    </div>

    <!-- STEP 2: ACADEMIC LEVEL & TRACK/STRAND SELECTION -->
    <div v-if="activeStep === 2" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
      <div class="border-b border-slate-100 pb-4 mb-6">
        <h2 class="text-lg font-bold text-slate-800">Step 2: Grade Level & Senior High Track/Strand Selection</h2>
        <p class="text-xs text-slate-500 mt-1">Select your incoming grade level and SHS specialization (if Grade 11 or 12).</p>
      </div>

      <form @submit.prevent="saveApplicationDetails(true)" class="space-y-6">
        <!-- Student Classification Selector -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-2">Student Admission Classification *</label>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div 
              @click="form.applicant_type = 'New Student'"
              :class="[
                form.applicant_type === 'New Student' 
                  ? 'bg-emerald-50/80 border-emerald-500 ring-2 ring-emerald-500/20 text-emerald-950 shadow-sm' 
                  : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'
              ]"
              class="p-3.5 rounded-2xl border cursor-pointer transition flex items-start space-x-3"
            >
              <input type="radio" v-model="form.applicant_type" value="New Student" class="mt-0.5 text-emerald-600 focus:ring-emerald-500" />
              <div>
                <div class="font-bold text-xs text-slate-900">New Student / Non-Transferee</div>
                <div class="text-[11px] text-slate-500 mt-0.5">
                  {{ isSHS ? 'Incoming Grade 11 from JHS Completers' : 'Incoming Grade 7 from Elementary School' }}
                </div>
              </div>
            </div>

            <div 
              @click="form.applicant_type = 'Transferee'"
              :class="[
                form.applicant_type === 'Transferee' 
                  ? 'bg-emerald-50/80 border-emerald-500 ring-2 ring-emerald-500/20 text-emerald-950 shadow-sm' 
                  : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'
              ]"
              class="p-3.5 rounded-2xl border cursor-pointer transition flex items-start space-x-3"
            >
              <input type="radio" v-model="form.applicant_type" value="Transferee" class="mt-0.5 text-emerald-600 focus:ring-emerald-500" />
              <div>
                <div class="font-bold text-xs text-slate-900">Transferee Student</div>
                <div class="text-[11px] text-slate-500 mt-0.5">
                  {{ isSHS ? 'Transferring from another Senior High School' : 'Transferring from another Junior High School' }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-2">Select Target Grade Level *</label>
            <div class="grid grid-cols-3 gap-2">
              <button 
                type="button" 
                v-for="gl in academicOptions.grade_levels" 
                :key="gl.id"
                @click="form.grade_level_id = gl.id"
                :class="[
                  form.grade_level_id === gl.id 
                    ? 'bg-emerald-600 text-white font-bold border-emerald-600 shadow-sm' 
                    : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'
                ]"
                class="p-3 rounded-xl border text-center text-xs transition"
              >
                <div class="font-extrabold text-sm">{{ gl.level_code }}</div>
                <div class="text-[10px] opacity-80">{{ gl.name }}</div>
              </button>
            </div>
          </div>

          <!-- SHS Strand Selection (Visible only if Grade 11 or 12) -->
          <div v-if="isSHS">
            <label class="block text-xs font-semibold text-slate-700 mb-2">Senior High Specialized Strand *</label>
            <select v-model="form.strand_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm bg-white mb-3">
              <option value="" disabled>-- Select Track & Strand --</option>
              <option v-for="st in academicOptions.strands" :key="st.id" :value="st.id">
                [{{ st.code }}] {{ st.name }}
              </option>
            </select>

            <label class="block text-xs font-semibold text-slate-700 mb-1">DepEd Voucher / ESC Grantee Status</label>
            <select v-model="form.voucher_status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
              <option value="None">None / Non-Voucher</option>
              <option value="Public JHS Completer (100%)">Public JHS Completer (100% Voucher Subsidy)</option>
              <option value="Private ESC Grantee (80%)">Private ESC Grantee (80% Voucher Subsidy)</option>
              <option value="Private Non-ESC Voucher (50%)">Private Non-ESC QVR Voucher (50% Subsidy)</option>
            </select>
          </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
          <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Previous School Background</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">Last School Attended *</label>
              <input v-model="form.last_school_attended" type="text" required placeholder="e.g. Manila National High School" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">School Type *</label>
              <select v-model="form.last_school_type" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
                <option value="Public">Public School</option>
                <option value="Private">Private School</option>
                <option value="ESC Grantee">Private ESC Grantee School</option>
              </select>
            </div>
          </div>
        </div>

        <div class="flex justify-between pt-4 border-t border-slate-100">
          <button type="button" @click="activeStep = 1" class="px-5 py-2.5 rounded-xl font-semibold text-slate-600 hover:bg-slate-100 text-sm transition">
            ← Back to Step 1
          </button>
          <button type="submit" class="px-6 py-2.5 rounded-xl font-bold bg-emerald-600 hover:bg-emerald-500 text-white text-sm shadow-md transition">
            Save & Continue to Uploads →
          </button>
        </div>
      </form>
    </div>

    <!-- STEP 3: SEND REQUIREMENTS (DOCUMENT UPLOADS) -->
    <div v-if="activeStep === 3" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
      <div class="border-b border-slate-100 pb-4 mb-6">
        <h2 class="text-lg font-bold text-slate-800">Step 3: Send & Upload Admission Requirements</h2>
        <p class="text-xs text-slate-500 mt-1">Upload clear scanned copies or photos (PDF, PNG, JPG, up to 10MB each).</p>
      </div>

      <!-- REQUIREMENTS STATUS SUMMARY TALLY BAR -->
      <div class="flex flex-wrap items-center gap-2.5 mb-6 p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
        <span class="font-bold text-slate-700 mr-1 flex items-center space-x-1">
          <FileCheck class="w-4 h-4 text-slate-500" />
          <span>Status Summary:</span>
        </span>
        <span class="px-3 py-1 rounded-full font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center space-x-1">
          <CheckCircle class="w-3.5 h-3.5 text-emerald-600" />
          <span>{{ verifiedDocs.length }} Verified</span>
        </span>
        <span v-if="deficientDocs.length > 0" class="px-3 py-1 rounded-full font-extrabold bg-rose-100 text-rose-900 border border-rose-400 flex items-center space-x-1 animate-pulse">
          <AlertTriangle class="w-3.5 h-3.5 text-rose-600" />
          <span>{{ deficientDocs.length }} Deficient (Action Required)</span>
        </span>
        <span v-if="pendingDocs.length > 0" class="px-3 py-1 rounded-full font-semibold bg-amber-100 text-amber-800 border border-amber-300 flex items-center space-x-1">
          <Clock class="w-3.5 h-3.5 text-amber-600" />
          <span>{{ pendingDocs.length }} Pending Evaluation</span>
        </span>
      </div>

      <div class="space-y-4">
        <div 
          v-for="req in requiredDocsList" 
          :key="req.type"
          class="p-4 sm:p-5 rounded-2xl border transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
          :class="getCardStyleClass(req.type)"
        >
          <div class="flex items-start space-x-3 w-full sm:w-auto">
            <div 
              class="w-11 h-11 rounded-2xl border flex items-center justify-center shrink-0 shadow-sm"
              :class="getIconBadgeClass(req.type)"
            >
              <AlertTriangle v-if="getDocUploaded(req.type)?.status === 'Deficient'" class="w-6 h-6 text-rose-600" />
              <CheckCircle v-else-if="getDocUploaded(req.type)?.status === 'Verified'" class="w-6 h-6 text-emerald-600" />
              <Clock v-else-if="getDocUploaded(req.type)?.status === 'Pending'" class="w-6 h-6 text-amber-600" />
              <FileText v-else class="w-6 h-6 text-slate-500" />
            </div>

            <div class="flex-1">
              <div class="flex items-center flex-wrap gap-2">
                <h4 class="font-bold text-sm text-slate-900">{{ req.type }}</h4>
                <span v-if="req.required && !getDocUploaded(req.type)" class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">Required</span>
                <span v-if="getDocUploaded(req.type)" :class="getDocStatusClass(getDocUploaded(req.type).status)" class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wide">
                  {{ getDocUploaded(req.type).status }}
                </span>
              </div>
              <p class="text-xs text-slate-500 mt-0.5">{{ req.description }}</p>

              <!-- Uploaded filename -->
              <div v-if="getDocUploaded(req.type)" class="mt-1.5 text-xs text-slate-600">
                Uploaded file: <span class="font-semibold text-slate-800 font-mono">{{ getDocUploaded(req.type).original_filename }}</span>
              </div>

              <!-- PROMINENT REGISTRAR DEFICIENCY CALLOUT BOX -->
              <div v-if="getDocUploaded(req.type)?.status === 'Deficient'" class="mt-2.5 p-3 rounded-xl bg-rose-100/90 border border-rose-300 text-rose-950 text-xs">
                <div class="flex items-start space-x-2">
                  <AlertTriangle class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
                  <div>
                    <span class="font-extrabold uppercase text-[10px] tracking-wider text-rose-700 block">Registrar Deficiency Remark:</span>
                    <p class="font-bold text-rose-900 mt-0.5">{{ getDocUploaded(req.type).verification_notes || 'Incomplete or unclear copy. Please replace with a clear, readable copy.' }}</p>
                  </div>
                </div>
              </div>

              <!-- Verified note -->
              <div v-else-if="getDocUploaded(req.type)?.status === 'Verified'" class="mt-1.5 text-xs font-semibold text-emerald-700 flex items-center space-x-1">
                <Check class="w-3.5 h-3.5" />
                <span>Verified and approved by Registrar</span>
              </div>
            </div>
          </div>

          <!-- Action Buttons (View, Replace, Remove OR Choose File) -->
          <div class="shrink-0 w-full sm:w-auto flex items-center flex-wrap sm:flex-nowrap gap-2">
            <!-- If Uploaded -->
            <template v-if="getDocUploaded(req.type)">
              <button 
                type="button" 
                @click="openPreviewModal(getDocUploaded(req.type))" 
                class="px-3 py-2 rounded-xl text-xs font-bold bg-white text-blue-700 border border-blue-300 hover:bg-blue-50 flex items-center space-x-1.5 transition shadow-sm"
                title="View document in pop-up modal"
              >
                <Eye class="w-4 h-4 text-blue-600" />
                <span>View</span>
              </button>

              <!-- CASE 1: IF VERIFIED -> LOCKED (Irreplaceable & Unremovable) -->
              <div 
                v-if="getDocUploaded(req.type).status === 'Verified'"
                class="px-3 py-2 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center space-x-1.5 shadow-sm"
                title="This requirement has been verified and locked by the Registrar. It cannot be altered or removed."
              >
                <CheckCircle class="w-3.5 h-3.5 text-emerald-600" />
                <span>Accepted & Locked</span>
              </div>

              <!-- CASE 2: IF DEFICIENT -> HIGH VISIBILITY RE-UPLOAD & REMOVE -->
              <template v-else-if="getDocUploaded(req.type).status === 'Deficient'">
                <label 
                  class="cursor-pointer inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-extrabold bg-rose-600 hover:bg-rose-500 text-white shadow-md transition space-x-1.5"
                >
                  <Upload class="w-4 h-4" />
                  <span>Re-upload File</span>
                  <input type="file" @change="handleFileUpload($event, req.type)" class="hidden" accept=".pdf,.png,.jpg,.jpeg,.webp" />
                </label>

                <button 
                  type="button" 
                  @click="handleDeleteDocument(getDocUploaded(req.type))" 
                  class="px-3 py-2 rounded-xl text-xs font-bold bg-white text-rose-700 border border-rose-300 hover:bg-rose-50 flex items-center space-x-1 transition shadow-sm"
                  title="Remove this uploaded document"
                >
                  <Trash2 class="w-4 h-4 text-rose-600" />
                  <span>Remove</span>
                </button>
              </template>

              <!-- CASE 3: IF PENDING -> REGULAR REPLACE & REMOVE -->
              <template v-else>
                <label 
                  class="cursor-pointer inline-flex items-center justify-center px-3 py-2 rounded-xl text-xs font-bold bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 shadow-sm transition"
                >
                  <Upload class="w-4 h-4 mr-1 text-slate-500" />
                  <span>Replace</span>
                  <input type="file" @change="handleFileUpload($event, req.type)" class="hidden" accept=".pdf,.png,.jpg,.jpeg,.webp" />
                </label>

                <button 
                  type="button" 
                  @click="handleDeleteDocument(getDocUploaded(req.type))" 
                  class="px-3 py-2 rounded-xl text-xs font-bold bg-white text-rose-700 border border-rose-300 hover:bg-rose-50 flex items-center space-x-1 transition shadow-sm"
                  title="Remove this uploaded document"
                >
                  <Trash2 class="w-4 h-4 text-rose-600" />
                  <span>Remove</span>
                </button>
              </template>
            </template>

            <!-- If Not Yet Uploaded -->
            <template v-else>
              <label class="cursor-pointer inline-flex items-center justify-center w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-bold bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 shadow-sm transition">
                <Upload class="w-4 h-4 mr-1.5 text-slate-500" />
                <span>Choose File</span>
                <input type="file" @change="handleFileUpload($event, req.type)" class="hidden" accept=".pdf,.png,.jpg,.jpeg,.webp" />
              </label>
            </template>
          </div>
        </div>
      </div>

      <!-- Mandatory Document Warning if Incomplete -->
      <div v-if="!hasAllMandatoryDocs" class="mt-6 p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs flex items-start space-x-2.5 shadow-sm">
        <AlertTriangle class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
        <div>
          <span class="font-bold block text-amber-950">Mandatory Documents Required:</span>
          <p class="text-[11px] text-amber-800 mt-0.5">
            You must upload clear copies of <strong class="underline">{{ missingMandatoryDocs.join(' and ') }}</strong> before submitting your requirements for review.
          </p>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row items-center justify-between pt-6 mt-6 border-t border-slate-100 gap-3">
        <button type="button" @click="activeStep = 2" class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-semibold text-slate-600 hover:bg-slate-100 text-sm transition text-center">
          ← Back to Step 2
        </button>
        <button 
          @click="submitForEvaluation" 
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

    <!-- STEP 4: PRINTABLE OFFICIAL PRE-ENROLLMENT & ASSESSMENT FORM -->
    <div v-if="activeStep === 4" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl print:p-0 print:m-0 print:border-none print:shadow-none print:rounded-none">
      <!-- Action Bar (Hidden in Print) -->
      <div class="no-print flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-200 gap-4">
        <div>
          <h2 class="text-lg font-bold text-slate-800">Step 4: Official Pre-Enrollment & Assessment Slip</h2>
          <p class="text-xs text-slate-500">View your assessment breakdown and print this slip for payment at the Treasury window.</p>
        </div>
        <div class="flex items-center space-x-2">
          <button @click="activeStep = 5" class="px-4 py-2.5 rounded-xl font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition flex items-center space-x-1.5">
            <span>Go to Status Tracker (Step 5) →</span>
          </button>
          <button @click="printSlip" class="px-5 py-2.5 rounded-xl font-bold bg-slate-900 hover:bg-slate-800 text-white text-xs shadow-md transition flex items-center space-x-2">
            <Printer class="w-4 h-4" />
            <span>Print Assessment Form</span>
          </button>
        </div>
      </div>

      <!-- Printable Certificate of Matriculation / DepEd Enrollment Slip -->
      <div class="border-2 border-slate-800 p-6 print:p-3.5 rounded-xl bg-white text-slate-900 font-sans print:border-slate-800">
        <!-- DepEd Header -->
        <div class="text-center border-b-2 border-slate-800 pb-2.5 mb-3 print:pb-2 print:mb-2">
          <div class="text-[11px] print:text-[9.5px] font-semibold tracking-widest uppercase text-slate-600">Republic of the Philippines • Department of Education</div>
          <h2 class="text-lg print:text-base font-extrabold tracking-tight uppercase mt-0.5 mb-0.5">SIA High School - Basic Education Department</h2>
          <p class="text-xs print:text-[10px] text-slate-600">Official Web-Based Admission & Pre-Enrollment Assessment Form</p>
          <div class="inline-block mt-1.5 px-3 py-0.5 rounded bg-slate-100 border border-slate-300 text-xs print:text-[10px] font-bold font-mono">
            SY 2026-2027 • {{ isSHS ? '1st Semester' : 'Full Academic Year' }}
          </div>
        </div>

        <!-- Student & Program Info Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 print:gap-2 text-xs print:text-[10.5px] mb-3 print:mb-2 bg-slate-50 p-3 print:p-2 rounded-lg border border-slate-200">
          <div>
            <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Student Number:</span>
            <span class="font-bold font-mono text-xs print:text-[11px] text-blue-700">{{ application?.student_no || application?.assessment_info?.student_number || 'Pending Approval' }}</span>
          </div>
          <div>
            <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Applicant Name:</span>
            <span class="font-bold text-xs print:text-[11px]">{{ application?.last_name }}, {{ application?.first_name }} {{ application?.middle_name || '' }}</span>
          </div>
          <div>
            <span class="text-slate-500 block text-[9.5px] uppercase font-bold">DepEd LRN:</span>
            <span class="font-bold font-mono text-xs print:text-[11px]">{{ application?.lrn || 'N/A' }}</span>
          </div>
          <div>
            <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Grade & Section:</span>
            <span class="font-bold text-emerald-800 text-xs print:text-[11px]">{{ application?.grade_level_name }} - {{ application?.queue_info?.section_name || 'Main' }}</span>
          </div>
        </div>

        <!-- Enrolled Subjects Table -->
        <div class="mb-3 print:mb-2">
          <h3 class="text-xs print:text-[10.5px] font-bold uppercase tracking-wider text-slate-700 mb-1.5 print:mb-1">Curriculum Learning Areas / Enrolled Subjects</h3>
          <table class="w-full text-xs print:text-[10px] text-left border-collapse border border-slate-300">
            <thead>
              <tr class="bg-slate-100 border-b border-slate-300">
                <th class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-300">Subject Code</th>
                <th class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-300">Descriptive Title</th>
                <th class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-300">Classification</th>
                <th class="p-1.5 print:py-0.5 print:px-1.5 text-center">Units / Hours</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="sub in sampleSubjects" :key="sub.code" class="border-b border-slate-200">
                <td class="p-1.5 print:py-0.5 print:px-1.5 font-mono font-bold border-r border-slate-200">{{ sub.code }}</td>
                <td class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-200">{{ sub.title }}</td>
                <td class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-200">{{ sub.category }}</td>
                <td class="p-1.5 print:py-0.5 print:px-1.5 text-center font-bold">{{ sub.units }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Fee Assessment & Subsidy Breakdown -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 print:gap-3 pt-2.5 print:pt-1.5 border-t border-slate-200 text-xs print:text-[10px]">
          <div>
            <h4 class="font-bold text-slate-800 uppercase mb-1">DepEd Subsidy / Voucher Information</h4>
            <div class="p-2.5 print:p-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 space-y-1">
              <div>Voucher Category: <span class="font-bold text-slate-900">{{ application?.voucher_status || 'None' }}</span></div>
              <div>School of Origin: <span class="font-bold text-slate-900">{{ application?.last_school_attended || 'Not specified' }}</span></div>
              <div v-if="estimatedAssessment.voucherDiscount > 0" class="text-[11px] font-bold text-emerald-700">
                Applied Tuition Subsidy: - ₱{{ estimatedAssessment.voucherDiscount.toLocaleString('en-US', {minimumFractionDigits: 2}) }}
              </div>
              <div class="text-[9.5px] text-slate-500 italic mt-0.5">
                {{ estimatedAssessment.voucherDiscount > 0 ? 'Voucher subsidy is pre-applied to your tuition breakdown below.' : 'No voucher subsidy applied for this category.' }}
              </div>
            </div>
          </div>

          <div>
            <h4 class="font-bold text-slate-800 uppercase mb-1">Treasury Assessment Summary</h4>
            <div class="bg-slate-50 p-2.5 print:p-2 rounded-lg border border-slate-200 space-y-0.5 font-mono">
              <div class="flex justify-between">
                <span>Tuition & Instruction:</span>
                <span>₱{{ estimatedAssessment.tuition.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
              </div>
              <div class="flex justify-between">
                <span>Miscellaneous & Lab Fees:</span>
                <span>₱{{ estimatedAssessment.misc.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
              </div>
              <div class="flex justify-between text-emerald-700 font-bold border-t border-slate-200 pt-0.5">
                <span>Less: DepEd Voucher Subsidy:</span>
                <span>- ₱{{ estimatedAssessment.voucherDiscount.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
              </div>
              <div class="flex justify-between text-xs print:text-[11px] font-extrabold text-slate-900 border-t-2 border-slate-800 pt-0.5">
                <span>Net Payable Amount:</span>
                <span>₱{{ estimatedAssessment.netPayable.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
              </div>
              <div class="flex justify-between text-[10px] text-slate-600 pt-0.5">
                <span>Required Downpayment:</span>
                <span>₱{{ estimatedAssessment.downpayment.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Signatures Footer -->
        <div class="grid grid-cols-3 gap-6 print:gap-4 text-center text-xs print:text-[10px] mt-6 print:mt-4 pt-4 print:pt-2.5 border-t border-slate-300">
          <div>
            <div class="border-b border-slate-400 pb-0.5 mb-0.5 font-bold">{{ application?.first_name }} {{ application?.last_name }}</div>
            <span class="text-[9px] text-slate-500 uppercase">Student / Applicant Signature</span>
          </div>
          <div>
            <div class="border-b border-slate-400 pb-0.5 mb-0.5 font-bold">Office of the Registrar</div>
            <span class="text-[9px] text-slate-500 uppercase">Verified & Evaluated</span>
          </div>
          <div>
            <div class="border-b border-slate-400 pb-0.5 mb-0.5 font-bold">Treasury / Cashier</div>
            <span class="text-[9px] text-slate-500 uppercase">Official Receipt Stamp</span>
          </div>
        </div>
      </div>
    </div>

    <!-- STEP 5: ADMISSION STATUS & QUEUE TRACKER -->
    <div v-if="activeStep === 5" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
      <div class="border-b border-slate-100 pb-4 mb-6">
        <h2 class="text-lg font-bold text-slate-800">Step 5: Admission Status & Verification Progress</h2>
        <p class="text-xs text-slate-500 mt-1">Real-time status updates from the Registrar & Academic Coordinator.</p>
      </div>

      <!-- Live Timeline -->
      <div class="space-y-6 max-w-2xl mx-auto py-4">
        <div v-for="(item, idx) in statusTimeline" :key="idx" class="flex items-start space-x-4">
          <div class="flex flex-col items-center">
            <div 
              class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs"
              :class="item.completed ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 text-slate-400 border border-slate-200'"
            >
              <Check v-if="item.completed" class="w-5 h-5" />
              <span v-else>{{ idx + 1 }}</span>
            </div>
            <div v-if="idx < statusTimeline.length - 1" class="w-0.5 h-12" :class="item.completed ? 'bg-emerald-500' : 'bg-slate-200'"></div>
          </div>
          <div class="pt-1">
            <h4 class="font-bold text-sm" :class="item.completed ? 'text-slate-900' : 'text-slate-400'">{{ item.title }}</h4>
            <p class="text-xs text-slate-500 mt-0.5">{{ item.desc }}</p>
          </div>
        </div>
      </div>

      <!-- Status Notice: Under Review (Waiting for Registrar Verification) -->
      <div v-if="application?.status === 'Under Review'" class="mt-8 p-6 rounded-3xl bg-amber-50/80 border-2 border-amber-200 text-amber-950 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start space-x-3.5">
          <div class="w-11 h-11 rounded-2xl bg-amber-100 border border-amber-300 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
            <Clock class="w-5 h-5 animate-pulse" />
          </div>
          <div>
            <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-amber-200/70 text-amber-900 text-[10px] font-bold uppercase tracking-wider mb-1">
              <span>Awaiting Registrar Evaluation</span>
            </div>
            <h4 class="text-base font-bold text-amber-950">Please Wait for Registrar Verification</h4>
            <p class="text-xs text-amber-900/80 mt-1 leading-relaxed max-w-xl">
              Your admission requirements have been successfully submitted and are currently being reviewed by the Office of the Registrar. Once your documents and LRN are verified, your section assignment and official queue number will be issued automatically.
            </p>
            <div class="flex items-center space-x-2 mt-2 text-[11px] text-amber-800 font-semibold">
              <span>💡 Tip: You can print your Pre-Enrollment Slip anytime in Step 4 while waiting.</span>
            </div>
          </div>
        </div>
        <button @click="activeStep = 4" class="px-5 py-2.5 rounded-xl font-bold bg-amber-700 hover:bg-amber-600 text-white text-xs shadow-md transition flex items-center space-x-1.5 shrink-0">
          <Printer class="w-4 h-4" />
          <span>View Slip (Step 4)</span>
        </button>
      </div>

      <!-- Status Notice: Deficient Documents -->
      <div v-if="application?.status === 'Requirements Deficient'" class="mt-8 p-6 rounded-3xl bg-rose-50 border-2 border-rose-200 text-rose-950 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start space-x-3.5">
          <div class="w-11 h-11 rounded-2xl bg-rose-100 border border-rose-300 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
            <AlertCircle class="w-5 h-5" />
          </div>
          <div>
            <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-rose-200/70 text-rose-900 text-[10px] font-bold uppercase tracking-wider mb-1">
              <span>Action Required</span>
            </div>
            <h4 class="text-base font-bold text-rose-950">Document Deficiency Found</h4>
            <p class="text-xs text-rose-900/80 mt-1 leading-relaxed max-w-xl">
              {{ application?.remarks || 'Some of your uploaded requirements require re-submission or clearer scans. Please replace the flagged files in Step 3.' }}
            </p>
          </div>
        </div>
        <button @click="activeStep = 3" class="px-5 py-2.5 rounded-xl font-bold bg-rose-600 hover:bg-rose-500 text-white text-xs shadow-md transition shrink-0">
          Fix Uploads (Step 3) →
        </button>
      </div>

      <!-- Action Box if approved & queued -->
      <div v-if="['Queued for Enrollment', 'Approved', 'Assessed'].includes(application?.status)" class="mt-8 p-6 rounded-3xl bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
        <div>
          <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold uppercase tracking-wider mb-1">
            <CheckCircle class="w-3 h-3 text-blue-600" />
            <span>Requirements Verified & Approved</span>
          </div>
          <h4 class="text-base font-extrabold text-slate-900 mt-0.5">Please Proceed to the Treasury Office</h4>
          <p class="text-xs text-slate-600 mt-1">
            Queue Number: <strong class="text-blue-700">#{{ application?.queue_info?.queue_number || '1' }}</strong> • Section: <strong class="text-slate-800">{{ application?.queue_info?.section_name || 'Assigned Section' }}</strong> • Student ID: <strong class="text-blue-700 font-mono">{{ application?.student_no || 'Generated' }}</strong>
          </p>
          <p class="text-xs text-blue-800 font-semibold mt-1">
            Present your printed Enrollment Slip at the Cashier window to settle the required downpayment.
          </p>
        </div>
        <button @click="activeStep = 4" class="px-5 py-2.5 rounded-xl font-bold bg-blue-600 hover:bg-blue-500 text-white text-xs shadow-md transition flex items-center space-x-1.5 shrink-0">
          <Printer class="w-4 h-4" />
          <span>View & Print Enrollment Slip (Step 4)</span>
        </button>
      </div>
    </div>
  </main>
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
              class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center font-bold transition"
            >
              ✕
            </button>
          </div>
        </div>

        <!-- Modal Body: PDF iframe or Image Viewer -->
        <div class="flex-1 p-4 sm:p-6 overflow-y-auto bg-slate-100 flex items-center justify-center min-h-[400px]">
          <!-- PDF Viewer -->
          <iframe 
            v-if="isPdf(previewDoc.file_path)" 
            :src="getFileUrl(previewDoc.file_path)" 
            class="w-full h-[70vh] rounded-xl border border-slate-300 bg-white shadow-inner"
          ></iframe>

          <!-- Image Viewer -->
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

    <!-- REMOVE UPLOADED DOCUMENT CONFIRMATION POPUP MODAL -->
    <div v-if="deleteDocModal.isOpen" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 text-xs space-y-4 animate-in fade-in zoom-in duration-150">
        
        <div class="flex items-start space-x-3.5 border-b border-slate-100 pb-3.5">
          <div class="p-3 rounded-2xl bg-rose-100 text-rose-700 border border-rose-200 shrink-0">
            <Trash2 class="w-6 h-6 text-rose-600" />
          </div>
          <div>
            <div class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase font-mono tracking-wider bg-rose-100 text-rose-900 border border-rose-200 mb-1">
              <span>Requirement Removal</span>
            </div>
            <h3 class="text-base font-extrabold text-slate-900 leading-snug">
              Remove Uploaded File?
            </h3>
            <p class="text-[11px] text-slate-500 mt-0.5">
              Requirement: <strong>{{ deleteDocModal.doc?.document_type }}</strong>
            </p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-slate-500 font-medium">Filename:</span>
            <span class="font-bold text-slate-900 truncate max-w-[200px]" :title="deleteDocModal.doc?.original_filename">{{ deleteDocModal.doc?.original_filename }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-slate-500 font-medium">Status:</span>
            <span class="font-bold text-slate-800">{{ deleteDocModal.doc?.status || 'Pending Verification' }}</span>
          </div>
        </div>

        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-900 flex items-start space-x-2">
          <AlertCircle class="w-4 h-4 text-amber-700 shrink-0 mt-0.5" />
          <span>You can upload an updated or clearer copy of this document immediately afterward.</span>
        </div>

        <div class="flex items-center justify-end space-x-2.5 pt-2 border-t border-slate-100">
          <button 
            type="button" 
            @click="deleteDocModal.isOpen = false" 
            :disabled="deleteDocModal.isDeleting"
            class="px-4 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs"
          >
            Cancel
          </button>
          
          <button 
            type="button" 
            @click="executeDeleteDocument()" 
            :disabled="deleteDocModal.isDeleting"
            class="px-5 py-2.5 rounded-xl font-bold bg-rose-600 hover:bg-rose-500 text-white shadow-md transition flex items-center space-x-2 text-xs"
          >
            <span v-if="deleteDocModal.isDeleting" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <Trash2 v-else class="w-3.5 h-3.5 text-white" />
            <span>{{ deleteDocModal.isDeleting ? 'Removing...' : 'Confirm Remove File' }}</span>
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { 
  CheckCircle, AlertCircle, FileText, Upload, Check, Printer, Eye, Trash2, Download,
  AlertTriangle, Clock, ArrowRight, FileCheck, GraduationCap, CreditCard,
  Lock, ChevronRight, User, BookOpen, UploadCloud, Activity
} from 'lucide-vue-next';
import api, { getFileUrl } from '../../services/api';

const router = useRouter();
const activeStep = ref(1);
const stepLockNotice = ref('');
const application = ref(null);
const academicOptions = ref({ grade_levels: [], tracks: [], strands: [] });
const isLoading = ref(false);
const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const goToStudentLogin = () => {
  localStorage.removeItem('sia_auth_token');
  localStorage.removeItem('sia_auth_user');
  window.dispatchEvent(new Event('auth-changed'));
  router.push('/login');
};

const deficientDocs = computed(() => {
  return application.value?.documents?.filter(d => d.status === 'Deficient' || d.status === 'Rejected') || [];
});

const verifiedDocs = computed(() => {
  return application.value?.documents?.filter(d => d.status === 'Verified') || [];
});

const pendingDocs = computed(() => {
  return application.value?.documents?.filter(d => d.status === 'Pending') || [];
});

const steps = [
  { id: 1, title: 'Personal Demographics', subtitle: 'LRN, Name & Contact' },
  { id: 2, title: 'Grade Level & Strand', subtitle: 'Academic Program Selection' },
  { id: 3, title: 'Send Requirements', subtitle: 'Upload PSA, SF9 & Credentials' },
  { id: 4, title: 'Print Enrollment Slip', subtitle: 'Pre-Assessment & Treasury Slip' },
  { id: 5, title: 'Admission Status Tracker', subtitle: 'Live Registrar Evaluation' }
];

const mandatoryDocs = computed(() => {
  return requiredDocsList.value.filter(r => r.required).map(r => r.type);
});

const hasAllMandatoryDocs = computed(() => {
  const uploaded = (application.value?.documents || []).filter(d => d.status !== 'Rejected' && d.status !== 'Deficient').map(d => d.document_type);
  return mandatoryDocs.value.length > 0 && mandatoryDocs.value.every(m => uploaded.includes(m));
});

const missingMandatoryDocs = computed(() => {
  const uploaded = (application.value?.documents || []).filter(d => d.status !== 'Rejected' && d.status !== 'Deficient').map(d => d.document_type);
  return mandatoryDocs.value.filter(m => !uploaded.includes(m));
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
  return isStep3Completed.value && ['Approved', 'Queued for Enrollment', 'Assessed', 'Enrolled'].includes(application.value?.status);
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
  if (stepNumber === 4 || stepNumber === 5) {
    return isStep3Completed.value;
  }
  return false;
};

const selectStep = (stepNumber) => {
  if (canAccessStep(stepNumber)) {
    activeStep.value = stepNumber;
    stepLockNotice.value = '';
  } else {
    if (stepNumber === 2) {
      stepLockNotice.value = 'Please complete and save your Demographics (Step 1) before proceeding.';
    } else if (stepNumber === 3) {
      stepLockNotice.value = 'Please select and save your Grade Level & Strand (Step 2) before proceeding.';
    } else if (stepNumber === 4 || stepNumber === 5) {
      if (!hasAllMandatoryDocs.value) {
        stepLockNotice.value = `Mandatory requirements incomplete: Please upload ${missingMandatoryDocs.value.join(' and ')} in Step 3 first.`;
      } else {
        stepLockNotice.value = 'Please click "Submit Requirements for Review" in Step 3 before proceeding.';
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
    if (!isStep3Completed.value) return 'Locked';
    const s = application.value?.status;
    if (s === 'Enrolled') return 'Enrolled';
    if (['Approved', 'Queued for Enrollment', 'Assessed'].includes(s)) return 'Approved / Queued';
    return 'Under Evaluation';
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

const statusBadgeClass = computed(() => {
  const s = application.value?.status;
  if (s === 'Enrolled') return 'bg-emerald-950 text-emerald-400 border border-emerald-500/40';
  if (s === 'Approved' || s === 'Queued for Enrollment') return 'bg-blue-950 text-blue-400 border border-blue-500/40';
  if (s === 'Requirements Deficient' || s === 'Rejected') return 'bg-rose-950 text-rose-400 border border-rose-500/40';
  return 'bg-amber-950 text-amber-400 border border-amber-500/40';
});

const requiredDocsList = computed(() => {
  const isSHSLevel = isSHS.value;
  const isTransferee = form.value.applicant_type === 'Transferee';
  const isVoucher = isSHSLevel && form.value.voucher_status && form.value.voucher_status !== 'None';

  const list = [
    { 
      type: 'PSA Birth Certificate', 
      required: true, 
      description: 'Clear copy of PSA Birth Certificate (SECPA copy)' 
    },
    { 
      type: 'SF9 / Form 138 (Report Card)', 
      required: true, 
      description: isSHSLevel 
        ? (isTransferee ? 'SHS Semestral Report Card / Transcript of Records' : 'Grade 10 Report Card with General Average & LRN') 
        : (isTransferee ? 'Previous Grade Level Report Card showing passing marks' : 'Grade 6 Elementary Report Card with LRN & Completion') 
    },
    { 
      type: 'Certificate of Good Moral Character', 
      required: true, 
      description: 'Issued by previous School Principal / Guidance Counselor' 
    },
    { 
      type: '2x2 ID Picture', 
      required: true, 
      description: 'Recent 2x2 colored photo in formal attire with white background' 
    }
  ];

  // Transferee requirements
  if (isTransferee) {
    list.push({
      type: 'Certificate of Transfer Credential / Honorable Dismissal',
      required: true,
      description: 'Official Transfer Clearance / Certificate of Eligibility to Transfer from previous school'
    });
  }

  // SHS Non-Transferee JHS Completers
  if (isSHSLevel && !isTransferee) {
    list.push({
      type: 'Certificate of JHS Completion',
      required: true,
      description: 'Junior High School Completion Certificate / Diploma'
    });
  }

  // SHS Voucher Grantees (ONLY shown if isSHS AND voucher_status !== 'None')
  if (isVoucher) {
    list.push({
      type: 'ESC Certificate / Voucher Cert',
      required: true,
      description: 'PEAC / DepEd Voucher Certificate / QVR Certificate for tuition subsidy validation'
    });
  }

  return list;
});

const getDocUploaded = (type) => {
  return application.value?.documents?.find(d => d.document_type === type);
};

const getCardStyleClass = (type) => {
  const doc = getDocUploaded(type);
  if (!doc) return 'border-slate-200 bg-slate-50/50 hover:border-slate-300';
  if (doc.status === 'Deficient' || doc.status === 'Rejected') {
    return 'border-rose-400 bg-rose-50/90 border-l-4 border-l-rose-600 shadow-md ring-1 ring-rose-300';
  }
  if (doc.status === 'Verified') {
    return 'border-emerald-300 bg-emerald-50/70 border-l-4 border-l-emerald-600 shadow-sm';
  }
  return 'border-amber-200 bg-amber-50/60 border-l-4 border-l-amber-500 shadow-sm';
};

const getIconBadgeClass = (type) => {
  const doc = getDocUploaded(type);
  if (!doc) return 'bg-white border-slate-200 text-slate-500';
  if (doc.status === 'Deficient' || doc.status === 'Rejected') {
    return 'bg-rose-100 border-rose-300 text-rose-600 shadow-sm';
  }
  if (doc.status === 'Verified') {
    return 'bg-emerald-100 border-emerald-300 text-emerald-600 shadow-sm';
  }
  return 'bg-amber-100 border-amber-300 text-amber-600 shadow-sm';
};

const getDocStatusClass = (status) => {
  if (status === 'Verified') return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
  if (status === 'Deficient' || status === 'Rejected') return 'bg-rose-600 text-white shadow-sm';
  return 'bg-amber-100 text-amber-800 border border-amber-300';
};

const statusTimeline = computed(() => {
  const s = application.value?.status;
  const isEnrolled = s === 'Enrolled';
  const isQueued = ['Queued for Enrollment', 'Assessed', 'Enrolled'].includes(s);
  const isApproved = ['Approved', 'Queued for Enrollment', 'Assessed', 'Enrolled'].includes(s);
  const isSubmitted = ['Under Review', 'Approved', 'Queued for Enrollment', 'Assessed', 'Enrolled'].includes(s);

  return [
    { title: '1. Admission Account Created', desc: 'Temporary account generated with application reference number.', completed: true },
    { title: '2. Requirements Submitted', desc: 'PSA, SF9, and credentials uploaded for evaluation.', completed: isSubmitted },
    { title: '3. Registrar Document Verification', desc: 'Registrar validates LRN, grades, and authenticity.', completed: isApproved },
    { title: '4. Queued for Enrollment', desc: 'Section assigned and Pre-Enrollment Assessment form generated.', completed: isQueued },
    { title: '5. Treasury Payment & Official Enrolled Account', desc: 'Downpayment processed and permanent Student Portal account created.', completed: isEnrolled }
  ];
});

const sampleSubjects = computed(() => {
  const glId = application.value?.grade_level_id || form.value.grade_level_id || 1;
  const strandId = application.value?.strand_id || form.value.strand_id;
  const allSubs = academicOptions.value?.subjects || [];

  if (allSubs.length > 0) {
    if (isSHS.value) {
      // SHS: Match Grade Level, 1st Semester, and Core/Applied (strand_id null) or Specialized (strand_id matching)
      const filtered = allSubs.filter(sub => {
        const matchesGl = sub.grade_level_id === glId;
        const matchesSem = sub.semester === '1st Semester';
        const matchesStrand = !sub.strand_id || sub.strand_id === strandId;
        return matchesGl && matchesSem && matchesStrand;
      });
      if (filtered.length > 0) return filtered;
    } else {
      // JHS: Match Grade Level (Full Year)
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
    { code: 'AP-7', title: 'Araling Panlipunan 7 (Araling Asyano)', category: 'JHS Core', units: '1.0' },
    { code: 'TLE-7', title: 'Technology and Livelihood Education 7', category: 'JHS Core', units: '1.0' },
    { code: 'MAPEH-7', title: 'MAPEH 7 (Music, Arts, PE, Health)', category: 'JHS Core', units: '1.0' }
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
      voucherDiscount = 12500; // 100% Tuition Subsidy
    } else if (vStatus === 'Private ESC Grantee (80%)') {
      voucherDiscount = 10000; // 80% Tuition Subsidy
    } else if (vStatus === 'Private Non-ESC Voucher (50%)') {
      voucherDiscount = 6250;  // 50% Tuition Subsidy
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

    // Auto navigate to Step 4/5 if already approved
    if (['Queued for Enrollment', 'Assessed', 'Enrolled'].includes(appRes.data.status)) {
      activeStep.value = 5;
    }
  } catch (err) {
    errorMessage.value = err.message || 'Failed to load admission details.';
  } finally {
    isLoading.value = false;
  }
};

const saveApplicationDetails = async (isStep2 = false) => {
  errorMessage.value = '';

  // Validate minimum age in Step 1 (at least 11 years old)
  if (!isStep2 && form.value.birthdate) {
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

const submitForEvaluation = async () => {
  isSubmitting.value = true;
  errorMessage.value = '';
  try {
    await api.submitApplication();
    successMessage.value = 'Application successfully submitted for Registrar review!';
    activeStep.value = 4;
    await loadData();
  } catch (err) {
    errorMessage.value = err.message || 'Submission failed. Make sure all required documents are uploaded.';
  } finally {
    isSubmitting.value = false;
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

const deleteDocModal = ref({
  isOpen: false,
  doc: null,
  isDeleting: false
});

const handleDeleteDocument = (doc) => {
  if (!doc || !doc.id) return;
  deleteDocModal.value = {
    isOpen: true,
    doc: doc,
    isDeleting: false
  };
};

const executeDeleteDocument = async () => {
  if (!deleteDocModal.value.doc) return;
  deleteDocModal.value.isDeleting = true;
  errorMessage.value = '';
  try {
    const res = await api.deleteDocument(deleteDocModal.value.doc.id);
    successMessage.value = res.message || `${deleteDocModal.value.doc.document_type} removed successfully.`;
    deleteDocModal.value.isOpen = false;
    await loadData();
  } catch (err) {
    errorMessage.value = err.message || 'Failed to remove document.';
  } finally {
    deleteDocModal.value.isDeleting = false;
  }
};

onMounted(() => {
  loadData();
});
</script>
