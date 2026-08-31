<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans">
    
    <!-- Top Header & Actions Bar (Standard Staff Layout) -->
    <div class="no-print flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6 pb-5 border-b border-slate-200">
      <div>
        <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
          <GraduationCap class="w-3.5 h-3.5 text-amber-600" />
          <span>Faculty Instruction & Academic Evaluation</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
          {{ teacherProfile?.first_name ? `Prof. ${teacherProfile.first_name} ${teacherProfile.last_name}` : 'Teacher & Faculty Portal' }}
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">
          DepEd Electronic Class Record (E-Class Record), Weekly Bell Timetable, Class Masterlists, and Advisory SF9 Core Values.
        </p>
      </div>

      <div class="flex items-center space-x-2.5 flex-wrap gap-y-2 shrink-0">
        <!-- School Year & Load Summary Pill -->
        <div class="hidden sm:flex items-center space-x-2 bg-amber-50 text-amber-900 border border-amber-200 px-3.5 py-1.5 rounded-xl text-xs font-medium font-mono">
          <span>S.Y. {{ activeSchoolYear?.name || '2026-2027' }}</span>
          <span class="text-amber-300">•</span>
          <span>Classes:</span>
          <strong class="text-amber-950 font-bold">{{ teachingClasses.length }}</strong>
          <span class="text-amber-300">•</span>
          <span>Periods:</span>
          <strong class="text-amber-950 font-bold">{{ weeklySchedules.length }}</strong>
        </div>

        <button 
          @click="loadTeacherDashboard()" 
          class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-medium shadow-2xs transition flex items-center space-x-1.5 cursor-pointer"
          title="Refresh teacher portal data"
        >
          <RefreshCw class="w-3.5 h-3.5" :class="{ 'animate-spin': isLoading }" />
          <span>Refresh</span>
        </button>
      </div>
    </div>

    <!-- Quick Metrics Summary -->
    <div class="no-print grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
      <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs">
        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Assigned Classes</span>
        <strong class="text-2xl font-bold text-slate-900 font-mono mt-1 block">{{ dashboardStats.total_classes || 0 }}</strong>
        <span class="text-[10px] text-slate-400">Subject teaching blocks</span>
      </div>

      <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs">
        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Weekly Periods</span>
        <strong class="text-2xl font-bold text-amber-600 font-mono mt-1 block">{{ dashboardStats.total_schedule_periods || 0 }}</strong>
        <span class="text-[10px] text-slate-400">Scheduled bell slots</span>
      </div>

      <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs">
        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Enrolled Learners</span>
        <strong class="text-2xl font-bold text-emerald-600 font-mono mt-1 block">{{ dashboardStats.total_students || 0 }}</strong>
        <span class="text-[10px] text-slate-400">Total student roster</span>
      </div>

      <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs">
        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Advisory Class</span>
        <strong class="text-2xl font-bold text-purple-600 font-mono mt-1 block">{{ dashboardStats.total_advisory_sections || 0 }}</strong>
        <span class="text-[10px] text-slate-400">Homeroom sections</span>
      </div>
    </div>

    <!-- Feedback Alerts -->
    <div v-if="feedbackMessage" class="no-print mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between shadow-xs">
      <div class="flex items-center space-x-2">
        <CheckCircle class="w-4 h-4 text-emerald-600 shrink-0" />
        <span>{{ feedbackMessage }}</span>
      </div>
      <button @click="feedbackMessage = ''" class="text-emerald-500 hover:text-emerald-700 font-bold cursor-pointer">✕</button>
    </div>

    <div v-if="errorMessage" class="no-print mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center justify-between shadow-xs">
      <div class="flex items-center space-x-2">
        <AlertCircle class="w-4 h-4 text-rose-600 shrink-0" />
        <span>{{ errorMessage }}</span>
      </div>
      <button @click="errorMessage = ''" class="text-rose-500 hover:text-rose-700 font-bold cursor-pointer">✕</button>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 1: WEEKLY MASTER TIMETABLE & TEACHING LOADS          -->
    <!-- ======================================================== -->
    <div v-if="activeTab === 'schedule'" class="space-y-6">
      
      <!-- Weekly Bell Timetable (Monday - Friday) -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
          <div>
            <h2 class="text-base font-bold text-slate-900">Weekly Master Bell Timetable</h2>
            <p class="text-xs text-slate-500">Visual timetable matrix for your instructional periods across all assigned sections.</p>
          </div>
          <div class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl self-start">
            {{ weeklySchedules.length }} Assigned Time Blocks
          </div>
        </div>

        <!-- Schedule Days Columns -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3.5">
          <div v-for="day in ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']" :key="day" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
            <div class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-200 pb-2 flex items-center justify-between">
              <span>{{ day }}</span>
              <span class="text-[10px] font-mono font-normal text-slate-500">{{ getDaySchedules(day).length }} classes</span>
            </div>

            <div v-if="getDaySchedules(day).length === 0" class="py-8 text-center text-slate-400 text-[11px]">
              No classes scheduled
            </div>

            <div 
              v-for="s in getDaySchedules(day)" 
              :key="s.id"
              class="p-3 rounded-xl bg-white border border-slate-200 shadow-2xs hover:border-blue-500 hover:shadow-xs transition space-y-2 group cursor-pointer"
              @click="openClassRecord(s.section_id, s.subject_id)"
            >
              <div class="flex items-center justify-between text-[10px] font-mono text-slate-500">
                <span class="font-bold text-blue-900">{{ formatTime(s.time_start) }} - {{ formatTime(s.time_end) }}</span>
                <span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 font-sans">{{ s.room || 'Room' }}</span>
              </div>
              <div class="font-bold text-xs text-slate-900 group-hover:text-blue-900 transition leading-snug">
                {{ s.subject_name }}
              </div>
              <div class="flex items-center justify-between text-[11px] text-slate-600 pt-1 border-t border-slate-50">
                <span class="font-medium text-slate-700 truncate mr-2">{{ s.section_name }}</span>
                <span class="text-[10px] px-1.5 py-0.5 rounded font-bold uppercase bg-blue-50 text-blue-800 font-mono shrink-0">
                  {{ s.grade_level_code }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Assigned Teaching Loads Directory Cards -->
      <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
          <div>
            <h2 class="text-base font-bold text-slate-900">Teaching Loads Directory</h2>
            <p class="text-xs text-slate-500">All registered subject-section combinations assigned to your instructional load.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div 
            v-for="c in teachingClasses" 
            :key="`${c.section_id}-${c.subject_id}`"
            class="p-5 rounded-2xl border border-slate-200 bg-white hover:border-blue-900 hover:shadow-xs transition space-y-4 flex flex-col justify-between"
          >
            <div class="space-y-2.5">
              <div class="flex items-center justify-between">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-900 border border-blue-200">
                  {{ c.grade_level_code }} • {{ c.section_name }}
                </span>
                <span class="text-[11px] font-mono font-medium text-slate-500">{{ c.units }} Units</span>
              </div>
              <h3 class="font-bold text-sm text-slate-900 leading-snug">{{ c.subject_name }}</h3>
              <div class="text-xs text-slate-500 flex items-center space-x-2">
                <span class="font-mono text-slate-600">{{ c.subject_code }}</span>
                <span>•</span>
                <span>{{ c.section_room || 'Room' }}</span>
                <span>•</span>
                <span>{{ c.subject_category || 'Core' }}</span>
              </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
              <div class="text-xs text-slate-600">
                <span class="font-bold text-slate-900">{{ c.enrolled_count || 0 }}</span> Enrolled Learners
              </div>
              <button 
                @click="openClassRecord(c.section_id, c.subject_id)"
                type="button" 
                class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-blue-900 hover:bg-blue-800 text-white shadow-2xs transition flex items-center space-x-1.5 cursor-pointer"
              >
                <FileSpreadsheet class="w-3.5 h-3.5" />
                <span>Open E-Class Record</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 2: ELECTRONIC CLASS RECORD (E-CLASS RECORD & GRADES) -->
    <!-- ======================================================== -->
    <div v-if="activeTab === 'grading'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
      
      <!-- Top Action & Selection Bar -->
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
          <div class="flex items-center space-x-2">
            <h2 class="text-base font-bold text-slate-900">DepEd Electronic Class Record (E-Class Record)</h2>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-900 font-mono">
              DepEd Order 8, s. 2015
            </span>
          </div>
          <p class="text-xs text-slate-500 mt-0.5">
            Encode quarterly grades (0–100). Passing mark is 75.00. Final ratings calculate dynamically according to DepEd curriculum guidelines.
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
          <!-- Class Selector Dropdown -->
          <select 
            v-model="selectedClassKey" 
            @change="handleClassChange()"
            class="px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer shadow-2xs"
          >
            <option value="">-- Select Class Section & Subject --</option>
            <option 
              v-for="c in teachingClasses" 
              :key="`${c.section_id}-${c.subject_id}`" 
              :value="`${c.section_id}-${c.subject_id}`"
            >
              {{ c.grade_level_code }} - {{ c.section_name }} : {{ c.subject_name }} ({{ c.enrolled_count }} learners)
            </option>
          </select>

          <!-- Quick Fill Helper Dropdown -->
          <button 
            v-if="selectedClassKey"
            @click="showQuickFillModal = true"
            type="button"
            class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-2xs transition flex items-center space-x-1.5 cursor-pointer"
          >
            <Sparkles class="w-3.5 h-3.5 text-amber-500" />
            <span>Quick Batch Fill</span>
          </button>

          <!-- Save Grades Button -->
          <button 
            @click="saveGradesBatch()" 
            :disabled="isSavingGrades || !selectedClassKey" 
            type="button" 
            class="px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white shadow-xs transition flex items-center space-x-1.5 cursor-pointer"
          >
            <Check class="w-4 h-4" />
            <span>{{ isSavingGrades ? 'Saving Grades...' : 'Save & Submit Grades' }}</span>
          </button>
        </div>
      </div>

      <!-- Class Active Header Details -->
      <div v-if="currentClassData.section" class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
        <div class="space-y-0.5">
          <div class="font-extrabold text-blue-950 text-sm">
            {{ currentClassData.section.name }} • {{ currentClassData.subject.name }}
          </div>
          <div class="text-slate-600 text-[11px]">
            Grade Level: <strong class="text-slate-900">{{ currentClassData.section.grade_level_name }}</strong> | 
            Classification: <strong class="text-slate-900">{{ currentClassData.subject.classification }}</strong> | 
            Term: <strong class="text-slate-900">{{ currentClassData.subject.semester || 'Full Academic Year' }}</strong>
          </div>
        </div>

        <!-- Filter / Search In Roster -->
        <div class="relative w-full sm:w-64">
          <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" />
          <input 
            v-model="searchGradeQuery" 
            type="text" 
            placeholder="Search student or LRN..." 
            class="w-full pl-9 pr-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"
          />
        </div>
      </div>

      <!-- Electronic Class Record Table -->
      <div v-if="selectedClassKey" class="overflow-x-auto">
        <table class="w-full text-xs text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
              <th class="py-3 px-4 w-12 text-slate-400 font-mono">#</th>
              <th class="py-3 px-4">Learner Name</th>
              <th class="py-3 px-4 font-mono">LRN / Student No</th>
              <th class="py-3 px-4 text-center">Gender</th>
              <th class="py-3 px-3 text-center w-24">Q1 (1st)</th>
              <th class="py-3 px-3 text-center w-24">Q2 (2nd)</th>
              <th class="py-3 px-3 text-center w-24">Q3 (3rd)</th>
              <th class="py-3 px-3 text-center w-24">Q4 (4th)</th>
              <th class="py-3 px-4 text-center w-28">Final Grade</th>
              <th class="py-3 px-4 text-center">Remarks</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="(s, idx) in filteredClassStudents" :key="s.student_id" class="hover:bg-slate-50/80 transition">
              <td class="py-3.5 px-4 font-mono text-slate-400 text-[11px]">{{ idx + 1 }}</td>
              <td class="py-3.5 px-4 font-bold text-slate-900 whitespace-nowrap">{{ s.full_name }}</td>
              <td class="py-3.5 px-4 font-mono text-slate-600 whitespace-nowrap text-[11px]">
                <div>{{ s.lrn || 'No LRN' }}</div>
                <div class="text-[10px] text-slate-400">{{ s.student_no }}</div>
              </td>
              <td class="py-3.5 px-4 text-center">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold" :class="s.gender === 'Female' ? 'bg-pink-50 text-pink-700 border border-pink-200' : 'bg-blue-50 text-blue-700 border border-blue-200'">
                  {{ s.gender }}
                </span>
              </td>

              <!-- Q1 Input -->
              <td class="py-2.5 px-2 text-center">
                <input 
                  v-model.number="s.q1" 
                  @input="recalculateStudentGrade(s)"
                  type="number" 
                  min="0" 
                  max="100" 
                  step="0.01"
                  placeholder="--"
                  class="w-18 px-2 py-1.5 rounded-lg border border-slate-300 text-center font-mono font-bold text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none focus:bg-blue-50"
                />
              </td>

              <!-- Q2 Input -->
              <td class="py-2.5 px-2 text-center">
                <input 
                  v-model.number="s.q2" 
                  @input="recalculateStudentGrade(s)"
                  type="number" 
                  min="0" 
                  max="100" 
                  step="0.01"
                  placeholder="--"
                  class="w-18 px-2 py-1.5 rounded-lg border border-slate-300 text-center font-mono font-bold text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none focus:bg-blue-50"
                />
              </td>

              <!-- Q3 Input -->
              <td class="py-2.5 px-2 text-center">
                <input 
                  v-model.number="s.q3" 
                  @input="recalculateStudentGrade(s)"
                  type="number" 
                  min="0" 
                  max="100" 
                  step="0.01"
                  placeholder="--"
                  class="w-18 px-2 py-1.5 rounded-lg border border-slate-300 text-center font-mono font-bold text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none focus:bg-blue-50"
                />
              </td>

              <!-- Q4 Input -->
              <td class="py-2.5 px-2 text-center">
                <input 
                  v-model.number="s.q4" 
                  @input="recalculateStudentGrade(s)"
                  type="number" 
                  min="0" 
                  max="100" 
                  step="0.01"
                  placeholder="--"
                  class="w-18 px-2 py-1.5 rounded-lg border border-slate-300 text-center font-mono font-bold text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none focus:bg-blue-50"
                />
              </td>

              <!-- Final Grade (Computed) -->
              <td class="py-3.5 px-4 text-center font-mono font-bold text-xs">
                <span v-if="s.final_grade !== null" :class="s.final_grade >= 75 ? 'text-emerald-700' : 'text-rose-700'">
                  {{ s.final_grade.toFixed(2) }}
                </span>
                <span v-else class="text-slate-300">--</span>
              </td>

              <!-- Remarks Badge -->
              <td class="py-3.5 px-4 text-center">
                <span 
                  class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                  :class="{
                    'bg-emerald-50 text-emerald-800 border border-emerald-200': s.remarks === 'Passed',
                    'bg-rose-50 text-rose-800 border border-rose-200': s.remarks === 'Failed',
                    'bg-slate-100 text-slate-600': s.remarks === 'Ongoing' || !s.remarks
                  }"
                >
                  {{ s.remarks || 'Ongoing' }}
                </span>
              </td>
            </tr>

            <tr v-if="filteredClassStudents.length === 0">
              <td colspan="10" class="py-12 text-center text-slate-400 text-xs">
                <Users class="w-8 h-8 text-slate-300 mx-auto mb-2" />
                <div>No learners enrolled in this section match your search filter.</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="py-16 text-center text-slate-400 text-xs border border-dashed border-slate-200 rounded-3xl">
        <BookOpen class="w-10 h-10 text-slate-300 mx-auto mb-3" />
        <div class="font-bold text-slate-700 text-sm">Select a Class Section & Subject to begin grading</div>
        <p class="text-slate-400 mt-1">Choose an assigned teaching load from the dropdown above to load the E-Class Record.</p>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 3: CLASS MASTERLISTS & STUDENT ROSTER                -->
    <!-- ======================================================== -->
    <div v-if="activeTab === 'roster'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
          <h2 class="text-base font-bold text-slate-900">Class Masterlists & Student Directory</h2>
          <p class="text-xs text-slate-500">Official student profile list, LRNs, contact details, and enrollment verifications.</p>
        </div>
        
        <select 
          v-model="selectedClassKey" 
          @change="handleClassChange()"
          class="px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer shadow-2xs"
        >
          <option value="">-- Select Class Section --</option>
          <option 
            v-for="c in teachingClasses" 
            :key="`${c.section_id}-${c.subject_id}`" 
            :value="`${c.section_id}-${c.subject_id}`"
          >
            {{ c.grade_level_code }} - {{ c.section_name }} : {{ c.subject_name }}
          </option>
        </select>
      </div>

      <div v-if="selectedClassKey" class="overflow-x-auto">
        <table class="w-full text-xs text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
              <th class="py-3 px-4 w-12 text-slate-400 font-mono">#</th>
              <th class="py-3 px-4">Learner Full Name</th>
              <th class="py-3 px-4 font-mono">LRN</th>
              <th class="py-3 px-4 font-mono">Student No</th>
              <th class="py-3 px-4 text-center">Gender</th>
              <th class="py-3 px-4">Contact Number</th>
              <th class="py-3 px-4">Email Address</th>
              <th class="py-3 px-4 text-center">Enrollment Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="(s, idx) in currentClassData.students || []" :key="s.student_id" class="hover:bg-slate-50/80 transition">
              <td class="py-3.5 px-4 font-mono text-slate-400 text-[11px]">{{ idx + 1 }}</td>
              <td class="py-3.5 px-4 font-bold text-slate-900">{{ s.full_name }}</td>
              <td class="py-3.5 px-4 font-mono text-slate-600">{{ s.lrn || 'N/A' }}</td>
              <td class="py-3.5 px-4 font-mono text-blue-900 font-bold">{{ s.student_no }}</td>
              <td class="py-3.5 px-4 text-center">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold" :class="s.gender === 'Female' ? 'bg-pink-50 text-pink-700 border border-pink-200' : 'bg-blue-50 text-blue-700 border border-blue-200'">
                  {{ s.gender }}
                </span>
              </td>
              <td class="py-3.5 px-4 font-mono text-slate-600">{{ s.contact_number || 'N/A' }}</td>
              <td class="py-3.5 px-4 text-slate-600">{{ s.email || 'N/A' }}</td>
              <td class="py-3.5 px-4 text-center">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                  Officially Enrolled
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 4: ADVISORY SECTION & SF9 CORE VALUES                -->
    <!-- ======================================================== -->
    <div v-if="activeTab === 'advisory'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
          <div class="flex items-center space-x-2">
            <h2 class="text-base font-bold text-slate-900">Homeroom Advisory Section & SF9 Core Values</h2>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-900 font-mono">
              Class Adviser
            </span>
          </div>
          <p class="text-xs text-slate-500 mt-0.5">
            DepEd SF9 Learner Core Values Matrix (AO = Always Observed, SO = Sometimes Observed, RO = Rarely Observed, NO = Not Observed).
          </p>
        </div>

        <div class="flex items-center space-x-2">
          <button 
            v-if="advisoryData.has_advisory"
            @click="setAllValues('AO')"
            type="button"
            class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-2xs transition flex items-center space-x-1.5 cursor-pointer"
          >
            <Sparkles class="w-3.5 h-3.5 text-purple-600" />
            <span>Mark All AO</span>
          </button>

          <button 
            @click="saveAdvisoryValues()" 
            :disabled="!advisoryData.has_advisory" 
            type="button" 
            class="px-4 py-2 rounded-xl text-xs font-semibold bg-purple-900 hover:bg-purple-800 disabled:opacity-50 text-white shadow-xs transition flex items-center space-x-1.5 cursor-pointer"
          >
            <Check class="w-4 h-4" />
            <span>Save SF9 Ratings</span>
          </button>
        </div>
      </div>

      <div v-if="advisoryData.has_advisory" class="space-y-4">
        <!-- Advisory Section Card -->
        <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200 flex flex-wrap items-center justify-between gap-3 text-xs">
          <div>
            <div class="font-extrabold text-purple-950 text-sm">
              {{ advisoryData.section?.name }} ({{ advisoryData.section?.grade_level_name }})
            </div>
            <div class="text-slate-600 text-[11px] mt-0.5">
              Room: <strong>{{ advisoryData.section?.room }}</strong> | Total Learners: <strong>{{ advisoryData.total_learners }}</strong>
            </div>
          </div>
        </div>

        <!-- SF9 Core Values Matrix Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-xs text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                <th class="py-3 px-4 w-12 text-slate-400 font-mono">#</th>
                <th class="py-3 px-4">Learner Name</th>
                <th class="py-3 px-4 text-center">Maka-Diyos</th>
                <th class="py-3 px-4 text-center">Makatao</th>
                <th class="py-3 px-4 text-center">Makakalikasan</th>
                <th class="py-3 px-4 text-center">Makabansa</th>
                <th class="py-3 px-4 text-center font-mono">Gen. Average</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="(l, idx) in advisoryData.learners" :key="l.student_id" class="hover:bg-slate-50/80 transition">
                <td class="py-3.5 px-4 font-mono text-slate-400 text-[11px]">{{ idx + 1 }}</td>
                <td class="py-3.5 px-4 font-bold text-slate-900 whitespace-nowrap">{{ l.full_name }}</td>

                <!-- Maka-Diyos Select -->
                <td class="py-2.5 px-3 text-center">
                  <select v-model="l.values_ratings.maka_diyos_q1" class="px-2.5 py-1 rounded-lg border border-slate-300 text-xs font-bold focus:ring-2 focus:ring-purple-500 focus:outline-none cursor-pointer">
                    <option value="AO">AO</option>
                    <option value="SO">SO</option>
                    <option value="RO">RO</option>
                    <option value="NO">NO</option>
                  </select>
                </td>

                <!-- Makatao Select -->
                <td class="py-2.5 px-3 text-center">
                  <select v-model="l.values_ratings.maka_tao_q1" class="px-2.5 py-1 rounded-lg border border-slate-300 text-xs font-bold focus:ring-2 focus:ring-purple-500 focus:outline-none cursor-pointer">
                    <option value="AO">AO</option>
                    <option value="SO">SO</option>
                    <option value="RO">RO</option>
                    <option value="NO">NO</option>
                  </select>
                </td>

                <!-- Makakalikasan Select -->
                <td class="py-2.5 px-3 text-center">
                  <select v-model="l.values_ratings.makakalikasan_q1" class="px-2.5 py-1 rounded-lg border border-slate-300 text-xs font-bold focus:ring-2 focus:ring-purple-500 focus:outline-none cursor-pointer">
                    <option value="AO">AO</option>
                    <option value="SO">SO</option>
                    <option value="RO">RO</option>
                    <option value="NO">NO</option>
                  </select>
                </td>

                <!-- Makabansa Select -->
                <td class="py-2.5 px-3 text-center">
                  <select v-model="l.values_ratings.makabansa_q1" class="px-2.5 py-1 rounded-lg border border-slate-300 text-xs font-bold focus:ring-2 focus:ring-purple-500 focus:outline-none cursor-pointer">
                    <option value="AO">AO</option>
                    <option value="SO">SO</option>
                    <option value="RO">RO</option>
                    <option value="NO">NO</option>
                  </select>
                </td>

                <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800">
                  {{ l.general_average ? l.general_average.toFixed(2) : '--' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-else class="py-16 text-center text-slate-400 text-xs border border-dashed border-slate-200 rounded-3xl">
        <Award class="w-10 h-10 text-slate-300 mx-auto mb-2" />
        <div class="font-bold text-slate-700">No Advisory Section Assigned</div>
        <p class="text-slate-400 mt-1">This faculty account is currently assigned purely for subject instruction.</p>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 5: DAILY ATTENDANCE TRACKER (SF2)                    -->
    <!-- ======================================================== -->
    <div v-if="activeTab === 'attendance'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
          <h2 class="text-base font-bold text-slate-900">Daily Attendance Log (DepEd SF2)</h2>
          <p class="text-xs text-slate-500">Record daily learner attendance status (Present, Absent, Late, Excused).</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <input 
            v-model="attendanceDate" 
            type="date" 
            class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none cursor-pointer"
          />
          <button 
            @click="markAllPresent()" 
            type="button" 
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800 transition cursor-pointer"
          >
            Mark All Present
          </button>
          <button 
            @click="saveAttendanceLog()" 
            type="button" 
            class="px-4 py-1.5 rounded-xl text-xs font-semibold bg-blue-900 hover:bg-blue-800 text-white shadow-xs transition cursor-pointer"
          >
            Save Attendance Log
          </button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
              <th class="py-3 px-4 w-12 text-slate-400 font-mono">#</th>
              <th class="py-3 px-4">Learner Name</th>
              <th class="py-3 px-4 font-mono">LRN</th>
              <th class="py-3 px-4 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="(s, idx) in attendanceStudents" :key="s.student_id" class="hover:bg-slate-50/80 transition">
              <td class="py-3.5 px-4 font-mono text-slate-400 text-[11px]">{{ idx + 1 }}</td>
              <td class="py-3.5 px-4 font-bold text-slate-900">{{ s.full_name }}</td>
              <td class="py-3.5 px-4 font-mono text-slate-600">{{ s.lrn || 'N/A' }}</td>
              <td class="py-2.5 px-4 text-center">
                <div class="inline-flex rounded-xl p-1 bg-slate-100 space-x-1">
                  <button 
                    v-for="st in ['Present', 'Late', 'Absent', 'Excused']" 
                    :key="st"
                    @click="s.attendance_status = st"
                    type="button"
                    :class="[
                      'px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase transition cursor-pointer',
                      s.attendance_status === st 
                        ? (st === 'Present' ? 'bg-emerald-600 text-white shadow-2xs' : st === 'Late' ? 'bg-amber-500 text-white shadow-2xs' : st === 'Absent' ? 'bg-rose-600 text-white shadow-2xs' : 'bg-blue-600 text-white shadow-2xs')
                        : 'text-slate-500 hover:text-slate-800'
                    ]"
                  >
                    {{ st }}
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Quick Batch Fill Grades Modal -->
    <div v-if="showQuickFillModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center space-x-2">
            <Sparkles class="w-5 h-5 text-amber-500" />
            <h3 class="font-bold text-slate-900 text-base">Quick Fill Quarterly Scores</h3>
          </div>
          <button @click="showQuickFillModal = false" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">✕</button>
        </div>

        <p class="text-xs text-slate-500">
          Quickly populate empty score fields for all learners in this class for rapid drafting.
        </p>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-semibold text-slate-700 block mb-1">Select Target Quarter</label>
            <select v-model="quickFillQuarter" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-blue-500 focus:outline-none">
              <option value="q1">Quarter 1 (1st Quarter)</option>
              <option value="q2">Quarter 2 (2nd Quarter)</option>
              <option value="q3">Quarter 3 (3rd Quarter)</option>
              <option value="q4">Quarter 4 (4th Quarter)</option>
              <option value="all">All Quarters (Q1 to Q4)</option>
            </select>
          </div>

          <div>
            <label class="font-semibold text-slate-700 block mb-1">Score Value (0 - 100)</label>
            <input 
              v-model.number="quickFillScore" 
              type="number" 
              min="0" 
              max="100" 
              placeholder="e.g. 85.00" 
              class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
          </div>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
          <button 
            @click="showQuickFillModal = false" 
            type="button" 
            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition cursor-pointer"
          >
            Cancel
          </button>
          <button 
            @click="applyQuickFill()" 
            type="button" 
            class="px-4 py-2 rounded-xl text-xs font-semibold bg-blue-900 hover:bg-blue-800 text-white shadow-xs transition cursor-pointer"
          >
            Apply to Class
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { 
  GraduationCap, Clock, BookOpen, Users, Award, Calendar, 
  FileSpreadsheet, CheckCircle, Search, Check, RefreshCw, 
  Sparkles, AlertCircle
} from 'lucide-vue-next';
import api from '../../services/api';

const route = useRoute();
const router = useRouter();

const activeTab = ref('schedule');
const isLoading = ref(false);
const isSavingGrades = ref(false);
const feedbackMessage = ref('');
const errorMessage = ref('');

watch(() => route.query.tab, (newTab) => {
  if (newTab && ['schedule', 'grading', 'roster', 'advisory', 'attendance'].includes(newTab)) {
    activeTab.value = newTab;
  }
}, { immediate: true });

// Data State
const teacherProfile = ref(null);
const activeSchoolYear = ref(null);
const dashboardStats = ref({});
const weeklySchedules = ref([]);
const teachingClasses = ref([]);
const advisorySections = ref([]);

const selectedClassKey = ref('');
const currentClassData = ref({ section: null, subject: null, students: [] });
const searchGradeQuery = ref('');

const advisoryData = ref({ has_advisory: false, section: null, learners: [] });
const attendanceDate = ref(new Date().toISOString().split('T')[0]);
const attendanceStudents = ref([]);

// Quick fill modal state
const showQuickFillModal = ref(false);
const quickFillQuarter = ref('q1');
const quickFillScore = ref(85);

// Load Dashboard Data
const loadTeacherDashboard = async () => {
  isLoading.value = true;
  feedbackMessage.value = '';
  errorMessage.value = '';
  try {
    const res = await api.getTeacherDashboard();
    const data = res.data;
    teacherProfile.value = data.teacher;
    activeSchoolYear.value = data.school_year;
    dashboardStats.value = data.stats || {};
    weeklySchedules.value = data.weekly_schedules || [];
    teachingClasses.value = data.classes || [];
    advisorySections.value = data.advisory_sections || [];

    // If first class exists and no class selected, default to first class
    if (teachingClasses.value.length > 0 && !selectedClassKey.value) {
      const first = teachingClasses.value[0];
      selectedClassKey.value = `${first.section_id}-${first.subject_id}`;
      await loadClassStudents(first.section_id, first.subject_id);
    }

    // Load Advisory Data
    await loadAdvisorySection();
  } catch (err) {
    console.error('Failed to load teacher dashboard:', err);
    errorMessage.value = err.message || 'Failed to load teacher portal data.';
  } finally {
    isLoading.value = false;
  }
};

const getDaySchedules = (day) => {
  return weeklySchedules.value.filter(s => s.day_of_week === day);
};

const formatTime = (timeStr) => {
  if (!timeStr) return '';
  const [h, m] = timeStr.split(':');
  let hour = parseInt(h, 10);
  const ampm = hour >= 12 ? 'PM' : 'AM';
  hour = hour % 12 || 12;
  return `${hour}:${m} ${ampm}`;
};

const openClassRecord = async (sectionId, subjectId) => {
  selectedClassKey.value = `${sectionId}-${subjectId}`;
  activeTab.value = 'grading';
  router.push({ query: { tab: 'grading' } });
  await loadClassStudents(sectionId, subjectId);
};

const handleClassChange = async () => {
  if (!selectedClassKey.value) return;
  const [secId, subId] = selectedClassKey.value.split('-').map(Number);
  await loadClassStudents(secId, subId);
};

const loadClassStudents = async (sectionId, subjectId) => {
  try {
    const res = await api.getTeacherClassStudents(sectionId, subjectId);
    currentClassData.value = res.data;

    // Prepare attendance list clone
    attendanceStudents.value = (res.data.students || []).map(s => ({
      ...s,
      attendance_status: 'Present'
    }));
  } catch (err) {
    console.error('Failed to load class students:', err);
  }
};

const recalculateStudentGrade = (student) => {
  const isSHS = (currentClassData.value.section?.level_category || '') === 'SHS';
  const semester = currentClassData.value.subject?.semester || '1st Semester';

  const q1 = student.q1 !== null && student.q1 !== '' ? Number(student.q1) : null;
  const q2 = student.q2 !== null && student.q2 !== '' ? Number(student.q2) : null;
  const q3 = student.q3 !== null && student.q3 !== '' ? Number(student.q3) : null;
  const q4 = student.q4 !== null && student.q4 !== '' ? Number(student.q4) : null;

  if (isSHS) {
    if (semester.toLowerCase().includes('2nd')) {
      if (q3 !== null && q4 !== null) {
        student.final_grade = Math.round(((q3 + q4) / 2) * 100) / 100;
        student.remarks = student.final_grade >= 75 ? 'Passed' : 'Failed';
      } else {
        student.final_grade = null;
        student.remarks = 'Ongoing';
      }
    } else {
      if (q1 !== null && q2 !== null) {
        student.final_grade = Math.round(((q1 + q2) / 2) * 100) / 100;
        student.remarks = student.final_grade >= 75 ? 'Passed' : 'Failed';
      } else {
        student.final_grade = null;
        student.remarks = 'Ongoing';
      }
    }
  } else {
    // JHS Full Year
    if (q1 !== null && q2 !== null && q3 !== null && q4 !== null) {
      student.final_grade = Math.round(((q1 + q2 + q3 + q4) / 4) * 100) / 100;
      student.remarks = student.final_grade >= 75 ? 'Passed' : 'Failed';
    } else {
      student.final_grade = null;
      student.remarks = 'Ongoing';
    }
  }
};

const filteredClassStudents = computed(() => {
  const list = currentClassData.value.students || [];
  if (!searchGradeQuery.value.trim()) return list;
  const q = searchGradeQuery.value.toLowerCase().trim();
  return list.filter(s => 
    s.full_name.toLowerCase().includes(q) ||
    (s.lrn && s.lrn.toLowerCase().includes(q)) ||
    (s.student_no && s.student_no.toLowerCase().includes(q))
  );
});

const applyQuickFill = () => {
  const score = Number(quickFillScore.value) || 0;
  const list = currentClassData.value.students || [];

  list.forEach(s => {
    if (quickFillQuarter.value === 'all') {
      s.q1 = score;
      s.q2 = score;
      s.q3 = score;
      s.q4 = score;
    } else {
      s[quickFillQuarter.value] = score;
    }
    recalculateStudentGrade(s);
  });

  showQuickFillModal.value = false;
  feedbackMessage.value = `Applied score of ${score} to ${quickFillQuarter.value.toUpperCase()}.`;
  setTimeout(() => { feedbackMessage.value = ''; }, 3500);
};

const saveGradesBatch = async () => {
  if (!selectedClassKey.value) return;
  const [secId, subId] = selectedClassKey.value.split('-').map(Number);

  isSavingGrades.value = true;
  feedbackMessage.value = '';
  errorMessage.value = '';

  try {
    const gradesPayload = (currentClassData.value.students || []).map(s => ({
      student_id: s.student_id,
      q1: s.q1,
      q2: s.q2,
      q3: s.q3,
      q4: s.q4
    }));

    const res = await api.saveTeacherGrades({
      section_id: secId,
      subject_id: subId,
      grades: gradesPayload
    });

    feedbackMessage.value = res.message || 'Grades saved successfully!';
    setTimeout(() => { feedbackMessage.value = ''; }, 4000);
    await loadClassStudents(secId, subId);
  } catch (err) {
    errorMessage.value = 'Failed to save grades: ' + (err.message || 'Error occurred.');
  } finally {
    isSavingGrades.value = false;
  }
};

const loadAdvisorySection = async () => {
  try {
    const res = await api.getTeacherAdvisorySection();
    advisoryData.value = res.data;
  } catch (err) {
    console.error('Failed to load advisory section:', err);
  }
};

const setAllValues = (rating) => {
  if (!advisoryData.value.learners) return;
  advisoryData.value.learners.forEach(l => {
    l.values_ratings = {
      maka_diyos_q1: rating,
      maka_diyos_q2: rating,
      maka_tao_q1: rating,
      maka_tao_q2: rating,
      makakalikasan_q1: rating,
      makakalikasan_q2: rating,
      makabansa_q1: rating,
      makabansa_q2: rating
    };
  });
  feedbackMessage.value = `Marked all Core Values as ${rating} (${rating === 'AO' ? 'Always Observed' : rating}).`;
  setTimeout(() => { feedbackMessage.value = ''; }, 3500);
};

const saveAdvisoryValues = async () => {
  if (!advisoryData.value.section?.id) return;
  try {
    await api.saveTeacherAdvisoryValues({
      section_id: advisoryData.value.section.id,
      values: advisoryData.value.learners
    });
    feedbackMessage.value = 'DepEd SF9 Learner Core Values successfully recorded.';
    setTimeout(() => { feedbackMessage.value = ''; }, 4000);
  } catch (err) {
    errorMessage.value = 'Failed to save values: ' + err.message;
  }
};

const markAllPresent = () => {
  attendanceStudents.value.forEach(s => {
    s.attendance_status = 'Present';
  });
  feedbackMessage.value = 'Marked all enrolled learners as Present.';
  setTimeout(() => { feedbackMessage.value = ''; }, 3500);
};

const saveAttendanceLog = async () => {
  if (!selectedClassKey.value) {
    errorMessage.value = 'Please select a class section first.';
    return;
  }
  const [secId] = selectedClassKey.value.split('-').map(Number);
  try {
    const res = await api.saveTeacherAttendance({
      section_id: secId,
      date: attendanceDate.value,
      attendance: attendanceStudents.value
    });
    feedbackMessage.value = res.message || 'Attendance logged successfully.';
    setTimeout(() => { feedbackMessage.value = ''; }, 4000);
  } catch (err) {
    errorMessage.value = 'Failed to save attendance: ' + err.message;
  }
};

onMounted(() => {
  loadTeacherDashboard();
});
</script>
