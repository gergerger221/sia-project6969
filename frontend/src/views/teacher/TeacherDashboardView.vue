<template>
  <div class="min-h-screen bg-slate-50 text-slate-900 pb-16 font-sans">
    
    <!-- Top Header Banner -->
    <div class="bg-gradient-to-r from-[#0c2340] via-[#16355d] to-[#0c2340] text-white border-b border-blue-900/40 px-4 sm:px-8 py-6 shadow-md">
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <div class="flex items-center space-x-2.5">
            <div class="w-10 h-10 rounded-2xl bg-amber-500/20 border border-amber-400/40 flex items-center justify-center text-amber-300">
              <GraduationCap class="w-5 h-5" />
            </div>
            <div>
              <div class="text-[11px] font-extrabold uppercase tracking-widest text-amber-400 font-mono">
                Faculty Instruction & Academic Services
              </div>
              <h1 class="text-xl sm:text-2xl font-serif font-black text-white leading-tight">
                {{ teacherProfile?.first_name ? `${teacherProfile.first_name} ${teacherProfile.last_name}` : 'Teacher Portal' }}
              </h1>
            </div>
          </div>
          <p class="text-xs text-blue-200/80 mt-1 max-w-2xl">
            DepEd Electronic Class Record (E-Class Record), Weekly Bell Timetable, Class Masterlists, and Advisory SF9 Core Values Assessment.
          </p>
        </div>

        <div class="flex items-center space-x-3 shrink-0">
          <div class="px-3 py-1.5 rounded-xl bg-blue-950/80 border border-blue-500/30 text-xs font-mono flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-blue-200">S.Y. {{ activeSchoolYear?.name || '2026-2027' }}</span>
          </div>
          <button 
            @click="loadTeacherDashboard()" 
            class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white transition text-xs flex items-center space-x-1.5 cursor-pointer shadow-xs"
            title="Refresh dashboard data"
          >
            <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': isLoading }" />
          </button>
        </div>
      </div>

      <!-- Navigation Tabs Pill Bar -->
      <div class="max-w-7xl mx-auto mt-6 pt-4 border-t border-blue-800/40 flex items-center space-x-2 overflow-x-auto custom-scrollbar">
        <button 
          v-for="t in tabs" 
          :key="t.id"
          @click="selectTab(t.id)"
          type="button"
          :class="[
            'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2 shrink-0 cursor-pointer select-none',
            activeTab === t.id 
              ? 'bg-amber-400 text-slate-950 shadow-md font-extrabold' 
              : 'text-blue-200 hover:text-white hover:bg-white/10'
          ]"
        >
          <component :is="t.icon" class="w-3.5 h-3.5" />
          <span>{{ t.label }}</span>
          <span 
            v-if="t.badge !== undefined" 
            class="px-1.5 py-0.2 rounded-full text-[10px] font-mono"
            :class="activeTab === t.id ? 'bg-slate-900 text-amber-300' : 'bg-blue-900 text-blue-200'"
          >
            {{ t.badge }}
          </span>
        </button>
      </div>
    </div>

    <!-- Main Content Body -->
    <div class="max-w-7xl mx-auto px-4 sm:px-8 mt-6">
      
      <!-- Toast Message -->
      <transition enter-active-class="transition duration-300 ease-out" enter-from-class="transform -translate-y-2 opacity-0" enter-to-class="transform translate-y-0 opacity-100" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="feedbackMessage" class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs flex items-center justify-between shadow-sm">
          <div class="flex items-center space-x-2">
            <CheckCircle2 class="w-4 h-4 text-emerald-600 shrink-0" />
            <span class="font-semibold">{{ feedbackMessage }}</span>
          </div>
          <button @click="feedbackMessage = ''" class="text-emerald-700 hover:text-emerald-900 cursor-pointer">
            <X class="w-4 h-4" />
          </button>
        </div>
      </transition>

      <!-- ======================================================== -->
      <!-- TAB 1: WEEKLY SCHEDULE & ASSIGNED TEACHING LOADS         -->
      <!-- ======================================================== -->
      <div v-if="activeTab === 'schedule'" class="space-y-6">
        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-900 shrink-0">
              <BookOpen class="w-6 h-6" />
            </div>
            <div>
              <div class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Teaching Classes</div>
              <div class="text-2xl font-black text-slate-900">{{ dashboardStats.total_classes || 0 }}</div>
              <div class="text-[11px] text-slate-500">Unique class blocks</div>
            </div>
          </div>

          <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-800 shrink-0">
              <Clock class="w-6 h-6" />
            </div>
            <div>
              <div class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Weekly Periods</div>
              <div class="text-2xl font-black text-slate-900">{{ dashboardStats.total_schedule_periods || 0 }}</div>
              <div class="text-[11px] text-slate-500">Scheduled time slots</div>
            </div>
          </div>

          <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-800 shrink-0">
              <Users class="w-6 h-6" />
            </div>
            <div>
              <div class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Enrolled Students</div>
              <div class="text-2xl font-black text-slate-900">{{ dashboardStats.total_students || 0 }}</div>
              <div class="text-[11px] text-slate-500">Learners handled</div>
            </div>
          </div>

          <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-800 shrink-0">
              <Award class="w-6 h-6" />
            </div>
            <div>
              <div class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Advisory Class</div>
              <div class="text-2xl font-black text-slate-900">{{ dashboardStats.total_advisory_sections || 0 }}</div>
              <div class="text-[11px] text-slate-500">Homeroom sections</div>
            </div>
          </div>
        </div>

        <!-- Weekly Bell Schedule Timetable -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
              <h2 class="text-base font-bold text-slate-900">Weekly Master Timetable (Monday – Friday)</h2>
              <p class="text-xs text-slate-500">Visual timetable matrix for your instructional periods across all assigned sections.</p>
            </div>
            <div class="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-xl self-start">
              {{ weeklySchedules.length }} Assigned Periods
            </div>
          </div>

          <!-- Schedule Days Columns -->
          <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div v-for="day in ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']" :key="day" class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
              <div class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-200 pb-1.5 flex items-center justify-between">
                <span>{{ day }}</span>
                <span class="text-[10px] font-mono text-slate-400">{{ getDaySchedules(day).length }} classes</span>
              </div>

              <div v-if="getDaySchedules(day).length === 0" class="py-6 text-center text-slate-400 text-[11px]">
                No classes scheduled
              </div>

              <div 
                v-for="s in getDaySchedules(day)" 
                :key="s.id"
                class="p-2.5 rounded-xl bg-white border border-slate-200 shadow-2xs hover:border-blue-500 transition space-y-1.5 group cursor-pointer"
                @click="openClassRecord(s.section_id, s.subject_id)"
              >
                <div class="flex items-center justify-between text-[10px] font-mono text-slate-500">
                  <span class="font-bold text-blue-900">{{ formatTime(s.time_start) }} - {{ formatTime(s.time_end) }}</span>
                  <span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-600">{{ s.room || 'Room' }}</span>
                </div>
                <div class="font-bold text-xs text-slate-900 group-hover:text-blue-900 transition leading-snug">
                  {{ s.subject_name }}
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-600">
                  <span class="font-semibold text-slate-700">{{ s.section_name }}</span>
                  <span class="text-[10px] px-1.5 py-0.5 rounded-md font-bold uppercase bg-blue-50 text-blue-800 font-mono">
                    {{ s.grade_level_code }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Assigned Teaching Loads Card Grid -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
              <h2 class="text-base font-bold text-slate-900">Teaching Loads Directory</h2>
              <p class="text-xs text-slate-500">Click on any class block to open its DepEd Electronic Class Record (E-Class Record).</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div 
              v-for="c in teachingClasses" 
              :key="`${c.section_id}-${c.subject_id}`"
              class="p-5 rounded-2xl border border-slate-200 bg-white hover:border-blue-900 hover:shadow-md transition space-y-3 flex flex-col justify-between"
            >
              <div class="space-y-2">
                <div class="flex items-center justify-between">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-blue-50 text-blue-900 border border-blue-200">
                    {{ c.grade_level_code }} • {{ c.section_name }}
                  </span>
                  <span class="text-[11px] font-mono text-slate-500">{{ c.units }} Units</span>
                </div>
                <h3 class="font-bold text-sm text-slate-900 leading-snug">{{ c.subject_name }}</h3>
                <div class="text-xs text-slate-500 flex items-center space-x-2">
                  <span>{{ c.subject_code }}</span>
                  <span>•</span>
                  <span>{{ c.section_room || 'Room' }}</span>
                </div>
              </div>

              <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <div class="text-xs text-slate-600">
                  <span class="font-bold text-slate-900">{{ c.enrolled_count || 0 }}</span> Enrolled Learners
                </div>
                <button 
                  @click="openClassRecord(c.section_id, c.subject_id)"
                  type="button" 
                  class="px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-900 hover:bg-blue-800 text-white shadow-2xs transition flex items-center space-x-1 cursor-pointer"
                >
                  <FileSpreadsheet class="w-3.5 h-3.5" />
                  <span>Open Grades</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- TAB 2: ELECTRONIC CLASS RECORD (E-CLASS RECORD & GRADES) -->
      <!-- ======================================================== -->
      <div v-if="activeTab === 'grading'" class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-5">
        
        <!-- Controls & Class Selector -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 pb-4">
          <div>
            <div class="flex items-center space-x-2">
              <h2 class="text-base font-bold text-slate-900">DepEd Electronic Class Record (E-Class Record)</h2>
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-900 font-mono">
                Order 8, s. 2015
              </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Encode quarterly scores (0–100). Final rating and passing remarks calculate automatically.</p>
          </div>

          <!-- Class Dropdown Selector -->
          <div class="flex flex-wrap items-center gap-2">
            <select 
              v-model="selectedClassKey" 
              @change="handleClassChange()"
              class="px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-900 cursor-pointer shadow-2xs"
            >
              <option value="">-- Select Class Section & Subject --</option>
              <option 
                v-for="c in teachingClasses" 
                :key="`${c.section_id}-${c.subject_id}`" 
                :value="`${c.section_id}-${c.subject_id}`"
              >
                {{ c.grade_level_code }} - {{ c.section_name }} : {{ c.subject_name }} ({{ c.enrolled_count }} students)
              </option>
            </select>

            <button 
              @click="saveGradesBatch()" 
              :disabled="isSavingGrades || !selectedClassKey" 
              type="button" 
              class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white shadow-xs transition flex items-center space-x-1.5 cursor-pointer"
            >
              <Check class="w-4 h-4" />
              <span>{{ isSavingGrades ? 'Saving Grades...' : 'Save Grades' }}</span>
            </button>
          </div>
        </div>

        <!-- Class Metadata Card -->
        <div v-if="currentClassData.section" class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200 flex flex-wrap items-center justify-between gap-3 text-xs">
          <div class="space-y-0.5">
            <div class="font-extrabold text-blue-950 text-sm">
              {{ currentClassData.section.name }} • {{ currentClassData.subject.name }}
            </div>
            <div class="text-slate-600 text-[11px]">
              Level: <strong class="text-slate-900">{{ currentClassData.section.grade_level_name }}</strong> | 
              Classification: <strong class="text-slate-900">{{ currentClassData.subject.classification }}</strong> | 
              Semester: <strong class="text-slate-900">{{ currentClassData.subject.semester || 'Full Year' }}</strong>
            </div>
          </div>

          <div class="flex items-center space-x-2">
            <!-- Search student in class -->
            <div class="relative w-48 sm:w-60">
              <Search class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" />
              <input 
                v-model="searchGradeQuery" 
                type="text" 
                placeholder="Search student or LRN..." 
                class="w-full pl-8 pr-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs focus:outline-none focus:border-blue-900"
              />
            </div>
          </div>
        </div>

        <!-- Grades Table -->
        <div v-if="selectedClassKey" class="overflow-x-auto">
          <table class="w-full text-xs text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                <th class="p-3">#</th>
                <th class="p-3">Learner Name</th>
                <th class="p-3 font-mono">LRN / Student No</th>
                <th class="p-3 text-center">Gender</th>
                <th class="p-3 text-center w-20">Q1 (1st)</th>
                <th class="p-3 text-center w-20">Q2 (2nd)</th>
                <th class="p-3 text-center w-20">Q3 (3rd)</th>
                <th class="p-3 text-center w-20">Q4 (4th)</th>
                <th class="p-3 text-center w-24">Final Grade</th>
                <th class="p-3 text-center">Remarks</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="(s, idx) in filteredClassStudents" :key="s.student_id" class="hover:bg-slate-50/80 transition">
                <td class="p-3 font-mono text-slate-400 text-[11px]">{{ idx + 1 }}</td>
                <td class="p-3 font-bold text-slate-900 whitespace-nowrap">{{ s.full_name }}</td>
                <td class="p-3 font-mono text-slate-500 text-[11px] whitespace-nowrap">
                  <div>{{ s.lrn || 'No LRN' }}</div>
                  <div class="text-[10px] text-slate-400">{{ s.student_no }}</div>
                </td>
                <td class="p-3 text-center">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="s.gender === 'Female' ? 'bg-pink-100 text-pink-800' : 'bg-blue-100 text-blue-800'">
                    {{ s.gender }}
                  </span>
                </td>

                <!-- Q1 Input -->
                <td class="p-2 text-center">
                  <input 
                    v-model.number="s.q1" 
                    @input="recalculateStudentGrade(s)"
                    type="number" 
                    min="0" 
                    max="100" 
                    step="0.01"
                    placeholder="--"
                    class="w-16 px-2 py-1 rounded-lg border border-slate-300 text-center font-mono font-bold text-xs focus:outline-none focus:border-blue-900 focus:bg-blue-50"
                  />
                </td>

                <!-- Q2 Input -->
                <td class="p-2 text-center">
                  <input 
                    v-model.number="s.q2" 
                    @input="recalculateStudentGrade(s)"
                    type="number" 
                    min="0" 
                    max="100" 
                    step="0.01"
                    placeholder="--"
                    class="w-16 px-2 py-1 rounded-lg border border-slate-300 text-center font-mono font-bold text-xs focus:outline-none focus:border-blue-900 focus:bg-blue-50"
                  />
                </td>

                <!-- Q3 Input -->
                <td class="p-2 text-center">
                  <input 
                    v-model.number="s.q3" 
                    @input="recalculateStudentGrade(s)"
                    type="number" 
                    min="0" 
                    max="100" 
                    step="0.01"
                    placeholder="--"
                    class="w-16 px-2 py-1 rounded-lg border border-slate-300 text-center font-mono font-bold text-xs focus:outline-none focus:border-blue-900 focus:bg-blue-50"
                  />
                </td>

                <!-- Q4 Input -->
                <td class="p-2 text-center">
                  <input 
                    v-model.number="s.q4" 
                    @input="recalculateStudentGrade(s)"
                    type="number" 
                    min="0" 
                    max="100" 
                    step="0.01"
                    placeholder="--"
                    class="w-16 px-2 py-1 rounded-lg border border-slate-300 text-center font-mono font-bold text-xs focus:outline-none focus:border-blue-900 focus:bg-blue-50"
                  />
                </td>

                <!-- Final Grade (Computed) -->
                <td class="p-3 text-center font-mono font-black text-xs">
                  <span v-if="s.final_grade !== null" :class="s.final_grade >= 75 ? 'text-emerald-700' : 'text-rose-700'">
                    {{ s.final_grade.toFixed(2) }}
                  </span>
                  <span v-else class="text-slate-300">--</span>
                </td>

                <!-- Remarks -->
                <td class="p-3 text-center">
                  <span 
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider"
                    :class="{
                      'bg-emerald-100 text-emerald-800 border border-emerald-200': s.remarks === 'Passed',
                      'bg-rose-100 text-rose-800 border border-rose-200': s.remarks === 'Failed',
                      'bg-slate-100 text-slate-600': s.remarks === 'Ongoing' || !s.remarks
                    }"
                  >
                    {{ s.remarks || 'Ongoing' }}
                  </span>
                </td>
              </tr>

              <tr v-if="filteredClassStudents.length === 0">
                <td colspan="10" class="p-8 text-center text-slate-400 text-xs">
                  <Users class="w-8 h-8 text-slate-300 mx-auto mb-2" />
                  <div>No enrolled learners found in this class section.</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="p-12 text-center text-slate-400 text-xs border border-dashed border-slate-200 rounded-3xl">
          <BookOpen class="w-10 h-10 text-slate-300 mx-auto mb-3" />
          <div class="font-bold text-slate-700 text-sm">Select a Class Section & Subject to begin grading</div>
          <p class="text-slate-400 mt-1">Choose an assigned teaching load from the dropdown above to load the E-Class Record.</p>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- TAB 3: CLASS MASTERLISTS & STUDENT ROSTER                -->
      <!-- ======================================================== -->
      <div v-if="activeTab === 'roster'" class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
          <div>
            <h2 class="text-base font-bold text-slate-900">Class Masterlists & Student Directory</h2>
            <p class="text-xs text-slate-500">View official learner profiles, LRNs, and contact information per section.</p>
          </div>
          
          <select 
            v-model="selectedClassKey" 
            @change="handleClassChange()"
            class="px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-900 cursor-pointer shadow-2xs"
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
              <tr class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                <th class="p-3">#</th>
                <th class="p-3">Learner Full Name</th>
                <th class="p-3 font-mono">LRN</th>
                <th class="p-3 font-mono">Student No</th>
                <th class="p-3">Gender</th>
                <th class="p-3">Contact Number</th>
                <th class="p-3">Email Address</th>
                <th class="p-3">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="(s, idx) in currentClassData.students || []" :key="s.student_id" class="hover:bg-slate-50 transition">
                <td class="p-3 font-mono text-slate-400 text-[11px]">{{ idx + 1 }}</td>
                <td class="p-3 font-bold text-slate-900">{{ s.full_name }}</td>
                <td class="p-3 font-mono text-slate-600">{{ s.lrn || 'N/A' }}</td>
                <td class="p-3 font-mono text-blue-900 font-bold">{{ s.student_no }}</td>
                <td class="p-3">{{ s.gender }}</td>
                <td class="p-3 font-mono text-slate-600">{{ s.contact_number || 'N/A' }}</td>
                <td class="p-3 text-slate-600">{{ s.email || 'N/A' }}</td>
                <td class="p-3">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
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
      <div v-if="activeTab === 'advisory'" class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
          <div>
            <div class="flex items-center space-x-2">
              <h2 class="text-base font-bold text-slate-900">Homeroom Advisory Section & SF9 Core Values</h2>
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-100 text-purple-900 font-mono">
                Class Adviser
              </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">DepEd Core Values Assessment (AO = Always Observed, SO = Sometimes Observed, RO = Rarely Observed, NO = Not Observed).</p>
          </div>

          <button 
            @click="saveAdvisoryValues()" 
            :disabled="!advisoryData.has_advisory" 
            type="button" 
            class="px-4 py-2 rounded-xl text-xs font-bold bg-purple-900 hover:bg-purple-800 disabled:opacity-50 text-white shadow-xs transition flex items-center space-x-1.5 cursor-pointer"
          >
            <Check class="w-4 h-4" />
            <span>Save Values Ratings</span>
          </button>
        </div>

        <div v-if="advisoryData.has_advisory" class="space-y-4">
          <!-- Advisory Header Card -->
          <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-200 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div>
              <div class="font-extrabold text-purple-950 text-sm">
                {{ advisoryData.section?.name }} ({{ advisoryData.section?.grade_level_name }})
              </div>
              <div class="text-slate-600 text-[11px] mt-0.5">
                Room: <strong>{{ advisoryData.section?.room }}</strong> | Total Learners: <strong>{{ advisoryData.total_learners }}</strong>
              </div>
            </div>
          </div>

          <!-- SF9 Core Values Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                  <th class="p-3">#</th>
                  <th class="p-3">Learner Name</th>
                  <th class="p-3 text-center">Maka-Diyos</th>
                  <th class="p-3 text-center">Makatao</th>
                  <th class="p-3 text-center">Makakalikasan</th>
                  <th class="p-3 text-center">Makabansa</th>
                  <th class="p-3 text-center font-mono">Gen. Average</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="(l, idx) in advisoryData.learners" :key="l.student_id" class="hover:bg-slate-50 transition">
                  <td class="p-3 font-mono text-slate-400 text-[11px]">{{ idx + 1 }}</td>
                  <td class="p-3 font-bold text-slate-900 whitespace-nowrap">{{ l.full_name }}</td>

                  <!-- Maka-Diyos -->
                  <td class="p-2 text-center">
                    <select v-model="l.values_ratings.maka_diyos_q1" class="px-2 py-1 rounded-lg border border-slate-300 text-xs font-bold focus:outline-none focus:border-purple-900 cursor-pointer">
                      <option value="AO">AO</option>
                      <option value="SO">SO</option>
                      <option value="RO">RO</option>
                      <option value="NO">NO</option>
                    </select>
                  </td>

                  <!-- Makatao -->
                  <td class="p-2 text-center">
                    <select v-model="l.values_ratings.maka_tao_q1" class="px-2 py-1 rounded-lg border border-slate-300 text-xs font-bold focus:outline-none focus:border-purple-900 cursor-pointer">
                      <option value="AO">AO</option>
                      <option value="SO">SO</option>
                      <option value="RO">RO</option>
                      <option value="NO">NO</option>
                    </select>
                  </td>

                  <!-- Makakalikasan -->
                  <td class="p-2 text-center">
                    <select v-model="l.values_ratings.makakalikasan_q1" class="px-2 py-1 rounded-lg border border-slate-300 text-xs font-bold focus:outline-none focus:border-purple-900 cursor-pointer">
                      <option value="AO">AO</option>
                      <option value="SO">SO</option>
                      <option value="RO">RO</option>
                      <option value="NO">NO</option>
                    </select>
                  </td>

                  <!-- Makabansa -->
                  <td class="p-2 text-center">
                    <select v-model="l.values_ratings.makabansa_q1" class="px-2 py-1 rounded-lg border border-slate-300 text-xs font-bold focus:outline-none focus:border-purple-900 cursor-pointer">
                      <option value="AO">AO</option>
                      <option value="SO">SO</option>
                      <option value="RO">RO</option>
                      <option value="NO">NO</option>
                    </select>
                  </td>

                  <td class="p-3 text-center font-mono font-bold text-slate-800">
                    {{ l.general_average ? l.general_average.toFixed(2) : '--' }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-else class="p-12 text-center text-slate-400 text-xs border border-dashed border-slate-200 rounded-3xl">
          <Award class="w-10 h-10 text-slate-300 mx-auto mb-2" />
          <div class="font-bold text-slate-700">No Advisory Section Assigned</div>
          <p class="text-slate-400 mt-1">This faculty account is currently assigned purely for subject instruction.</p>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- TAB 5: DAILY ATTENDANCE TRACKER (SF2)                    -->
      <!-- ======================================================== -->
      <div v-if="activeTab === 'attendance'" class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
          <div>
            <h2 class="text-base font-bold text-slate-900">Daily Attendance Log (DepEd SF2)</h2>
            <p class="text-xs text-slate-500">Record daily learner attendance status (Present, Absent, Late, Excused).</p>
          </div>

          <div class="flex items-center space-x-2">
            <input 
              v-model="attendanceDate" 
              type="date" 
              class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-900 cursor-pointer"
            />
            <button 
              @click="markAllPresent()" 
              type="button" 
              class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 transition cursor-pointer"
            >
              Mark All Present
            </button>
            <button 
              @click="saveAttendanceLog()" 
              type="button" 
              class="px-4 py-1.5 rounded-xl text-xs font-bold bg-blue-900 hover:bg-blue-800 text-white shadow-xs transition cursor-pointer"
            >
              Save Attendance
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-xs text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                <th class="p-3">#</th>
                <th class="p-3">Learner Name</th>
                <th class="p-3 font-mono">LRN</th>
                <th class="p-3 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="(s, idx) in attendanceStudents" :key="s.student_id" class="hover:bg-slate-50 transition">
                <td class="p-3 font-mono text-slate-400 text-[11px]">{{ idx + 1 }}</td>
                <td class="p-3 font-bold text-slate-900">{{ s.full_name }}</td>
                <td class="p-3 font-mono text-slate-600">{{ s.lrn || 'N/A' }}</td>
                <td class="p-2 text-center">
                  <div class="inline-flex rounded-xl p-1 bg-slate-100 space-x-1">
                    <button 
                      v-for="st in ['Present', 'Late', 'Absent', 'Excused']" 
                      :key="st"
                      @click="s.attendance_status = st"
                      type="button"
                      :class="[
                        'px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase transition cursor-pointer',
                        s.attendance_status === st 
                          ? (st === 'Present' ? 'bg-emerald-600 text-white' : st === 'Late' ? 'bg-amber-500 text-white' : st === 'Absent' ? 'bg-rose-600 text-white' : 'bg-blue-600 text-white')
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

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { 
  GraduationCap, Clock, BookOpen, Users, Award, Calendar, 
  FileSpreadsheet, CheckCircle2, Search, X, Check, RefreshCw 
} from 'lucide-vue-next';
import api from '../../services/api';

const route = useRoute();
const router = useRouter();

const activeTab = ref('schedule');
const isLoading = ref(false);
const isSavingGrades = ref(false);
const feedbackMessage = ref('');

const tabs = [
  { id: 'schedule', label: 'Weekly Schedule & Classes', icon: Clock },
  { id: 'grading', label: 'Electronic Class Record', icon: FileSpreadsheet },
  { id: 'roster', label: 'Class Masterlists', icon: Users },
  { id: 'advisory', label: 'Advisory Section (SF9)', icon: Award },
  { id: 'attendance', label: 'Attendance Sheet (SF2)', icon: Calendar }
];

const selectTab = (tabId) => {
  activeTab.value = tabId;
  router.push({ query: { tab: tabId } });
};

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

// Load Dashboard Data
const loadTeacherDashboard = async () => {
  isLoading.value = true;
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

const saveGradesBatch = async () => {
  if (!selectedClassKey.value) return;
  const [secId, subId] = selectedClassKey.value.split('-').map(Number);

  isSavingGrades.value = true;
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
    alert('Failed to save grades: ' + (err.message || 'Error occurred.'));
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
    alert('Failed to save values: ' + err.message);
  }
};

const markAllPresent = () => {
  attendanceStudents.value.forEach(s => {
    s.attendance_status = 'Present';
  });
};

const saveAttendanceLog = async () => {
  if (!selectedClassKey.value) {
    alert('Please select a class section first.');
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
    alert('Failed to save attendance: ' + err.message);
  }
};

onMounted(() => {
  loadTeacherDashboard();
});
</script>
