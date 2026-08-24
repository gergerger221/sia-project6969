<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-950 text-purple-400 border border-purple-500/30 text-xs font-bold uppercase tracking-wider mb-2">
          <span>Academic Affairs & Curriculum Coordination</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Curriculum, Subjects & Section Management</h1>
        <p class="text-xs text-slate-400 mt-1">Configure DepEd K to 12 & MATATAG subjects, prerequisites, sections, and faculty adviser loading.</p>
      </div>

      <div class="flex items-center space-x-2 flex-wrap gap-y-2">
        <button 
          @click="activeTab = 'curriculum'"
          :class="activeTab === 'curriculum' ? 'bg-purple-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
          class="px-3.5 py-2 rounded-xl text-xs transition"
        >
          Curriculum ({{ curriculumData.subjects?.length || 0 }})
        </button>
        <button 
          @click="activeTab = 'strands'"
          :class="activeTab === 'strands' ? 'bg-purple-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
          class="px-3.5 py-2 rounded-xl text-xs transition"
        >
          Strands ({{ curriculumData.strands?.length || 0 }})
        </button>
        <button 
          @click="activeTab = 'sections'"
          :class="activeTab === 'sections' ? 'bg-purple-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
          class="px-3.5 py-2 rounded-xl text-xs transition"
        >
          Class Sections ({{ sectionsData.sections?.length || 0 }})
        </button>
        <button 
          @click="switchToSchedulesTab()"
          :class="activeTab === 'schedules' ? 'bg-purple-600 text-white font-bold shadow-lg shadow-purple-900/40' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
          class="px-3.5 py-2 rounded-xl text-xs transition flex items-center space-x-1.5"
        >
          <Clock class="w-3.5 h-3.5" />
          <span>Class Schedules & Timetables</span>
        </button>
        <button 
          @click="switchToEventsTab()"
          :class="activeTab === 'events' ? 'bg-purple-600 text-white font-bold shadow-lg shadow-purple-900/40' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
          class="px-3.5 py-2 rounded-xl text-xs transition flex items-center space-x-1.5"
        >
          <Calendar class="w-3.5 h-3.5" />
          <span>School Events Calendar</span>
        </button>
      </div>
    </div>

    <!-- Alert -->
    <div v-if="successMessage" class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500 text-emerald-300 text-xs mb-6 flex items-center justify-between">
      <span>{{ successMessage }}</span>
      <button @click="successMessage = ''" class="font-bold">✕</button>
    </div>

    <!-- TAB 1: CURRICULUM MANAGEMENT & SUBJECTS CATALOG -->
    <div v-if="activeTab === 'curriculum'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
      <!-- CURRICULUM DECLARATION & LOCK STATUS BANNER -->
      <div 
        v-if="curriculumData.curriculum_locked"
        class="mb-6 p-5 rounded-2xl bg-emerald-950 border border-emerald-500/40 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-lg"
      >
        <div class="flex items-start space-x-3.5">
          <div class="p-2.5 rounded-xl bg-emerald-900/80 text-emerald-400 border border-emerald-500/40 shrink-0 mt-0.5">
            <Lock class="w-5 h-5" />
          </div>
          <div>
            <div class="flex items-center space-x-2 flex-wrap gap-y-1">
              <h3 class="font-extrabold text-sm text-emerald-200">School Year Curriculum Officially Declared & Locked</h3>
              <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-mono text-[10px] font-bold border border-emerald-500/40">
                🔒 DepEd Integrity Freeze Active
              </span>
            </div>
            <p class="text-[11px] text-slate-300 mt-1 leading-relaxed max-w-3xl">
              All <strong>{{ curriculumData.subjects?.length || 0 }} subjects</strong> and <strong>{{ curriculumData.strands?.length || 0 }} strands</strong> are officially locked from editing or deletion to protect active student permanent records (SF10 / Form 137), quarterly report cards (SF9), and section timetables. Any mid-year DepEd curriculum adjustments will apply to the next school year.
            </p>
          </div>
        </div>

        <button 
          @click="toggleCurriculumDeclaration()" 
          class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-600 transition shrink-0 flex items-center space-x-1.5"
          title="Unlock curriculum to enable drafting modifications"
        >
          <Unlock class="w-3.5 h-3.5 text-amber-400" />
          <span>Unlock Curriculum (Setup Mode)</span>
        </button>
      </div>

      <div 
        v-else
        class="mb-6 p-5 rounded-2xl bg-amber-950 border border-amber-500/40 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-lg"
      >
        <div class="flex items-start space-x-3.5">
          <div class="p-2.5 rounded-xl bg-amber-900/80 text-amber-400 border border-amber-500/40 shrink-0 mt-0.5">
            <AlertCircle class="w-5 h-5" />
          </div>
          <div>
            <div class="flex items-center space-x-2 flex-wrap gap-y-1">
              <h3 class="font-extrabold text-sm text-amber-200">Curriculum in Draft / Setup Mode</h3>
              <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-mono text-[10px] font-bold border border-amber-500/40">
                🟡 Open for Editing
              </span>
            </div>
            <p class="text-[11px] text-slate-300 mt-1 leading-relaxed max-w-3xl">
              Curriculum learning areas, units, and strands are currently open for modifications. Once the academic year starts or official enrollments are generated, click <strong>"Declare & Lock SY Curriculum"</strong> to freeze the curriculum.
            </p>
          </div>
        </div>

        <button 
          @click="toggleCurriculumDeclaration()" 
          class="px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md transition shrink-0 flex items-center space-x-1.5"
          title="Declare and freeze curriculum for active school year"
        >
          <Lock class="w-3.5 h-3.5 text-white" />
          <span>Declare & Lock SY Curriculum</span>
        </button>
      </div>

      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6 border-b border-slate-100 pb-5">
        <div>
          <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 font-bold text-[10px] uppercase tracking-wider mb-1">
            <BookOpen class="w-3 h-3 text-purple-700" />
            <span>Master Academic Blueprint</span>
          </div>
          <h2 class="text-lg font-extrabold text-slate-900">DepEd Learning Areas & Curriculum</h2>
          <p class="text-xs text-slate-500">Configure core, applied, and specialized subjects, term scheduling, units, and prerequisites.</p>
        </div>

        <button 
          @click="openSubjectModal()" 
          class="px-4 py-2 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-500 text-white shadow-md transition flex items-center space-x-1.5"
        >
          <Plus class="w-4 h-4" />
          <span>Add New Subject</span>
        </button>
      </div>

      <!-- Filters & KPI Bar -->
      <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-6 space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <!-- Search -->
          <div>
            <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Search Subject</label>
            <div class="relative">
              <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" />
              <input 
                v-model="currFilter.search" 
                type="text" 
                placeholder="Code or title..." 
                class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs" 
              />
            </div>
          </div>

          <!-- Grade Level Filter -->
          <div>
            <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Grade Level</label>
            <select v-model="currFilter.grade_level_id" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs">
              <option value="">All Grade Levels</option>
              <option v-for="g in curriculumData.grade_levels" :key="g.id" :value="g.id">{{ g.name }} ({{ g.category }})</option>
            </select>
          </div>

          <!-- Strand Filter -->
          <div>
            <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Strand / Track</label>
            <select v-model="currFilter.strand_id" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs">
              <option value="">All Strands / General</option>
              <option value="core">Core Subjects Only</option>
              <option v-for="s in curriculumData.strands" :key="s.id" :value="s.id">{{ s.code }} - {{ s.name }}</option>
            </select>
          </div>

          <!-- Category Filter -->
          <div>
            <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Classification</label>
            <select v-model="currFilter.category" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs">
              <option value="">All Classifications</option>
              <option value="JHS Core">JHS Core</option>
              <option value="SHS Core">SHS Core</option>
              <option value="SHS Applied">SHS Applied</option>
              <option value="SHS Specialized">SHS Specialized</option>
            </select>
          </div>

          <!-- Semester Filter -->
          <div>
            <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Academic Term / Sem</label>
            <select v-model="currFilter.semester" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs">
              <option value="">All Terms</option>
              <option value="1st Semester">1st Semester</option>
              <option value="2nd Semester">2nd Semester</option>
              <option value="Full Year">Full Year (JHS)</option>
            </select>
          </div>
        </div>

        <!-- Summary Statistics Pills -->
        <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-slate-200 text-xs">
          <div class="px-3 py-1 rounded-lg bg-white border border-slate-200 font-semibold text-slate-700">
            Filtered Subjects: <strong class="text-purple-700">{{ filteredSubjects.length }}</strong>
          </div>
          <div class="px-3 py-1 rounded-lg bg-white border border-slate-200 font-semibold text-slate-700">
            Total Academic Units: <strong class="text-slate-900">{{ curriculumStats.totalUnits.toFixed(1) }}</strong>
          </div>
          <div class="px-3 py-1 rounded-lg bg-white border border-slate-200 font-semibold text-slate-700">
            Total Weekly Hours: <strong class="text-slate-900">{{ curriculumStats.totalHours.toFixed(1) }} hrs</strong>
          </div>
        </div>
      </div>

      <!-- Subjects Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
              <th class="p-3.5">Subject Code</th>
              <th class="p-3.5">Subject Title</th>
              <th class="p-3.5">Grade Level / Strand</th>
              <th class="p-3.5">Classification</th>
              <th class="p-3.5">Term</th>
              <th class="p-3.5">Prerequisite</th>
              <th class="p-3.5 text-center">Hours / Units</th>
              <th class="p-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="sub in filteredSubjects" :key="sub.id" class="hover:bg-slate-50 transition">
              <td class="p-3.5 font-bold font-mono text-purple-700">{{ sub.code }}</td>
              <td class="p-3.5 font-bold text-slate-800">
                {{ sub.title }}
                <p v-if="sub.description" class="text-[11px] font-normal text-slate-400 mt-0.5 line-clamp-1">{{ sub.description }}</p>
              </td>
              <td class="p-3.5">
                <span class="font-semibold text-slate-800">{{ sub.grade_level_name }}</span>
                <span v-if="sub.strand_code" class="ml-1 px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 font-bold text-[10px]">
                  {{ sub.strand_code }}
                </span>
              </td>
              <td class="p-3.5">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="getCategoryClass(sub.category)">
                  {{ sub.category }}
                </span>
              </td>
              <td class="p-3.5 text-slate-600">{{ sub.semester }}</td>
              <td class="p-3.5 font-mono text-[11px] text-slate-500">
                <span v-if="sub.prerequisite_code" class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200">
                  {{ sub.prerequisite_code }}
                </span>
                <span v-else class="text-slate-400">None</span>
              </td>
              <td class="p-3.5 text-center font-mono font-bold">{{ sub.lecture_hours }}h / {{ sub.units }}u</td>
              <td class="p-3.5 text-right space-x-1 whitespace-nowrap">
                <template v-if="curriculumData.curriculum_locked">
                  <span 
                    class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-500 font-semibold text-[11px] border border-slate-200 cursor-not-allowed select-none"
                    title="Locked: Classes, grades, and transcripts are actively linked to this subject for SY 2026-2027"
                  >
                    <Lock class="w-3 h-3 text-slate-400" />
                    <span>Locked</span>
                  </span>
                </template>
                <template v-else>
                  <button 
                    @click="openSubjectModal(sub)" 
                    class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-purple-700 hover:bg-purple-50 transition"
                    title="Edit curriculum subject"
                  >
                    Edit
                  </button>
                  <button 
                    @click="confirmDeleteSubject(sub)" 
                    class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-rose-600 hover:bg-rose-50 transition"
                    title="Delete or archive subject"
                  >
                    Delete
                  </button>
                </template>
              </td>
            </tr>
            <tr v-if="filteredSubjects.length === 0">
              <td colspan="8" class="p-8 text-center text-slate-400 text-xs">
                No curriculum subjects matched your search criteria.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 2: ACADEMIC STRANDS & TRACKS MANAGEMENT -->
    <div v-if="activeTab === 'strands'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
      <!-- CURRICULUM DECLARATION & LOCK STATUS BANNER (FOR STRANDS) -->
      <div 
        v-if="curriculumData.curriculum_locked"
        class="p-5 rounded-2xl bg-emerald-950 border border-emerald-500/40 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-lg"
      >
        <div class="flex items-start space-x-3.5">
          <div class="p-2.5 rounded-xl bg-emerald-900/80 text-emerald-400 border border-emerald-500/40 shrink-0 mt-0.5">
            <Lock class="w-5 h-5" />
          </div>
          <div>
            <div class="flex items-center space-x-2 flex-wrap gap-y-1">
              <h3 class="font-extrabold text-sm text-emerald-200">Academic Strands Locked & Active for School Year 2026-2027</h3>
              <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-mono text-[10px] font-bold border border-emerald-500/40">
                🔒 View Only Mode
              </span>
            </div>
            <p class="text-[11px] text-slate-300 mt-1 leading-relaxed max-w-3xl">
              All <strong>{{ curriculumData.strands?.length || 0 }} Senior High strands</strong> and their linked curriculum subjects are locked to protect student transcripts, sections, and ongoing class schedules. Click any strand below to view its complete 4-semester learning area roadmap.
            </p>
          </div>
        </div>

        <button 
          @click="toggleCurriculumDeclaration()" 
          class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-600 transition shrink-0 flex items-center space-x-1.5"
          title="Unlock curriculum to enable drafting modifications"
        >
          <Unlock class="w-3.5 h-3.5 text-amber-400" />
          <span>Unlock Setup Mode</span>
        </button>
      </div>

      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
          <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 font-bold text-[10px] uppercase tracking-wider mb-1">
            <Layers class="w-3 h-3 text-purple-700" />
            <span>Senior High School Program Offerings</span>
          </div>
          <h2 class="text-lg font-extrabold text-slate-900">Academic Tracks & Strands Catalog</h2>
          <p class="text-xs text-slate-500">Manage specialized Senior High strands, admission intake availability, and historical program archival.</p>
        </div>

        <template v-if="curriculumData.curriculum_locked">
          <div 
            class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200 flex items-center space-x-1.5 cursor-not-allowed select-none shrink-0"
            title="Curriculum is officially declared and locked. Adding new strands is disabled during the active school year."
          >
            <Lock class="w-3.5 h-3.5 text-slate-400" />
            <span>Strands Locked (View Only)</span>
          </div>
        </template>
        <template v-else>
          <button 
            @click="openStrandModal()" 
            class="px-4 py-2 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-500 text-white shadow-md transition flex items-center space-x-1.5 shrink-0"
          >
            <Plus class="w-4 h-4" />
            <span>Add New Strand</span>
          </button>
        </template>
      </div>

      <!-- Strands Filter Bar & Quick Status Pills -->
      <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3 text-xs">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200/70 pb-3">
          <div class="flex items-center space-x-1.5 flex-wrap gap-y-1.5">
            <button 
              type="button" 
              @click="strandFilter.status = 'Active'"
              :class="strandFilter.status === 'Active' ? 'bg-purple-600 text-white font-bold shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
              class="px-3 py-1.5 rounded-xl transition flex items-center space-x-1.5"
            >
              <span>Active Offerings</span>
              <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="strandFilter.status === 'Active' ? 'bg-purple-800 text-purple-100' : 'bg-slate-100 text-slate-600'">
                {{ strandStats.activeCount }}
              </span>
            </button>

            <button 
              type="button" 
              @click="strandFilter.status = 'Archived'"
              :class="strandFilter.status === 'Archived' ? 'bg-slate-800 text-white font-bold shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
              class="px-3 py-1.5 rounded-xl transition flex items-center space-x-1.5"
            >
              <span>Archived Strands</span>
              <span class="px-1.5 py-0.2 rounded-full text-[10px]" :class="strandFilter.status === 'Archived' ? 'bg-slate-950 text-slate-300' : 'bg-slate-100 text-slate-600'">
                {{ strandStats.archivedCount }}
              </span>
            </button>

            <button 
              type="button" 
              @click="strandFilter.status = ''"
              :class="strandFilter.status === '' ? 'bg-purple-100 text-purple-900 font-bold border border-purple-300' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
              class="px-3 py-1.5 rounded-xl transition flex items-center space-x-1.5"
            >
              <span>All Programs</span>
              <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-600">
                {{ strandStats.totalCount }}
              </span>
            </button>
          </div>

          <div class="text-[11px] text-slate-500 font-medium">
            Showing <strong class="text-slate-800">{{ filteredStrands.length }}</strong> of {{ strandStats.totalCount }} programs
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Filter by Academic Track</label>
            <select v-model="strandFilter.track_id" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 bg-white">
              <option value="">All Academic Tracks</option>
              <option v-for="t in curriculumData.tracks" :key="t.id" :value="t.id">{{ t.code }} - {{ t.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Search Strand Code / Title</label>
            <div class="relative">
              <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" />
              <input 
                v-model="strandFilter.search" 
                type="text" 
                placeholder="e.g. STEM, TVL, Humanities..." 
                class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-300 bg-white" 
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Strands Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div 
          v-for="st in filteredStrands" 
          :key="st.id"
          class="p-5 rounded-2xl border transition relative flex flex-col justify-between hover:shadow-md"
          :class="getStrandCardBorderClass(st.status)"
        >
          <!-- Clickable Card Body -->
          <div @click="openStrandDetailsModal(st)" class="cursor-pointer group">
            <!-- Card Header: Badges & Edit -->
            <div class="flex items-center justify-between gap-2 mb-3">
              <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider font-mono" :class="getTrackBadgeClass(st.track_code)">
                {{ st.track_name || 'Academic Track' }}
              </span>

              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase" :class="getStrandStatusBadgeClass(st.status)">
                {{ st.status || 'Active' }}
              </span>
            </div>

            <!-- Strand Title -->
            <div class="flex items-baseline space-x-2">
              <span class="text-sm font-mono font-extrabold text-purple-700 group-hover:text-purple-900">{{ st.code }}</span>
              <h3 class="text-sm font-bold text-slate-900 group-hover:text-purple-900 transition">{{ st.name }}</h3>
            </div>
            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed line-clamp-2">{{ st.description || 'No description provided.' }}</p>

            <!-- Real-time Analytics Metrics -->
            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-3 gap-2 text-center">
              <div class="p-2 rounded-xl bg-slate-50 border border-slate-100 group-hover:bg-purple-50/50 transition">
                <div class="text-xs font-bold font-mono text-purple-900">{{ st.enrolled_students_count || 0 }}</div>
                <div class="text-[9px] font-semibold text-slate-400 uppercase">Enrolled</div>
              </div>
              <div class="p-2 rounded-xl bg-slate-50 border border-slate-100 group-hover:bg-purple-50/50 transition">
                <div class="text-xs font-bold font-mono text-blue-900">{{ st.active_sections_count || 0 }}</div>
                <div class="text-[9px] font-semibold text-slate-400 uppercase">Sections</div>
              </div>
              <div class="p-2 rounded-xl bg-slate-50 border border-slate-100 group-hover:bg-purple-50/50 transition">
                <div class="text-xs font-bold font-mono text-emerald-900">{{ st.curriculum_subjects_count || 0 }}</div>
                <div class="text-[9px] font-semibold text-slate-400 uppercase">Courses</div>
              </div>
            </div>

            <div class="mt-3 flex items-center justify-between text-[11px] font-bold text-purple-700 group-hover:text-purple-900">
              <span class="inline-flex items-center space-x-1">
                <BookOpen class="w-3.5 h-3.5" />
                <span>View Full Curriculum & Semesters</span>
              </span>
              <span>→</span>
            </div>
          </div>

          <!-- Card Actions Footer: View-Only when Locked / Full Management when Unlocked -->
          <div class="mt-4 pt-3 border-t border-slate-100 text-xs">
            <!-- 1. VIEW ONLY MODE (WHEN CURRICULUM IS LOCKED) -->
            <template v-if="curriculumData.curriculum_locked">
              <button 
                @click="openStrandDetailsModal(st)"
                class="w-full py-2 px-3.5 rounded-xl font-bold bg-purple-50/80 hover:bg-purple-100/80 text-purple-700 hover:text-purple-900 border border-purple-200 transition flex items-center justify-between group/btn shadow-xs"
                title="View complete strand curriculum, learning areas, and section assignments"
              >
                <span class="inline-flex items-center space-x-1.5 font-bold">
                  <BookOpen class="w-3.5 h-3.5 text-purple-600" />
                  <span>View Strand Blueprint & Roadmap</span>
                </span>
                <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md bg-white border border-purple-200 text-purple-800 font-mono text-[10px] font-bold">
                  <Lock class="w-3 h-3 text-purple-600" />
                  <span>View Only</span>
                </span>
              </button>
            </template>

            <!-- 2. ACTIVE & DEACTIVATED STRAND ACTIONS (WHEN UNLOCKED) -->
            <template v-else-if="st.status !== 'Archived'">
              <div class="flex items-center justify-between gap-2">
                <button 
                  @click="editStrand(st)" 
                  class="px-3 py-1.5 rounded-xl font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] transition"
                >
                  Edit
                </button>

                <div class="flex items-center space-x-1.5">
                  <button 
                    v-if="st.status === 'Active'" 
                    @click="updateStrandStatus(st, 'Deactivated')"
                    class="px-2.5 py-1.5 rounded-xl font-semibold bg-amber-50 hover:bg-amber-100 text-amber-800 text-[10px] border border-amber-200 transition"
                    title="Temporarily close new admissions for this strand"
                  >
                    Pause Intake
                  </button>

                  <button 
                    v-if="st.status === 'Deactivated'" 
                    @click="updateStrandStatus(st, 'Active')"
                    class="px-2.5 py-1.5 rounded-xl font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[10px] border border-emerald-200 transition"
                    title="Reopen admissions for this strand"
                  >
                    Reopen Intake
                  </button>

                  <!-- REMOVE STRAND (SOFT-ARCHIVE) -->
                  <button 
                    @click="openRemoveStrandModal(st)"
                    class="px-3 py-1.5 rounded-xl font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 text-[10px] border border-rose-200 transition flex items-center space-x-1"
                    title="Remove strand from active offerings (moves to archived)"
                  >
                    <Trash2 class="w-3 h-3 text-rose-600" />
                    <span>Remove Strand</span>
                  </button>
                </div>
              </div>
            </template>

            <!-- 3. ARCHIVED STRAND ACTIONS (WHEN UNLOCKED) -->
            <template v-else>
              <div class="flex items-center justify-between gap-2">
                <button 
                  @click="updateStrandStatus(st, 'Active')" 
                  class="px-3 py-1.5 rounded-xl font-bold bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] transition flex items-center space-x-1 shadow-sm"
                  title="Restore strand to active offerings"
                >
                  <CheckCircle class="w-3 h-3" />
                  <span>Restore to Active</span>
                </button>

                <!-- PERMANENT HARD DELETE -->
                <button 
                  @click="confirmDeleteStrand(st)" 
                  class="px-2.5 py-1.5 rounded-xl font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 text-[10px] border border-rose-200 transition flex items-center space-x-1"
                  title="Permanently remove strand from database"
                >
                  <Trash2 class="w-3 h-3 text-rose-600" />
                  <span>Permanently Delete</span>
                </button>
              </div>
            </template>
          </div>
        </div>
      </div>

      <div v-if="filteredStrands.length === 0" class="p-12 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs">
        No academic strands found matching the filter criteria.
      </div>
    </div>

    <!-- TAB 3: CLASS SECTIONS & TEACHER ADVISERS -->
    <div v-if="activeTab === 'sections'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
          <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 font-bold text-[10px] uppercase tracking-wider mb-1">
            <Users class="w-3 h-3 text-purple-700" />
            <span>Classroom Capacity & Faculty Loading</span>
          </div>
          <h2 class="text-lg font-extrabold text-slate-900">Class Sectioning & Advisers</h2>
          <p class="text-xs text-slate-500">Monitor section capacities, room assignments, and assigned faculty advisers across JHS and SHS.</p>
        </div>
        <button @click="openSectionModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-500 text-white shadow-md transition flex items-center space-x-1.5 shrink-0">
          <Plus class="w-4 h-4" />
          <span>Create Section</span>
        </button>
      </div>

      <!-- Sections Filter & Sorting Toolbar -->
      <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3 text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <!-- Search Bar -->
          <div>
            <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Search Sections</label>
            <div class="relative">
              <Search class="w-3.5 h-3.5 absolute left-2.5 top-2.5 text-slate-400" />
              <input 
                v-model="sectionFilter.search" 
                type="text" 
                placeholder="Section, room, or adviser..." 
                class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition" 
              />
            </div>
          </div>

          <!-- Grade Level Filter -->
          <div>
            <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Grade Level</label>
            <select v-model="sectionFilter.grade_level_id" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs focus:ring-2 focus:ring-purple-500">
              <option value="">All Grade Levels</option>
              <option v-for="g in curriculumData.grade_levels" :key="g.id" :value="g.id">{{ g.name }} ({{ g.category }})</option>
            </select>
          </div>

          <!-- Track / Strand Filter -->
          <div>
            <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Track / Strand</label>
            <select v-model="sectionFilter.strand_id" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs focus:ring-2 focus:ring-purple-500">
              <option value="">All Strands & JHS</option>
              <option value="jhs">Junior High Sections (JHS)</option>
              <option v-for="s in curriculumData.strands" :key="s.id" :value="s.id">{{ s.code }} - {{ s.name }}</option>
            </select>
          </div>

          <!-- Sort Order Dropdown -->
          <div>
            <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1 flex items-center justify-between">
              <span>Sort Order</span>
              <span class="text-purple-600 font-semibold lowercase">({{ filteredSections.length }} found)</span>
            </label>
            <select v-model="sectionFilter.sortBy" class="w-full px-3 py-1.5 rounded-xl border border-purple-300 bg-purple-50/50 text-purple-900 font-semibold text-xs focus:ring-2 focus:ring-purple-500">
              <option value="grade_asc">Grade Level (Grade 7 → 12)</option>
              <option value="grade_desc">Grade Level (Grade 12 → 7)</option>
              <option value="name_asc">Section Name (A → Z)</option>
              <option value="name_desc">Section Name (Z → A)</option>
              <option value="enrolled_desc">Most Enrolled First</option>
              <option value="slots_desc">Most Available Slots First</option>
              <option value="capacity_desc">Highest Max Capacity</option>
            </select>
          </div>
        </div>

        <!-- Quick Summary Strip -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-200/80 text-xs">
          <div class="flex items-center space-x-2 flex-wrap gap-y-1">
            <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 font-semibold text-slate-700">
              Filtered Sections: <strong class="text-purple-700">{{ filteredSections.length }}</strong> of {{ sectionStats.totalSections }}
            </span>
            <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 font-semibold text-slate-700">
              Total Enrolled: <strong class="text-slate-900">{{ sectionStats.totalEnrolled }}</strong>
            </span>
            <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 font-semibold text-slate-700">
              Total Capacity: <strong class="text-slate-900">{{ sectionStats.totalCapacity }} slots</strong>
            </span>
          </div>

          <button 
            v-if="sectionFilter.search || sectionFilter.grade_level_id || sectionFilter.strand_id || sectionFilter.sortBy !== 'grade_asc'"
            @click="resetSectionFilters"
            class="text-[11px] font-bold text-purple-700 hover:text-purple-900 underline"
          >
            Reset Filters
          </button>
        </div>
      </div>

      <!-- Sections Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="sec in filteredSections" :key="sec.id" class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 hover:border-purple-300 hover:bg-purple-50/20 transition">
          <div class="flex items-center justify-between mb-2">
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 font-mono">
              {{ sec.grade_level_name }} {{ sec.strand_code ? '(' + sec.strand_code + ')' : '' }}
            </span>
            <button @click="openSectionModal(sec)" class="text-xs text-purple-600 font-bold hover:underline">Edit</button>
          </div>
          <h3 class="text-base font-extrabold text-slate-900">{{ sec.name }}</h3>
          <p class="text-xs text-slate-500 mt-0.5">Room: {{ sec.room || 'Unassigned' }}</p>

          <!-- Capacity Bar -->
          <div class="mt-4 pt-3 border-t border-slate-200">
            <div class="flex justify-between text-xs font-semibold mb-1">
              <span>Enrolled Students</span>
              <span>{{ sec.current_enrolled }} / {{ sec.max_capacity }}</span>
            </div>
            <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden">
              <div 
                class="h-full rounded-full bg-purple-600 transition-all duration-300"
                :style="{ width: Math.min(100, (sec.current_enrolled / sec.max_capacity) * 100) + '%' }"
              ></div>
            </div>
          </div>

          <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between">
            <div class="text-[11px] text-slate-600">
              Adviser: <strong class="text-slate-800">{{ sec.adviser_first ? sec.adviser_first + ' ' + sec.adviser_last : 'None Assigned' }}</strong>
            </div>
            <button 
              type="button"
              @click="openRosterModal(sec)" 
              class="px-3 py-1.5 rounded-xl font-bold bg-purple-100 hover:bg-purple-200 text-purple-900 text-[11px] transition flex items-center space-x-1.5 shadow-sm"
            >
              <Users class="w-3.5 h-3.5 text-purple-700" />
              <span>Class Roster ({{ sec.current_enrolled }})</span>
            </button>
          </div>
        </div>
      </div>

      <div v-if="filteredSections.length === 0" class="p-12 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs">
        No class sections match the selected filter and search criteria.
      </div>
    </div>

    <!-- TAB 4: CLASS TIMETABLES & SECTION SCHEDULES -->
    <div v-if="activeTab === 'schedules'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
          <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 font-bold text-[10px] uppercase tracking-wider mb-1">
            <Clock class="w-3 h-3 text-purple-700" />
            <span>Master Class Schedule & Conflict-Free Timetable</span>
          </div>
          <h2 class="text-lg font-extrabold text-slate-900">Class Schedules & Faculty Loading</h2>
          <p class="text-xs text-slate-500">Design weekly section time slots, assign teaching faculty, and prevent double-booking conflicts.</p>
        </div>

        <button 
          @click="openScheduleModal()" 
          class="px-4 py-2 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-500 text-white shadow-md transition flex items-center space-x-1.5 shrink-0"
        >
          <Plus class="w-4 h-4" />
          <span>Add Subject Schedule</span>
        </button>
      </div>

      <!-- Section & Term Selector Bar -->
      <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs relative">
        <!-- Searchable Section Selector -->
        <div class="relative">
          <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Select Section to Schedule</label>
          
          <!-- Trigger Combobox Button -->
          <button 
            type="button" 
            @click="isSectionDropdownOpen = !isSectionDropdownOpen"
            class="w-full px-3 py-2 rounded-xl border border-purple-300 bg-white font-semibold text-purple-950 focus:ring-2 focus:ring-purple-500 flex items-center justify-between text-left shadow-sm hover:border-purple-400 transition"
          >
            <div class="truncate mr-2">
              <span v-if="currentSelectedSection" class="font-bold text-slate-900">
                {{ currentSelectedSection.name }}
              </span>
              <span v-if="currentSelectedSection" class="text-[11px] text-purple-700 ml-1 font-normal">
                ({{ currentSelectedSection.grade_level_name }}{{ currentSelectedSection.strand_code ? ' • ' + currentSelectedSection.strand_code : '' }})
              </span>
              <span v-else class="text-slate-400">Choose a class section...</span>
            </div>
            <ChevronDown class="w-4 h-4 text-purple-600 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': isSectionDropdownOpen }" />
          </button>

          <!-- Floating Backdrop to click outside -->
          <div 
            v-if="isSectionDropdownOpen" 
            @click="isSectionDropdownOpen = false" 
            class="fixed inset-0 z-30"
          ></div>

          <!-- Floating Searchable Dropdown Card -->
          <div 
            v-if="isSectionDropdownOpen" 
            class="absolute left-0 top-full mt-1.5 w-full sm:w-[380px] bg-white rounded-2xl border border-slate-200 shadow-2xl z-40 p-3 space-y-2.5 text-xs animate-in fade-in zoom-in-95 duration-150"
          >
            <!-- Search Input -->
            <div class="relative">
              <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input 
                v-model="scheduleSectionSearch" 
                type="text" 
                placeholder="Search section, grade, strand, or room..."
                class="w-full pl-8 pr-7 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-xs focus:ring-2 focus:ring-purple-500 focus:bg-white outline-none"
              />
              <button 
                v-if="scheduleSectionSearch" 
                @click="scheduleSectionSearch = ''" 
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 font-bold text-xs"
              >
                ✕
              </button>
            </div>

            <!-- Grade Category Filter Pills -->
            <div class="flex items-center space-x-1 overflow-x-auto pb-1 text-[10px]">
              <button 
                type="button" 
                @click="scheduleGradeFilter = ''"
                :class="scheduleGradeFilter === '' ? 'bg-purple-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="px-2.5 py-1 rounded-lg whitespace-nowrap transition"
              >
                All ({{ sectionsData.sections?.length || 0 }})
              </button>
              <button 
                type="button" 
                @click="scheduleGradeFilter = 'jhs'"
                :class="scheduleGradeFilter === 'jhs' ? 'bg-purple-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="px-2.5 py-1 rounded-lg whitespace-nowrap transition"
              >
                JHS (8)
              </button>
              <button 
                type="button" 
                @click="scheduleGradeFilter = 'g11'"
                :class="scheduleGradeFilter === 'g11' ? 'bg-purple-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="px-2.5 py-1 rounded-lg whitespace-nowrap transition"
              >
                Grade 11 (16)
              </button>
              <button 
                type="button" 
                @click="scheduleGradeFilter = 'g12'"
                :class="scheduleGradeFilter === 'g12' ? 'bg-purple-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="px-2.5 py-1 rounded-lg whitespace-nowrap transition"
              >
                Grade 12 (16)
              </button>
            </div>

            <!-- Scrollable Sections List -->
            <div class="max-h-56 overflow-y-auto space-y-1 divide-y divide-slate-100 pr-1">
              <button 
                v-for="sec in filteredScheduleSections" 
                :key="sec.id"
                type="button"
                @click="selectScheduleSection(sec)"
                :class="selectedScheduleSectionId === sec.id ? 'bg-purple-50 text-purple-950 font-bold border border-purple-200' : 'hover:bg-slate-50 text-slate-800 border border-transparent'"
                class="w-full p-2 rounded-xl text-left transition flex items-center justify-between group"
              >
                <div class="min-w-0 pr-2">
                  <div class="flex items-center space-x-1.5">
                    <span class="font-bold text-xs truncate">{{ sec.name }}</span>
                    <span v-if="sec.strand_code" class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-purple-100 text-purple-800 font-mono">
                      {{ sec.strand_code }}
                    </span>
                  </div>
                  <div class="text-[10px] text-slate-400 mt-0.5 flex items-center space-x-2">
                    <span>{{ sec.grade_level_name }}</span>
                    <span>•</span>
                    <span class="truncate">{{ sec.room || 'No Room Assigned' }}</span>
                  </div>
                </div>

                <div v-if="selectedScheduleSectionId === sec.id" class="text-purple-600 shrink-0">
                  <Check class="w-4 h-4" />
                </div>
              </button>

              <div v-if="filteredScheduleSections.length === 0" class="py-4 text-center text-slate-400 text-[11px]">
                No sections match "<span class="font-semibold text-slate-600">{{ scheduleSectionSearch }}</span>"
              </div>
            </div>
          </div>
        </div>

        <!-- Academic Term / Semester (Visible ONLY for Senior High School) -->
        <div v-if="isCurrentSectionSHS">
          <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Academic Term / Semester</label>
          <select 
            v-model="selectedScheduleSemester" 
            @change="loadSectionScheduleData" 
            class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-purple-500 font-medium"
          >
            <option value="1st Semester">1st Semester</option>
            <option value="2nd Semester">2nd Semester</option>
          </select>
        </div>

        <div class="flex items-center justify-between sm:justify-end gap-3 pt-4 sm:pt-0" :class="{ 'sm:col-span-2': !isCurrentSectionSHS }">
          <div class="text-right">
            <div class="text-xs font-bold text-slate-900">{{ activeSectionSchedule?.schedules?.length || 0 }} Scheduled Classes</div>
            <div class="text-[11px] text-purple-700 font-semibold">
              {{ totalScheduledHours.toFixed(1) }} Total Weekly Hours
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoadingSchedule" class="p-12 text-center text-slate-400">
        <span class="w-7 h-7 border-2 border-purple-600 border-t-transparent rounded-full animate-spin inline-block"></span>
        <p class="mt-2 text-xs">Loading section timetable matrix...</p>
      </div>

      <!-- Schedule Timetable Matrix / Cards -->
      <div v-else class="space-y-4">
        <!-- Visual Day-by-Day View -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
          <div 
            v-for="day in ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']" 
            :key="day"
            class="rounded-2xl border border-slate-200 bg-slate-50/50 p-3 flex flex-col justify-between"
          >
            <div class="border-b border-slate-200 pb-2 mb-2 flex items-center justify-between">
              <h4 class="font-extrabold text-xs text-slate-800">{{ day }}</h4>
              <span class="text-[10px] font-bold text-purple-700 font-mono">
                {{ getSchedulesForDay(day).length }} class{{ getSchedulesForDay(day).length === 1 ? '' : 'es' }}
              </span>
            </div>

            <div class="space-y-2 flex-1">
              <div 
                v-for="item in getSchedulesForDay(day)" 
                :key="item.id"
                class="p-2.5 bg-white rounded-xl border border-slate-200 shadow-sm hover:border-purple-300 transition text-xs space-y-1.5 group"
              >
                <div class="flex items-center justify-between">
                  <span class="font-mono font-extrabold text-[10px] text-purple-700">{{ item.subject_code }}</span>
                  <span class="text-[9px] px-1.5 py-0.2 rounded bg-purple-50 text-purple-800 font-semibold">
                    {{ formatTime(item.time_start) }} - {{ formatTime(item.time_end) }}
                  </span>
                </div>

                <div class="font-bold text-slate-900 text-[11px] line-clamp-1" :title="item.subject_title">
                  {{ item.subject_title }}
                </div>

                <div class="text-[10px] text-slate-600 flex items-center space-x-1.5 pt-0.5">
                  <User class="w-3 h-3 text-purple-600 shrink-0" />
                  <span class="truncate">{{ item.teacher_first ? item.teacher_first + ' ' + item.teacher_last : 'Unassigned Teacher' }}</span>
                </div>

                <div class="text-[10px] text-slate-500 flex items-center space-x-1.5">
                  <MapPin class="w-3 h-3 text-slate-400 shrink-0" />
                  <span class="truncate">{{ item.room || 'Room Unassigned' }}</span>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                  <button 
                    type="button"
                    @click.stop="openScheduleModal(item)" 
                    class="px-2.5 py-1 rounded-lg font-bold text-[10px] bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 transition flex items-center space-x-1 shadow-2xs"
                  >
                    <Pencil class="w-2.5 h-2.5" />
                    <span>Edit Period</span>
                  </button>

                  <button 
                    type="button"
                    @click.stop="openDeleteScheduleModal(item)" 
                    class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition"
                    title="Remove period from section timetable"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>

              <div v-if="getSchedulesForDay(day).length === 0" class="h-24 flex items-center justify-center text-slate-300 text-[10px] italic">
                No classes
              </div>
            </div>
          </div>
        </div>

        <!-- Schedule Items List Table -->
        <div class="mt-6 rounded-2xl border border-slate-200 overflow-hidden">
          <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200 font-bold text-xs text-slate-700 flex items-center justify-between">
            <span>All Scheduled Periods for Current Section</span>
            <span class="font-mono text-[11px] text-purple-700">{{ activeSectionSchedule?.schedules?.length || 0 }} Items</span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
              <thead>
                <tr class="bg-slate-50/50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                  <th class="p-3">Day</th>
                  <th class="p-3">Time Period</th>
                  <th class="p-3">Subject Code & Title</th>
                  <th class="p-3">Assigned Faculty</th>
                  <th class="p-3">Room / Lab</th>
                  <th class="p-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="sch in activeSectionSchedule?.schedules || []" :key="sch.id" class="hover:bg-slate-50 transition">
                  <td class="p-3 font-bold text-slate-800">{{ sch.day_of_week }}</td>
                  <td class="p-3 font-mono font-semibold text-purple-800">
                    {{ formatTime(sch.time_start) }} – {{ formatTime(sch.time_end) }}
                  </td>
                  <td class="p-3">
                    <span class="font-bold font-mono text-purple-700 mr-1.5">{{ sch.subject_code }}</span>
                    <span class="text-slate-800 font-medium">{{ sch.subject_title }}</span>
                  </td>
                  <td class="p-3 text-slate-700">
                    {{ sch.teacher_first ? sch.teacher_first + ' ' + sch.teacher_last : 'None Assigned' }}
                  </td>
                  <td class="p-3 font-mono text-slate-600">{{ sch.room || 'Unassigned' }}</td>
                  <td class="p-3 text-right space-x-1.5">
                    <button @click="openScheduleModal(sch)" class="text-purple-600 font-bold hover:underline">Edit</button>
                    <button @click="openDeleteScheduleModal(sch)" class="text-rose-600 font-bold hover:underline">Delete</button>
                  </td>
                </tr>
                <tr v-if="!activeSectionSchedule?.schedules || activeSectionSchedule.schedules.length === 0">
                  <td colspan="6" class="p-8 text-center text-slate-400">
                    No schedules defined for this section and semester yet. Click "+ Add Subject Schedule" above.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 5: SCHOOL EVENTS & ACADEMIC CALENDAR -->
    <div v-if="activeTab === 'events'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
          <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 font-bold text-[10px] uppercase tracking-wider mb-1">
            <Calendar class="w-3 h-3 text-purple-700" />
            <span>Official Institutional Calendar & Milestones</span>
          </div>
          <h2 class="text-lg font-extrabold text-slate-900">School Events & Academic Calendar</h2>
          <p class="text-xs text-slate-500">Plan and broadcast academic milestones, examination periods, holidays, and campus events.</p>
        </div>

        <button 
          @click="openEventModal()" 
          class="px-4 py-2 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-500 text-white shadow-md transition flex items-center space-x-1.5 shrink-0"
        >
          <Plus class="w-4 h-4" />
          <span>Add School Event</span>
        </button>
      </div>

      <!-- Upcoming Milestones Strip -->
      <div class="p-4 bg-gradient-to-r from-purple-900 to-indigo-950 rounded-2xl text-white space-y-3">
        <div class="flex items-center justify-between">
          <h3 class="font-extrabold text-xs uppercase tracking-wider flex items-center space-x-1.5 text-purple-300">
            <Sparkles class="w-3.5 h-3.5" />
            <span>Upcoming Academic Milestones & Deadlines</span>
          </h3>
          <span class="text-[10px] text-purple-200 font-mono">SY 2026-2027</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div 
            v-for="up in calendarData.upcoming" 
            :key="up.id"
            class="p-3 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10 space-y-1"
          >
            <div class="flex items-center justify-between text-[10px] font-bold">
              <span class="px-2 py-0.2 rounded uppercase" :class="getEventCategoryBadgeClass(up.event_category)">
                {{ up.event_category }}
              </span>
              <span class="font-mono text-purple-200">{{ formatEventDate(up.start_date) }}</span>
            </div>
            <div class="font-bold text-xs text-white line-clamp-1">{{ up.title }}</div>
            <div class="text-[10px] text-purple-200/80 line-clamp-1">{{ up.location || 'All Campuses' }}</div>
          </div>
          <div v-if="!calendarData.upcoming || calendarData.upcoming.length === 0" class="col-span-full text-center text-xs text-purple-300">
            No upcoming events scheduled.
          </div>
        </div>
      </div>

      <!-- Filters & Category Navigation -->
      <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center space-x-2 flex-wrap gap-y-1.5">
          <button 
            type="button" 
            @click="eventFilter.category = ''; loadEventsData()"
            :class="eventFilter.category === '' ? 'bg-purple-600 text-white font-bold' : 'bg-white text-slate-700 border border-slate-200'"
            class="px-3 py-1.5 rounded-xl transition text-[11px]"
          >
            All Events
          </button>
          <button 
            type="button" 
            @click="eventFilter.category = 'Academic'; loadEventsData()"
            :class="eventFilter.category === 'Academic' ? 'bg-purple-600 text-white font-bold' : 'bg-white text-slate-700 border border-slate-200'"
            class="px-3 py-1.5 rounded-xl transition text-[11px]"
          >
            Academic Milestones
          </button>
          <button 
            type="button" 
            @click="eventFilter.category = 'Examination'; loadEventsData()"
            :class="eventFilter.category === 'Examination' ? 'bg-purple-600 text-white font-bold' : 'bg-white text-slate-700 border border-slate-200'"
            class="px-3 py-1.5 rounded-xl transition text-[11px]"
          >
            Examinations
          </button>
          <button 
            type="button" 
            @click="eventFilter.category = 'Holiday'; loadEventsData()"
            :class="eventFilter.category === 'Holiday' ? 'bg-purple-600 text-white font-bold' : 'bg-white text-slate-700 border border-slate-200'"
            class="px-3 py-1.5 rounded-xl transition text-[11px]"
          >
            Holidays
          </button>
          <button 
            type="button" 
            @click="eventFilter.category = 'Activity'; loadEventsData()"
            :class="eventFilter.category === 'Activity' ? 'bg-purple-600 text-white font-bold' : 'bg-white text-slate-700 border border-slate-200'"
            class="px-3 py-1.5 rounded-xl transition text-[11px]"
          >
            Activities & Intramurals
          </button>
          <button 
            type="button" 
            @click="eventFilter.category = 'Administrative'; loadEventsData()"
            :class="eventFilter.category === 'Administrative' ? 'bg-purple-600 text-white font-bold' : 'bg-white text-slate-700 border border-slate-200'"
            class="px-3 py-1.5 rounded-xl transition text-[11px]"
          >
            Administrative & PTA
          </button>
        </div>

        <div class="flex items-center space-x-2">
          <select 
            v-model="eventFilter.audience" 
            @change="loadEventsData" 
            class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs"
          >
            <option value="">All Audiences</option>
            <option value="All">Target: All</option>
            <option value="Students">Target: Students</option>
            <option value="Faculty">Target: Faculty</option>
            <option value="Applicants">Target: Applicants</option>
          </select>
        </div>
      </div>

      <!-- Events List Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div 
          v-for="ev in calendarData.events" 
          :key="ev.id"
          class="p-5 rounded-2xl border border-slate-200 bg-slate-50/40 hover:border-purple-300 hover:bg-purple-50/10 transition flex flex-col justify-between"
        >
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider" :class="getEventCategoryBadgeClass(ev.event_category)">
                {{ ev.event_category }}
              </span>
              <span class="text-[10px] font-semibold text-slate-400">Audience: {{ ev.target_audience }}</span>
            </div>

            <h3 class="text-sm font-extrabold text-slate-900">{{ ev.title }}</h3>
            <p class="text-xs text-slate-500 leading-relaxed">{{ ev.description || 'No additional details provided.' }}</p>

            <div class="pt-2 border-t border-slate-100 space-y-1 text-[11px] text-slate-600">
              <div class="flex items-center space-x-1.5 font-medium">
                <Calendar class="w-3.5 h-3.5 text-purple-600" />
                <span>{{ formatEventDateRange(ev.start_date, ev.end_date) }}</span>
              </div>
              <div v-if="ev.start_time" class="flex items-center space-x-1.5 text-slate-500">
                <Clock class="w-3.5 h-3.5 text-slate-400" />
                <span>{{ formatTime(ev.start_time) }} {{ ev.end_time ? ' - ' + formatTime(ev.end_time) : '' }}</span>
              </div>
              <div v-if="ev.location" class="flex items-center space-x-1.5 text-slate-500">
                <MapPin class="w-3.5 h-3.5 text-slate-400" />
                <span>{{ ev.location }}</span>
              </div>
            </div>
          </div>

          <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-end space-x-2 text-xs">
            <button @click="openEventModal(ev)" class="px-3 py-1.5 rounded-xl font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] transition">
              Edit
            </button>
            <button @click="openDeleteEventModal(ev)" class="px-3 py-1.5 rounded-xl font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] transition">
              Delete
            </button>
          </div>
        </div>

        <div v-if="!calendarData.events || calendarData.events.length === 0" class="col-span-full p-12 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs">
          No school events found for the selected category and criteria. Click "+ Add School Event" above.
        </div>
      </div>
    </div>

    <!-- SUBJECT MODAL -->
    <div v-if="showSubjectModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl border border-slate-200 text-xs">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
          <div>
            <h3 class="text-base font-extrabold text-slate-900">{{ subjectForm.id ? 'Edit Curriculum Subject' : 'Add Subject to Curriculum' }}</h3>
            <p class="text-[11px] text-slate-500">Define course code, description, credit units, and academic prerequisites.</p>
          </div>
          <button @click="showSubjectModal = false" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
        </div>

        <form @submit.prevent="saveSubject" class="space-y-4">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Subject Code *</label>
              <input v-model="subjectForm.code" type="text" required placeholder="e.g. STEM-PRECALC" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold" />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Classification *</label>
              <select v-model="subjectForm.category" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white">
                <option value="JHS Core">JHS Core (Grade 7-10)</option>
                <option value="SHS Core">SHS Core (All Strands)</option>
                <option value="SHS Applied">SHS Applied (Track Contextualized)</option>
                <option value="SHS Specialized">SHS Specialized (Strand Specific)</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Subject Title *</label>
            <input v-model="subjectForm.title" type="text" required placeholder="e.g. Pre-Calculus" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-medium" />
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Course Description (Optional)</label>
            <textarea v-model="subjectForm.description" rows="2" placeholder="Brief summary of learning competencies..." class="w-full px-3 py-2 rounded-xl border border-slate-300"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Grade Level *</label>
              <select v-model="subjectForm.grade_level_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white">
                <option v-for="g in curriculumData.grade_levels" :key="g.id" :value="g.id">{{ g.name }} ({{ g.category }})</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Strand (If SHS Specialized)</label>
              <select v-model="subjectForm.strand_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white">
                <option :value="null">-- Core / General --</option>
                <option v-for="s in curriculumData.strands" :key="s.id" :value="s.id">{{ s.code }} - {{ s.name }}</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Semester / Term *</label>
              <select v-model="subjectForm.semester" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white">
                <option value="1st Semester">1st Semester</option>
                <option value="2nd Semester">2nd Semester</option>
                <option value="Full Year">Full Year (JHS)</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Lecture Hrs/Wk</label>
              <input v-model.number="subjectForm.lecture_hours" type="number" step="0.5" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono" />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Credit Units *</label>
              <input v-model.number="subjectForm.units" type="number" step="0.5" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold" />
            </div>
          </div>

          <!-- Prerequisite Subject Selector -->
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Academic Prerequisite (Optional)</label>
            <select v-model="subjectForm.prerequisite_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white font-mono">
              <option :value="null">-- None (No Prerequisite) --</option>
              <option 
                v-for="sub in eligiblePrerequisites" 
                :key="sub.id" 
                :value="sub.id"
              >
                [{{ sub.code }}] {{ sub.title }} ({{ sub.grade_level_name }})
              </option>
            </select>
            <span class="text-[10px] text-slate-400 block mt-1">Student must complete the selected subject before taking this course.</span>
          </div>

          <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100">
            <button type="button" @click="showSubjectModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold">Cancel</button>
            <button type="submit" class="px-5 py-2 rounded-xl font-bold bg-purple-600 hover:bg-purple-500 text-white shadow-md">
              {{ subjectForm.id ? 'Save Changes' : 'Add to Curriculum' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- DELETE / ARCHIVE SUBJECT MODAL -->
    <div v-if="deleteModal.isOpen" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-xs">
        <div class="flex items-center space-x-3 mb-4">
          <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold shrink-0">
            <Trash2 class="w-5 h-5" />
          </div>
          <div>
            <h3 class="text-sm font-extrabold text-slate-900">Remove Subject from Curriculum?</h3>
            <p class="text-[11px] text-slate-500">Subject: <strong class="font-mono text-purple-700">{{ deleteModal.subject?.code }}</strong> • {{ deleteModal.subject?.title }}</p>
          </div>
        </div>

        <div class="p-3.5 bg-amber-50 rounded-2xl border border-amber-200 text-amber-900 text-xs space-y-1.5 mb-4">
          <div class="flex items-center space-x-1 font-bold">
            <AlertCircle class="w-4 h-4 text-amber-700 shrink-0" />
            <span>Academic Records Safeguard:</span>
          </div>
          <p class="text-[11px] text-amber-800">
            • If this subject has <strong>active student enrollments or grades</strong>, it will be safely <strong>archived/deactivated</strong> so historical permanent records (SF10) are preserved.<br/>
            • If it is unassigned, it will be permanently deleted.
          </p>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-2">
          <button 
            type="button" 
            @click="deleteModal.isOpen = false" 
            :disabled="deleteModal.isDeleting"
            class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold"
          >
            Cancel
          </button>
          <button 
            type="button" 
            @click="executeDeleteSubject" 
            :disabled="deleteModal.isDeleting"
            class="px-5 py-2.5 rounded-xl font-bold bg-rose-600 hover:bg-rose-500 text-white shadow-md transition flex items-center space-x-1.5"
          >
            <span v-if="deleteModal.isDeleting" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span>{{ deleteModal.isDeleting ? 'Processing...' : 'Confirm Remove' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- STRAND MANAGEMENT MODAL -->
    <div v-if="showStrandModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 text-xs">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
          <div>
            <h3 class="text-base font-extrabold text-slate-900">{{ strandForm.id ? 'Edit Academic Strand' : 'Add New Academic Strand' }}</h3>
            <p class="text-[11px] text-slate-500">Configure track classification, program code, and intake lifecycle availability.</p>
          </div>
          <button @click="showStrandModal = false" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
        </div>

        <form @submit.prevent="saveStrand" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Academic Track *</label>
              <select v-model="strandForm.track_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs">
                <option v-for="t in curriculumData.tracks" :key="t.id" :value="t.id">{{ t.code }} - {{ t.name }}</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Strand Code *</label>
              <input v-model="strandForm.code" type="text" required placeholder="e.g. STEM / TVL-ICT" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold uppercase" />
            </div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Strand Full Name *</label>
            <input v-model="strandForm.name" type="text" required placeholder="e.g. Science, Technology, Engineering, and Mathematics" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-medium" />
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Description (Competencies & Career Pathways)</label>
            <textarea v-model="strandForm.description" rows="2" placeholder="e.g. Focuses on advanced physics, calculus, engineering research..." class="w-full px-3 py-2 rounded-xl border border-slate-300"></textarea>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Admission & Intake Status *</label>
            <select v-model="strandForm.status" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white font-semibold">
              <option value="Active">Active (Open for Admissions & Enrollment)</option>
              <option value="Deactivated">Deactivated (Temporarily Paused / Closed for New Intake)</option>
              <option value="Archived">Archived (Retired / Phased Out Program)</option>
            </select>
            <span class="text-[10px] text-slate-400 block mt-1">
              Deactivated or Archived strands will not appear in the Public Registration dropdown for new applicants.
            </span>
          </div>

          <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100">
            <button type="button" @click="showStrandModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold">Cancel</button>
            <button type="submit" class="px-5 py-2 rounded-xl font-bold bg-purple-600 hover:bg-purple-500 text-white shadow-md">
              {{ strandForm.id ? 'Save Strand Changes' : 'Create Strand' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- 1. SOFT-REMOVE STRAND MODAL (Move to Archive) -->
    <div v-if="removeStrandModal.isOpen" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-xs">
        <div class="flex items-center space-x-3 mb-4">
          <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold shrink-0">
            <Trash2 class="w-5 h-5" />
          </div>
          <div>
            <h3 class="text-sm font-extrabold text-slate-900">Remove Strand from Active Offerings?</h3>
            <p class="text-[11px] text-slate-500">Strand: <strong class="font-mono text-purple-700">{{ removeStrandModal.strand?.code }}</strong> • {{ removeStrandModal.strand?.name }}</p>
          </div>
        </div>

        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-slate-700 text-xs space-y-2 mb-4">
          <div class="flex items-center space-x-1.5 font-bold text-slate-900">
            <CheckCircle class="w-4 h-4 text-purple-600 shrink-0" />
            <span>Safe Archival Workflow:</span>
          </div>
          <p class="text-[11px] text-slate-600 leading-relaxed">
            Removing this strand will automatically move it to <strong>Archived Strands</strong> and hide it from active student admissions.
            <br/><br/>
            All existing student transcripts (SF10) and historical records are safely preserved. You can <strong>Restore</strong> this strand back to Active at any time.
          </p>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-2">
          <button 
            type="button" 
            @click="removeStrandModal.isOpen = false" 
            :disabled="removeStrandModal.isRemoving"
            class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold"
          >
            Cancel
          </button>
          
          <button 
            type="button" 
            @click="executeRemoveStrand"
            :disabled="removeStrandModal.isRemoving"
            class="px-5 py-2.5 rounded-xl font-bold bg-rose-600 hover:bg-rose-500 text-white shadow-md transition flex items-center space-x-1.5"
          >
            <span v-if="removeStrandModal.isRemoving" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span>{{ removeStrandModal.isRemoving ? 'Removing...' : 'Confirm Remove to Archive' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- 2. PERMANENT HARD DELETE STRAND MODAL (From Archive Only) -->
    <div v-if="deleteStrandModal.isOpen" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-xs">
        <div class="flex items-center space-x-3 mb-4">
          <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold shrink-0">
            <Trash2 class="w-5 h-5" />
          </div>
          <div>
            <h3 class="text-sm font-extrabold text-slate-900">Permanently Delete Strand from Database?</h3>
            <p class="text-[11px] text-slate-500">Strand: <strong class="font-mono text-purple-700">{{ deleteStrandModal.strand?.code }}</strong> • {{ deleteStrandModal.strand?.name }}</p>
          </div>
        </div>

        <div v-if="deleteStrandModal.strand?.enrolled_students_count > 0 || deleteStrandModal.strand?.active_sections_count > 0" class="p-3.5 bg-rose-50 rounded-2xl border border-rose-200 text-rose-900 text-xs space-y-1.5 mb-4">
          <div class="flex items-center space-x-1 font-bold">
            <AlertCircle class="w-4 h-4 text-rose-700 shrink-0" />
            <span>Academic Transcript Safeguard Notice:</span>
          </div>
          <p class="text-[11px] text-rose-800 leading-relaxed">
            This strand currently has <strong>{{ deleteStrandModal.strand?.enrolled_students_count }} enrolled students</strong> and <strong>{{ deleteStrandModal.strand?.active_sections_count }} class sections</strong>.
            <br/><br/>
            Hard deleting this strand is blocked to prevent permanent data corruption of historical student transcripts (SF10).
          </p>
        </div>

        <div v-else class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-slate-700 text-xs mb-4">
          This strand has no enrolled students or active sections and will be permanently removed from the database. This action cannot be undone.
        </div>

        <div class="flex items-center justify-end space-x-2 pt-2">
          <button 
            type="button" 
            @click="deleteStrandModal.isOpen = false" 
            :disabled="deleteStrandModal.isDeleting"
            class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold"
          >
            Cancel
          </button>

          <button 
            v-if="!(deleteStrandModal.strand?.enrolled_students_count > 0 || deleteStrandModal.strand?.active_sections_count > 0)"
            type="button" 
            @click="executeDeleteStrand" 
            :disabled="deleteStrandModal.isDeleting"
            class="px-5 py-2.5 rounded-xl font-bold bg-rose-600 hover:bg-rose-500 text-white shadow-md transition flex items-center space-x-1.5"
          >
            <span v-if="deleteStrandModal.isDeleting" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span>{{ deleteStrandModal.isDeleting ? 'Deleting...' : 'Permanently Delete' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- SECTION MODAL -->
    <div v-if="showSectionModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 text-xs">
        <h3 class="text-base font-bold text-slate-900 mb-4">{{ sectionForm.id ? 'Edit Class Section' : 'Create Section' }}</h3>
        <form @submit.prevent="saveSection" class="space-y-4">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Section Name *</label>
            <input v-model="sectionForm.name" type="text" required placeholder="e.g. Grade 11 - STEM Einstein" class="w-full px-3 py-2 rounded-xl border border-slate-300" />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Grade Level *</label>
              <select v-model="sectionForm.grade_level_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white">
                <option v-for="g in curriculumData.grade_levels" :key="g.id" :value="g.id">{{ g.name }}</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Strand</label>
              <select v-model="sectionForm.strand_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white">
                <option :value="null">-- JHS General --</option>
                <option v-for="s in curriculumData.strands" :key="s.id" :value="s.id">{{ s.code }}</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Max Student Capacity</label>
              <input v-model.number="sectionForm.max_capacity" type="number" class="w-full px-3 py-2 rounded-xl border border-slate-300" />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Room Assignment</label>
              <input v-model="sectionForm.room" type="text" placeholder="e.g. Science Wing 201" class="w-full px-3 py-2 rounded-xl border border-slate-300" />
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-3">
            <button type="button" @click="showSectionModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100">Cancel</button>
            <button type="submit" class="px-5 py-2 rounded-xl font-bold bg-purple-600 hover:bg-purple-500 text-white shadow-md">Save Section</button>
          </div>
        </form>
      </div>
    </div>

    <!-- CLASS ROSTER MODAL -->
    <div v-if="selectedRosterSection" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[88vh] overflow-y-auto p-6 sm:p-8 shadow-2xl border border-slate-200 text-xs">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
          <div>
            <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded bg-purple-100 text-purple-900 font-bold font-mono text-[10px] uppercase">
              {{ selectedRosterSection.grade_level_name }} {{ selectedRosterSection.strand_code ? '(' + selectedRosterSection.strand_code + ')' : '' }}
            </div>
            <h3 class="text-lg font-extrabold text-slate-900 mt-1">Class Roster: {{ selectedRosterSection.name }}</h3>
            <p class="text-xs text-slate-500">Room: {{ selectedRosterSection.room || 'Unassigned' }} • Capacity: {{ selectedRosterSection.current_enrolled }} / {{ selectedRosterSection.max_capacity }}</p>
          </div>
          <button @click="selectedRosterSection = null" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
        </div>

        <div v-if="isLoadingRoster" class="p-8 text-center text-slate-400">
          <span class="w-6 h-6 border-2 border-purple-600 border-t-transparent rounded-full animate-spin inline-block"></span>
          <p class="mt-2 text-xs">Loading enrolled students...</p>
        </div>

        <div v-else-if="rosterStudents.length === 0" class="p-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
          No students officially enrolled in this section yet.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 text-[11px] uppercase tracking-wider">
                <th class="p-3">#</th>
                <th class="p-3">Student Number</th>
                <th class="p-3">Student Name</th>
                <th class="p-3">LRN</th>
                <th class="p-3">Contact</th>
                <th class="p-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="(stud, idx) in rosterStudents" :key="stud.enrollment_id" class="hover:bg-slate-50 transition">
                <td class="p-3 font-mono font-bold text-slate-400">{{ idx + 1 }}</td>
                <td class="p-3 font-mono font-bold text-purple-700">{{ stud.student_no || stud.official_student_id }}</td>
                <td class="p-3 font-bold text-slate-900 uppercase">{{ stud.last_name }}, {{ stud.first_name }} {{ stud.middle_name || '' }}</td>
                <td class="p-3 font-mono text-slate-600">{{ stud.lrn || 'N/A' }}</td>
                <td class="p-3 text-slate-500 font-mono">{{ stud.contact_number || '-' }}</td>
                <td class="p-3 text-right">
                  <button 
                    type="button"
                    @click="openTransferModal(stud)" 
                    class="px-2.5 py-1.5 rounded-xl font-bold bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 text-[11px] transition inline-flex items-center space-x-1"
                    title="Move student to another section"
                  >
                    <ArrowRightLeft class="w-3.5 h-3.5 text-amber-700" />
                    <span>Transfer Section</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TRANSFER SECTION MODAL -->
    <div v-if="transferModal.isOpen" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-xs">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
          <div class="flex items-center space-x-2">
            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold">
              <ArrowRightLeft class="w-4 h-4" />
            </div>
            <div>
              <h3 class="text-sm font-extrabold text-slate-900">Transfer Student Section</h3>
              <p class="text-[10px] text-slate-500">Move student to another section within the same grade & strand.</p>
            </div>
          </div>
          <button @click="closeTransferModal" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
        </div>

        <div v-if="transferModal.error" class="p-3 mb-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center space-x-2">
          <AlertCircle class="w-4 h-4 text-rose-600 shrink-0" />
          <span>{{ transferModal.error }}</span>
        </div>

        <div class="space-y-3">
          <!-- Student Info Card -->
          <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
            <div class="flex justify-between">
              <span class="text-slate-500">Student:</span>
              <strong class="text-slate-900 font-bold uppercase">{{ transferModal.student?.last_name }}, {{ transferModal.student?.first_name }}</strong>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-500">Student ID:</span>
              <strong class="font-mono text-purple-700">{{ transferModal.student?.student_no || transferModal.student?.official_student_id }}</strong>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-500">Current Section:</span>
              <strong class="text-slate-800">{{ transferModal.student?.section_name }}</strong>
            </div>
          </div>

          <!-- Target Section Selection -->
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Select Target Section *</label>
            <select v-model="transferModal.targetSectionId" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs">
              <option value="">-- Choose New Section --</option>
              <option 
                v-for="sec in eligibleSections" 
                :key="sec.id" 
                :value="sec.id"
                :disabled="sec.current_enrolled >= sec.max_capacity"
              >
                {{ sec.name }} ({{ sec.current_enrolled }}/{{ sec.max_capacity }} enrolled • {{ Math.max(0, sec.max_capacity - sec.current_enrolled) }} seats left)
                {{ sec.current_enrolled >= sec.max_capacity ? ' - FULL' : '' }}
              </option>
            </select>
            <span v-if="eligibleSections.length === 0" class="text-[10px] text-amber-600 block mt-1">
              No other sections found for this grade level & strand.
            </span>
          </div>

          <!-- Transfer Reason -->
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Transfer Reason / Remarks</label>
            <input 
              v-model="transferModal.reason" 
              type="text" 
              placeholder="e.g. Schedule adjustment / Parent request" 
              class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs" 
            />
          </div>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-4 mt-4 border-t border-slate-100">
          <button 
            type="button" 
            @click="closeTransferModal" 
            :disabled="transferModal.isTransferring"
            class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold"
          >
            Cancel
          </button>
          <button 
            type="button" 
            @click="submitSectionTransfer" 
            :disabled="!transferModal.targetSectionId || transferModal.isTransferring"
            class="px-5 py-2.5 rounded-xl font-bold bg-amber-500 hover:bg-amber-400 disabled:opacity-50 text-slate-950 shadow-md transition flex items-center space-x-1.5"
          >
            <span v-if="transferModal.isTransferring" class="w-3.5 h-3.5 border-2 border-slate-950 border-t-transparent rounded-full animate-spin"></span>
            <span>{{ transferModal.isTransferring ? 'Transferring...' : 'Confirm Section Transfer' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- STRAND BLUEPRINT & FULL CURRICULUM MODAL -->
    <div v-if="selectedStrandForDetails" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl border border-slate-200 text-xs space-y-6">
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between border-b border-slate-100 pb-4">
          <div>
            <div class="flex items-center space-x-2 mb-1.5">
              <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider font-mono" :class="getTrackBadgeClass(selectedStrandForDetails.track_code)">
                {{ selectedStrandForDetails.track_name || 'Academic Track' }}
              </span>
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase" :class="getStrandStatusBadgeClass(selectedStrandForDetails.status)">
                {{ selectedStrandForDetails.status || 'Active' }}
              </span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 flex items-center space-x-2">
              <span class="text-purple-700 font-mono">[{{ selectedStrandForDetails.code }}]</span>
              <span>{{ selectedStrandForDetails.name }}</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed max-w-2xl">{{ selectedStrandForDetails.description || 'No description provided.' }}</p>
          </div>
          <button @click="selectedStrandForDetails = null" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-sm shrink-0">✕</button>
        </div>

        <!-- Strand Sections Summary Strip -->
        <div class="bg-purple-50/50 p-4 rounded-2xl border border-purple-100 space-y-3">
          <div class="flex items-center justify-between">
            <h4 class="font-extrabold text-purple-950 flex items-center space-x-1.5">
              <Users class="w-4 h-4 text-purple-700" />
              <span>Assigned Class Sections ({{ strandSections.length }})</span>
            </h4>
            <span class="text-[11px] font-semibold text-purple-800">
              Total Capacity: <strong>{{ strandSections.reduce((acc, s) => acc + Number(s.current_enrolled || 0), 0) }} / {{ strandSections.reduce((acc, s) => acc + Number(s.max_capacity || 40), 0) }} Students</strong>
            </span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div v-for="sec in strandSections" :key="sec.id" class="p-3 bg-white rounded-xl border border-purple-100 shadow-2xl/5">
              <div class="font-bold text-slate-900 text-xs">{{ sec.name }}</div>
              <div class="text-[11px] text-slate-500 mt-0.5">{{ sec.room || 'Room unassigned' }}</div>
              <div class="mt-2 flex items-center justify-between text-[10px] font-semibold text-slate-600">
                <span>{{ sec.current_enrolled }} / {{ sec.max_capacity }} enrolled</span>
                <span :class="sec.current_enrolled >= sec.max_capacity ? 'text-rose-600' : 'text-emerald-600'">
                  {{ sec.current_enrolled >= sec.max_capacity ? 'FULL' : `${sec.max_capacity - sec.current_enrolled} left` }}
                </span>
              </div>
            </div>
            <div v-if="strandSections.length === 0" class="col-span-full text-center p-3 text-slate-400">
              No sections created for this strand yet.
            </div>
          </div>
        </div>

        <!-- 2-Year Curriculum Road Map by Semester -->
        <div class="space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
              <h3 class="text-base font-extrabold text-slate-900 flex items-center space-x-2">
                <BookOpen class="w-4 h-4 text-purple-700" />
                <span>Senior High School Curriculum Breakdown</span>
              </h3>
              <p class="text-[11px] text-slate-500">Core, Applied, and Specialized learning areas across all 4 semestral terms.</p>
            </div>

            <!-- Term Filter Pills -->
            <div class="flex items-center space-x-1 bg-slate-100 p-1 rounded-xl">
              <button 
                type="button" 
                @click="strandActiveTermTab = 'all'"
                :class="strandActiveTermTab === 'all' ? 'bg-white text-purple-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                class="px-2.5 py-1 rounded-lg text-[10px] transition"
              >
                All 4 Semesters
              </button>
              <button 
                type="button" 
                @click="strandActiveTermTab = 'g11_s1'"
                :class="strandActiveTermTab === 'g11_s1' ? 'bg-white text-purple-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                class="px-2.5 py-1 rounded-lg text-[10px] transition"
              >
                G11 1st Sem
              </button>
              <button 
                type="button" 
                @click="strandActiveTermTab = 'g11_s2'"
                :class="strandActiveTermTab === 'g11_s2' ? 'bg-white text-purple-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                class="px-2.5 py-1 rounded-lg text-[10px] transition"
              >
                G11 2nd Sem
              </button>
              <button 
                type="button" 
                @click="strandActiveTermTab = 'g12_s1'"
                :class="strandActiveTermTab === 'g12_s1' ? 'bg-white text-purple-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                class="px-2.5 py-1 rounded-lg text-[10px] transition"
              >
                G12 1st Sem
              </button>
              <button 
                type="button" 
                @click="strandActiveTermTab = 'g12_s2'"
                :class="strandActiveTermTab === 'g12_s2' ? 'bg-white text-purple-900 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                class="px-2.5 py-1 rounded-lg text-[10px] transition"
              >
                G12 2nd Sem
              </button>
            </div>
          </div>

          <!-- Term Cards List -->
          <div class="space-y-4">
            <div 
              v-for="term in visibleStrandTerms" 
              :key="term.id"
              class="rounded-2xl border border-slate-200 overflow-hidden"
            >
              <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center space-x-2">
                  <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                  <h4 class="font-extrabold text-slate-900 text-xs">{{ term.grade }} • {{ term.semester }}</h4>
                </div>
                <div class="flex items-center space-x-3 text-[11px] text-slate-500 font-semibold">
                  <span>{{ term.subjects.length }} Subjects</span>
                  <span>•</span>
                  <span>{{ term.totalUnits.toFixed(1) }} Academic Units</span>
                  <span>•</span>
                  <span>{{ term.totalHours.toFixed(1) }} Weekly Hours</span>
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                  <thead>
                    <tr class="bg-slate-50/60 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                      <th class="p-2.5 pl-4">Subject Code</th>
                      <th class="p-2.5">Subject Title</th>
                      <th class="p-2.5">Classification</th>
                      <th class="p-2.5">Prerequisite</th>
                      <th class="p-2.5 text-center">Hours / Units</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="sub in term.subjects" :key="sub.id" class="hover:bg-slate-50 transition">
                      <td class="p-2.5 pl-4 font-mono font-bold text-purple-700">{{ sub.code }}</td>
                      <td class="p-2.5 font-semibold text-slate-800">{{ sub.title }}</td>
                      <td class="p-2.5">
                        <span class="px-2 py-0.5 rounded text-[9px] font-bold" :class="getCategoryClass(sub.category)">
                          {{ sub.category }}
                        </span>
                      </td>
                      <td class="p-2.5 font-mono text-[11px] text-slate-500">
                        <span v-if="sub.prerequisite_code" class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 font-semibold">
                          {{ sub.prerequisite_code }}
                        </span>
                        <span v-else class="text-slate-400">None</span>
                      </td>
                      <td class="p-2.5 text-center font-mono font-bold text-slate-700">
                        {{ sub.lecture_hours }}h / {{ sub.units }}u
                      </td>
                    </tr>
                    <tr v-if="term.subjects.length === 0">
                      <td colspan="5" class="p-4 text-center text-slate-400">
                        No subjects catalogued for this term.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Overall 2-Year Program Statistics Footer -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
          <div class="flex items-center space-x-4 flex-wrap gap-y-1">
            <span class="text-slate-600">Total Program Subjects: <strong class="text-purple-900 font-mono">{{ strandOverallStats.totalSubjects }}</strong></span>
            <span>•</span>
            <span class="text-slate-600">Total Academic Units: <strong class="text-purple-900 font-mono">{{ strandOverallStats.totalUnits.toFixed(1) }} units</strong></span>
            <span>•</span>
            <span class="text-slate-600">Total Weekly Load: <strong class="text-purple-900 font-mono">{{ strandOverallStats.totalHours.toFixed(1) }} hours</strong></span>
          </div>

          <div class="flex items-center space-x-2">
            <button 
              type="button"
              @click="editStrand(selectedStrandForDetails); selectedStrandForDetails = null" 
              class="px-4 py-2 rounded-xl font-bold bg-slate-200 hover:bg-slate-300 text-slate-800 transition"
            >
              Edit Strand Information
            </button>
            <button 
              type="button"
              @click="selectedStrandForDetails = null" 
              class="px-5 py-2 rounded-xl font-bold bg-purple-600 hover:bg-purple-500 text-white shadow-md transition"
            >
              Close
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- SCHEDULE PERIOD MODAL -->
    <div v-if="showScheduleModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div>
            <h3 class="text-base font-extrabold text-slate-900">{{ scheduleForm.id ? 'Edit Scheduled Period' : 'Add Class Schedule Period' }}</h3>
            <p class="text-[11px] text-slate-500">Section: <strong>{{ activeSectionSchedule?.section?.name }}</strong></p>
          </div>
          <button @click="showScheduleModal = false" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
        </div>

        <!-- Conflict Error Alert -->
        <div v-if="scheduleError" class="p-3.5 rounded-2xl bg-rose-50 border border-rose-300 text-rose-800 text-xs flex items-start space-x-2">
          <AlertCircle class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
          <div class="flex-1 font-medium">{{ scheduleError }}</div>
        </div>

        <form @submit.prevent="saveScheduleItem" class="space-y-3">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Select Subject *</label>
            <select v-model="scheduleForm.subject_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white font-medium" required>
              <option value="">-- Choose Curriculum Subject --</option>
              <option v-for="sub in activeSectionSchedule?.available_subjects || []" :key="sub.id" :value="sub.id">
                [{{ sub.code }}] {{ sub.title }} ({{ sub.category }} • {{ sub.lecture_hours }} hrs)
              </option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Day(s) of Week *</label>
              <select v-model="scheduleForm.day_of_week" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white" required>
                <option value="Mon-Fri">Monday to Friday (Daily)</option>
                <option value="Mon-Wed-Fri">Mon-Wed-Fri (MWF)</option>
                <option value="Tue-Thu">Tuesday & Thursday (TTh)</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Term / Semester *</label>
              <select v-if="isCurrentSectionSHS" v-model="scheduleForm.semester" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white" required>
                <option value="1st Semester">1st Semester</option>
                <option value="2nd Semester">2nd Semester</option>
              </select>
              <div v-else class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-100 text-slate-600 font-medium">
                Full Year (Junior High)
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Time Start *</label>
              <input v-model="scheduleForm.time_start" type="time" class="w-full px-3 py-2 rounded-xl border border-slate-300" required />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Time End *</label>
              <input v-model="scheduleForm.time_end" type="time" class="w-full px-3 py-2 rounded-xl border border-slate-300" required />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Assigned Teacher</label>
              <select v-model="scheduleForm.teacher_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white">
                <option :value="null">-- None Assigned --</option>
                <option v-for="t in activeSectionSchedule?.teachers || []" :key="t.id" :value="t.id">
                  {{ t.last_name }}, {{ t.first_name }} (@{{ t.username }})
                </option>
              </select>
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Room / Laboratory</label>
              <input v-model="scheduleForm.room" type="text" placeholder="e.g. Science Lab 1 / Rm 201" class="w-full px-3 py-2 rounded-xl border border-slate-300" />
            </div>
          </div>

          <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <div>
              <button 
                v-if="scheduleForm.id" 
                type="button" 
                @click="handleModalDeleteSchedule()" 
                class="px-3 py-2 rounded-xl text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 font-bold transition flex items-center space-x-1 text-xs"
              >
                <Trash2 class="w-3.5 h-3.5" />
                <span>Remove Period</span>
              </button>
            </div>
            <div class="flex items-center space-x-2">
              <button type="button" @click="showScheduleModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-xs">
                Cancel
              </button>
              <button type="submit" :disabled="isSavingSchedule" class="px-5 py-2.5 rounded-xl font-bold bg-purple-600 hover:bg-purple-500 text-white shadow-md transition flex items-center space-x-1.5 text-xs">
                <span v-if="isSavingSchedule" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <span>{{ isSavingSchedule ? 'Checking Conflicts...' : 'Save Schedule Period' }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- EVENT MODAL -->
    <div v-if="showEventModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div>
            <h3 class="text-base font-extrabold text-slate-900">{{ eventForm.id ? 'Edit School Event' : 'Create School Event / Milestone' }}</h3>
            <p class="text-[11px] text-slate-500">Publish academic deadlines, examinations, and institutional events.</p>
          </div>
          <button @click="showEventModal = false" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
        </div>

        <form @submit.prevent="saveSchoolEvent" class="space-y-3">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Event Title *</label>
            <input v-model="eventForm.title" type="text" placeholder="e.g. 1st Quarter Midterm Examinations" class="w-full px-3 py-2 rounded-xl border border-slate-300" required />
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Description</label>
            <textarea v-model="eventForm.description" rows="2" placeholder="Event details and guidelines..." class="w-full px-3 py-2 rounded-xl border border-slate-300"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Category *</label>
              <select v-model="eventForm.event_category" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white" required>
                <option value="Academic">Academic Milestone</option>
                <option value="Examination">Examination Period</option>
                <option value="Holiday">Holiday / Suspension</option>
                <option value="Activity">School Activity / Intramurals</option>
                <option value="Administrative">Administrative / PTA</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Target Audience</label>
              <select v-model="eventForm.target_audience" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white" required>
                <option value="All">Everyone (Public & School)</option>
                <option value="Students">Students & Parents</option>
                <option value="Faculty">Teachers & Staff Only</option>
                <option value="Applicants">Admission Applicants</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Start Date *</label>
              <input v-model="eventForm.start_date" type="date" class="w-full px-3 py-2 rounded-xl border border-slate-300" required />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">End Date *</label>
              <input v-model="eventForm.end_date" type="date" class="w-full px-3 py-2 rounded-xl border border-slate-300" required />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Start Time (Optional)</label>
              <input v-model="eventForm.start_time" type="time" class="w-full px-3 py-2 rounded-xl border border-slate-300" />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">End Time (Optional)</label>
              <input v-model="eventForm.end_time" type="time" class="w-full px-3 py-2 rounded-xl border border-slate-300" />
            </div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 mb-1">Campus Location / Venue</label>
            <input v-model="eventForm.location" type="text" placeholder="e.g. School Auditorium / Gymnasium" class="w-full px-3 py-2 rounded-xl border border-slate-300" />
          </div>

          <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100">
            <button type="button" @click="showEventModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold">
              Cancel
            </button>
            <button type="submit" :disabled="isSavingEvent" class="px-5 py-2.5 rounded-xl font-bold bg-purple-600 hover:bg-purple-500 text-white shadow-md transition flex items-center space-x-1.5">
              <span v-if="isSavingEvent" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              <span>{{ isSavingEvent ? 'Publishing...' : 'Save & Broadcast Event' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- CURRICULUM DECLARATION & LOCK CONFIRMATION POPUP MODAL -->
    <div v-if="curriculumLockModal.isOpen" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 text-xs space-y-4 animate-in fade-in zoom-in duration-150">
        
        <!-- Header with Dynamic Icon and Alert Styling -->
        <div class="flex items-start space-x-3.5 border-b border-slate-100 pb-4">
          <div 
            class="p-3 rounded-2xl shrink-0"
            :class="curriculumData.curriculum_locked ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200'"
          >
            <Unlock v-if="curriculumData.curriculum_locked" class="w-6 h-6 text-amber-700" />
            <Lock v-else class="w-6 h-6 text-emerald-700" />
          </div>
          <div>
            <div 
              class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase font-mono tracking-wider mb-1"
              :class="curriculumData.curriculum_locked ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-emerald-100 text-emerald-900 border border-emerald-200'"
            >
              <span>{{ curriculumData.curriculum_locked ? 'Unlock for Drafting' : 'Official DepEd Academic Freeze' }}</span>
            </div>
            <h3 class="text-base font-extrabold text-slate-900 leading-snug">
              {{ curriculumData.curriculum_locked ? 'Switch Curriculum to Draft / Setup Mode?' : 'Officially Declare & Lock SY Curriculum?' }}
            </h3>
            <p class="text-[11px] text-slate-500 mt-0.5">
              School Year: <strong>{{ curriculumData.active_school_year?.name || 'School Year 2026-2027' }}</strong>
            </p>
          </div>
        </div>

        <!-- Explanatory Box -->
        <div 
          class="p-4 rounded-2xl border text-xs space-y-2 leading-relaxed"
          :class="curriculumData.curriculum_locked ? 'bg-amber-50/80 border-amber-200 text-amber-950' : 'bg-emerald-50/80 border-emerald-200 text-emerald-950'"
        >
          <div class="font-bold text-slate-900 flex items-center space-x-1.5">
            <AlertCircle class="w-4 h-4" :class="curriculumData.curriculum_locked ? 'text-amber-700' : 'text-emerald-700'" />
            <span>{{ curriculumData.curriculum_locked ? 'Important Safeguard Information:' : 'Curriculum Lock Consequences:' }}</span>
          </div>

          <template v-if="curriculumData.curriculum_locked">
            <ul class="list-disc list-inside space-y-1.5 text-[11px] text-slate-700">
              <li>Re-enables adding, editing, and deleting learning area subjects and strands.</li>
              <li><strong class="text-amber-900">Caution:</strong> Do not alter course codes or units if students are already actively enrolled or graded for this school year.</li>
              <li>Once your modifications are done, re-lock the curriculum to freeze student transcripts.</li>
            </ul>
          </template>

          <template v-else>
            <ul class="list-disc list-inside space-y-1.5 text-[11px] text-slate-700">
              <li>Freezes all <strong>{{ curriculumData.subjects?.length || 119 }} subjects</strong> and <strong>{{ curriculumData.strands?.length || 8 }} strands</strong> from accidental modifications or deletion.</li>
              <li>Safeguards ongoing class schedule timetables, teacher loads, and official Form 137 / Form 138 transcripts.</li>
              <li>Per DepEd policy, mid-year curriculum revisions will be staged for the subsequent school year.</li>
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
            @click="executeCurriculumLockToggle()" 
            :disabled="curriculumLockModal.isProcessing"
            class="px-5 py-2.5 rounded-xl font-bold text-white shadow-md transition flex items-center space-x-2 text-xs"
            :class="curriculumData.curriculum_locked ? 'bg-amber-600 hover:bg-amber-500' : 'bg-emerald-600 hover:bg-emerald-500'"
          >
            <span v-if="curriculumLockModal.isProcessing" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <Unlock v-else-if="curriculumData.curriculum_locked" class="w-3.5 h-3.5 text-white" />
            <Lock v-else class="w-3.5 h-3.5 text-white" />
            <span>{{ curriculumLockModal.isProcessing ? 'Updating Status...' : (curriculumData.curriculum_locked ? 'Confirm Switch to Draft Mode' : 'Confirm Declare & Lock') }}</span>
          </button>
        </div>

      </div>
    </div>

    <!-- REMOVE SCHEDULE PERIOD CONFIRMATION POPUP MODAL -->
    <div v-if="deleteScheduleModal.isOpen" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 text-xs space-y-4 animate-in fade-in zoom-in duration-150">
        
        <div class="flex items-start space-x-3.5 border-b border-slate-100 pb-3.5">
          <div class="p-3 rounded-2xl bg-rose-100 text-rose-700 border border-rose-200 shrink-0">
            <Trash2 class="w-6 h-6 text-rose-600" />
          </div>
          <div>
            <div class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase font-mono tracking-wider bg-rose-100 text-rose-900 border border-rose-200 mb-1">
              <span>Timetable Period Removal</span>
            </div>
            <h3 class="text-base font-extrabold text-slate-900 leading-snug">
              Remove Subject Period from Schedule?
            </h3>
            <p class="text-[11px] text-slate-500 mt-0.5">
              Section: <strong>{{ activeSectionSchedule?.section?.name || 'Current Section' }}</strong>
            </p>
          </div>
        </div>

        <!-- Details Summary Card -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
          <div class="flex items-center justify-between pb-2 border-b border-slate-200/70">
            <span class="text-slate-500 font-medium">Subject:</span>
            <span class="font-bold text-slate-900 font-mono">[{{ deleteScheduleModal.schedule?.subject_code }}] {{ deleteScheduleModal.schedule?.subject_title }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-slate-500 font-medium">Day & Time:</span>
            <span class="font-bold text-purple-800">{{ deleteScheduleModal.schedule?.day_of_week }} • {{ formatTime(deleteScheduleModal.schedule?.time_start) }} - {{ formatTime(deleteScheduleModal.schedule?.time_end) }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-slate-500 font-medium">Faculty:</span>
            <span class="font-bold text-slate-800">{{ deleteScheduleModal.schedule?.teacher_first ? deleteScheduleModal.schedule.teacher_first + ' ' + deleteScheduleModal.schedule.teacher_last : 'Unassigned' }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-slate-500 font-medium">Room / Venue:</span>
            <span class="font-bold text-slate-800">{{ deleteScheduleModal.schedule?.room || 'Room Unassigned' }}</span>
          </div>
        </div>

        <!-- Safe Note -->
        <div class="p-3 bg-rose-50/70 rounded-xl border border-rose-200 text-[11px] text-rose-900 flex items-start space-x-2">
          <AlertCircle class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
          <span>This will remove this specific class time slot from the section's weekly schedule. It does <strong>NOT</strong> delete the subject from the master curriculum.</span>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-2.5 pt-2 border-t border-slate-100">
          <button 
            type="button" 
            @click="deleteScheduleModal.isOpen = false" 
            :disabled="deleteScheduleModal.isDeleting"
            class="px-4 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs"
          >
            Cancel (Keep in Schedule)
          </button>
          
          <button 
            type="button" 
            @click="executeDeleteSchedule()" 
            :disabled="deleteScheduleModal.isDeleting"
            class="px-5 py-2.5 rounded-xl font-bold bg-rose-600 hover:bg-rose-500 text-white shadow-md transition flex items-center space-x-2 text-xs"
          >
            <span v-if="deleteScheduleModal.isDeleting" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <Trash2 v-else class="w-3.5 h-3.5 text-white" />
            <span>{{ deleteScheduleModal.isDeleting ? 'Removing...' : 'Confirm Remove Period' }}</span>
          </button>
        </div>

      </div>
    </div>

    <!-- DELETE SCHOOL EVENT CONFIRMATION POPUP MODAL -->
    <div v-if="deleteEventModal.isOpen" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 text-xs space-y-4 animate-in fade-in zoom-in duration-150">
        
        <div class="flex items-start space-x-3.5 border-b border-slate-100 pb-3.5">
          <div class="p-3 rounded-2xl bg-rose-100 text-rose-700 border border-rose-200 shrink-0">
            <Trash2 class="w-6 h-6 text-rose-600" />
          </div>
          <div>
            <div class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase font-mono tracking-wider bg-rose-100 text-rose-900 border border-rose-200 mb-1">
              <span>Event Deletion</span>
            </div>
            <h3 class="text-base font-extrabold text-slate-900 leading-snug">
              Delete School Event & Broadcast?
            </h3>
            <p class="text-[11px] text-slate-500 mt-0.5">
              Event: <strong>{{ deleteEventModal.event?.title }}</strong>
            </p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-slate-500 font-medium">Category:</span>
            <span class="font-bold text-slate-900">{{ deleteEventModal.event?.event_category }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-slate-500 font-medium">Date:</span>
            <span class="font-bold text-purple-800">{{ formatEventDate(deleteEventModal.event?.start_date) }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-slate-500 font-medium">Venue:</span>
            <span class="font-bold text-slate-800">{{ deleteEventModal.event?.location || 'Campus Grounds' }}</span>
          </div>
        </div>

        <div class="flex items-center justify-end space-x-2.5 pt-2 border-t border-slate-100">
          <button 
            type="button" 
            @click="deleteEventModal.isOpen = false" 
            :disabled="deleteEventModal.isDeleting"
            class="px-4 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs"
          >
            Cancel
          </button>
          
          <button 
            type="button" 
            @click="executeDeleteEvent()" 
            :disabled="deleteEventModal.isDeleting"
            class="px-5 py-2.5 rounded-xl font-bold bg-rose-600 hover:bg-rose-500 text-white shadow-md transition flex items-center space-x-2 text-xs"
          >
            <span v-if="deleteEventModal.isDeleting" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <Trash2 v-else class="w-3.5 h-3.5 text-white" />
            <span>{{ deleteEventModal.isDeleting ? 'Deleting...' : 'Delete Event' }}</span>
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Plus, Users, ArrowRightLeft, AlertCircle, CheckCircle, Search, Trash2, BookOpen, Layers, Clock, Calendar, MapPin, Sparkles, Pencil, User, ChevronDown, Check, Lock, Unlock } from 'lucide-vue-next';
import api from '../../services/api';

const activeTab = ref('curriculum');
const curriculumData = ref({ subjects: [], grade_levels: [], strands: [], tracks: [] });
const sectionsData = ref({ sections: [], teachers: [] });
const calendarData = ref({ events: [], upcoming: [] });
const successMessage = ref('');

// Scheduler state
const selectedScheduleSectionId = ref(1);
const selectedScheduleSemester = ref('1st Semester');
const activeSectionSchedule = ref({ section: null, schedules: [], available_subjects: [], teachers: [] });
const isLoadingSchedule = ref(false);
const showScheduleModal = ref(false);
const isSavingSchedule = ref(false);
const scheduleError = ref('');

// Searchable Section Selector state
const isSectionDropdownOpen = ref(false);
const scheduleSectionSearch = ref('');
const scheduleGradeFilter = ref('');
const scheduleForm = ref({
  id: null,
  section_id: 1,
  subject_id: '',
  semester: '1st Semester',
  teacher_id: null,
  day_of_week: 'Mon-Wed-Fri',
  time_start: '08:00',
  time_end: '09:30',
  room: ''
});

// Event calendar state
const eventFilter = ref({ category: '', audience: '' });
const showEventModal = ref(false);
const isSavingEvent = ref(false);
const eventForm = ref({
  id: null,
  title: '',
  description: '',
  event_category: 'Academic',
  start_date: '',
  end_date: '',
  start_time: '',
  end_time: '',
  location: '',
  target_audience: 'All',
  is_published: 1
});

const selectedStrandForDetails = ref(null);
const strandActiveTermTab = ref('all');

const currFilter = ref({
  search: '',
  grade_level_id: '',
  strand_id: '',
  category: '',
  semester: ''
});

const sectionFilter = ref({
  search: '',
  grade_level_id: '',
  strand_id: '',
  sortBy: 'grade_asc'
});

const selectedRosterSection = ref(null);
const rosterStudents = ref([]);
const isLoadingRoster = ref(false);

const transferModal = ref({
  isOpen: false,
  student: null,
  targetSectionId: '',
  reason: '',
  isTransferring: false,
  error: ''
});

const showSubjectModal = ref(false);
const subjectForm = ref({
  id: null,
  code: '',
  title: '',
  description: '',
  category: 'JHS Core',
  grade_level_id: 1,
  strand_id: null,
  semester: 'Full Year',
  lecture_hours: 4,
  lab_hours: 0,
  units: 1,
  prerequisite_id: null
});

const deleteModal = ref({
  isOpen: false,
  subject: null,
  isDeleting: false
});

const showStrandModal = ref(false);
const strandForm = ref({
  id: null,
  track_id: 1,
  code: '',
  name: '',
  description: '',
  status: 'Active'
});

const strandFilter = ref({
  search: '',
  track_id: '',
  status: 'Active'
});

const removeStrandModal = ref({
  isOpen: false,
  strand: null,
  isRemoving: false
});

const deleteStrandModal = ref({
  isOpen: false,
  strand: null,
  isDeleting: false
});

const strandStats = computed(() => {
  const all = curriculumData.value.strands || [];
  const activeCount = all.filter(s => (s.status || 'Active') !== 'Archived').length;
  const archivedCount = all.filter(s => s.status === 'Archived').length;
  return {
    totalCount: all.length,
    activeCount,
    archivedCount
  };
});

const showSectionModal = ref(false);
const sectionForm = ref({
  id: null,
  name: '',
  grade_level_id: 1,
  strand_id: null,
  max_capacity: 45,
  room: '',
  adviser_id: null
});

const getCategoryClass = (cat) => {
  if (cat === 'SHS Specialized') return 'bg-purple-100 text-purple-800';
  if (cat === 'SHS Applied') return 'bg-blue-100 text-blue-800';
  if (cat === 'SHS Core') return 'bg-teal-100 text-teal-800';
  return 'bg-slate-100 text-slate-800';
};

const getTrackBadgeClass = (code) => {
  if (code === 'TVL') return 'bg-blue-100 text-blue-800';
  if (code === 'ARTS') return 'bg-pink-100 text-pink-800';
  if (code === 'SPORTS') return 'bg-emerald-100 text-emerald-800';
  return 'bg-purple-100 text-purple-800';
};

const getStrandStatusBadgeClass = (status) => {
  if (status === 'Active') return 'bg-emerald-100 text-emerald-800';
  if (status === 'Deactivated') return 'bg-amber-100 text-amber-800';
  if (status === 'Archived') return 'bg-slate-200 text-slate-700';
  return 'bg-emerald-100 text-emerald-800';
};

const getStrandCardBorderClass = (status) => {
  if (status === 'Archived') return 'border-slate-300 bg-slate-100/60 opacity-80';
  if (status === 'Deactivated') return 'border-amber-200 bg-amber-50/30';
  return 'border-slate-200 bg-white hover:border-purple-300 shadow-sm';
};

const filteredStrands = computed(() => {
  if (!curriculumData.value.strands) return [];
  return curriculumData.value.strands.filter(st => {
    if (strandFilter.value.search) {
      const q = strandFilter.value.search.toLowerCase();
      const codeMatch = st.code?.toLowerCase().includes(q);
      const nameMatch = st.name?.toLowerCase().includes(q);
      if (!codeMatch && !nameMatch) return false;
    }
    if (strandFilter.value.track_id && st.track_id != strandFilter.value.track_id) {
      return false;
    }
    if (strandFilter.value.status === 'Active' && st.status === 'Archived') {
      return false;
    }
    if (strandFilter.value.status === 'Archived' && st.status !== 'Archived') {
      return false;
    }
    return true;
  });
});

const filteredSubjects = computed(() => {
  if (!curriculumData.value.subjects) return [];
  return curriculumData.value.subjects.filter(sub => {
    if (currFilter.value.search) {
      const q = currFilter.value.search.toLowerCase();
      const codeMatch = sub.code?.toLowerCase().includes(q);
      const titleMatch = sub.title?.toLowerCase().includes(q);
      if (!codeMatch && !titleMatch) return false;
    }
    if (currFilter.value.grade_level_id && sub.grade_level_id != currFilter.value.grade_level_id) {
      return false;
    }
    if (currFilter.value.strand_id === 'core' && sub.strand_id !== null) {
      return false;
    }
    if (currFilter.value.strand_id && currFilter.value.strand_id !== 'core' && sub.strand_id != currFilter.value.strand_id) {
      return false;
    }
    if (currFilter.value.category && sub.category !== currFilter.value.category) {
      return false;
    }
    if (currFilter.value.semester && sub.semester !== currFilter.value.semester) {
      return false;
    }
    return true;
  });
});

const curriculumStats = computed(() => {
  const subs = filteredSubjects.value;
  const totalUnits = subs.reduce((acc, s) => acc + Number(s.units || 0), 0);
  const totalHours = subs.reduce((acc, s) => acc + Number(s.lecture_hours || 0) + Number(s.lab_hours || 0), 0);
  return { totalUnits, totalHours };
});

const eligiblePrerequisites = computed(() => {
  if (!curriculumData.value.subjects) return [];
  return curriculumData.value.subjects.filter(s => s.id !== subjectForm.value.id);
});

const filteredSections = computed(() => {
  if (!sectionsData.value.sections) return [];

  let list = sectionsData.value.sections.filter(sec => {
    // 1. Search text filter (name, room, adviser)
    if (sectionFilter.value.search) {
      const q = sectionFilter.value.search.toLowerCase();
      const nameMatch = sec.name?.toLowerCase().includes(q);
      const roomMatch = sec.room?.toLowerCase().includes(q);
      const adviserMatch = `${sec.adviser_first || ''} ${sec.adviser_last || ''} ${sec.adviser_username || ''}`.toLowerCase().includes(q);
      if (!nameMatch && !roomMatch && !adviserMatch) return false;
    }

    // 2. Grade Level filter
    if (sectionFilter.value.grade_level_id && sec.grade_level_id != sectionFilter.value.grade_level_id) {
      return false;
    }

    // 3. Strand filter
    if (sectionFilter.value.strand_id === 'jhs' && sec.strand_id !== null) {
      return false;
    }
    if (sectionFilter.value.strand_id && sectionFilter.value.strand_id !== 'jhs' && sec.strand_id != sectionFilter.value.strand_id) {
      return false;
    }

    return true;
  });

  // 4. Sorting logic
  return list.sort((a, b) => {
    switch (sectionFilter.value.sortBy) {
      case 'grade_asc':
        if (a.grade_level_id !== b.grade_level_id) return a.grade_level_id - b.grade_level_id;
        return a.name.localeCompare(b.name, undefined, { numeric: true });
      case 'grade_desc':
        if (a.grade_level_id !== b.grade_level_id) return b.grade_level_id - a.grade_level_id;
        return a.name.localeCompare(b.name, undefined, { numeric: true });
      case 'name_asc':
        return a.name.localeCompare(b.name, undefined, { numeric: true });
      case 'name_desc':
        return b.name.localeCompare(a.name, undefined, { numeric: true });
      case 'enrolled_desc':
        return (b.current_enrolled || 0) - (a.current_enrolled || 0);
      case 'slots_desc':
        const slotsA = (a.max_capacity || 45) - (a.current_enrolled || 0);
        const slotsB = (b.max_capacity || 45) - (b.current_enrolled || 0);
        return slotsB - slotsA;
      case 'capacity_desc':
        return (b.max_capacity || 0) - (a.max_capacity || 0);
      default:
        return a.grade_level_id - b.grade_level_id;
    }
  });
});

const sectionStats = computed(() => {
  const allSecs = sectionsData.value.sections || [];
  const totalSections = allSecs.length;
  const totalEnrolled = allSecs.reduce((acc, s) => acc + Number(s.current_enrolled || 0), 0);
  const totalCapacity = allSecs.reduce((acc, s) => acc + Number(s.max_capacity || 45), 0);
  return { totalSections, totalEnrolled, totalCapacity };
});

const resetSectionFilters = () => {
  sectionFilter.value = {
    search: '',
    grade_level_id: '',
    strand_id: '',
    sortBy: 'grade_asc'
  };
};

const openStrandDetailsModal = (strand) => {
  selectedStrandForDetails.value = strand;
  strandActiveTermTab.value = 'all';
};

const strandSections = computed(() => {
  if (!selectedStrandForDetails.value || !sectionsData.value.sections) return [];
  return sectionsData.value.sections.filter(s => s.strand_id === selectedStrandForDetails.value.id);
});

const getStrandTermSubjects = (gradeLevelId, semester) => {
  if (!selectedStrandForDetails.value || !curriculumData.value.subjects) return [];
  const strandId = selectedStrandForDetails.value.id;

  return curriculumData.value.subjects.filter(sub => {
    if (sub.grade_level_id !== gradeLevelId) return false;
    if (sub.semester !== semester) return false;
    // Core (strand_id null), Applied (strand_id null), and Specialized matching this strand
    return sub.strand_id === null || sub.strand_id === strandId;
  });
};

const strandCurriculumTerms = computed(() => {
  if (!selectedStrandForDetails.value) return [];

  const terms = [
    {
      id: 'g11_s1',
      grade: 'Grade 11',
      semester: '1st Semester',
      gradeLevelId: 5,
      subjects: getStrandTermSubjects(5, '1st Semester')
    },
    {
      id: 'g11_s2',
      grade: 'Grade 11',
      semester: '2nd Semester',
      gradeLevelId: 5,
      subjects: getStrandTermSubjects(5, '2nd Semester')
    },
    {
      id: 'g12_s1',
      grade: 'Grade 12',
      semester: '1st Semester',
      gradeLevelId: 6,
      subjects: getStrandTermSubjects(6, '1st Semester')
    },
    {
      id: 'g12_s2',
      grade: 'Grade 12',
      semester: '2nd Semester',
      gradeLevelId: 6,
      subjects: getStrandTermSubjects(6, '2nd Semester')
    }
  ];

  return terms.map(term => {
    const totalUnits = term.subjects.reduce((acc, s) => acc + Number(s.units || 0), 0);
    const totalHours = term.subjects.reduce((acc, s) => acc + Number(s.lecture_hours || 0) + Number(s.lab_hours || 0), 0);
    return { ...term, totalUnits, totalHours };
  });
});

const visibleStrandTerms = computed(() => {
  if (strandActiveTermTab.value === 'all') {
    return strandCurriculumTerms.value;
  }
  return strandCurriculumTerms.value.filter(t => t.id === strandActiveTermTab.value);
});

const strandOverallStats = computed(() => {
  const allTerms = strandCurriculumTerms.value;
  let totalSubjects = 0;
  let totalUnits = 0;
  let totalHours = 0;

  allTerms.forEach(t => {
    totalSubjects += t.subjects.length;
    totalUnits += t.totalUnits;
    totalHours += t.totalHours;
  });

  return { totalSubjects, totalUnits, totalHours };
});

const loadData = async () => {
  try {
    const [currRes, secRes] = await Promise.all([
      api.getCurriculum(),
      api.getSections()
    ]);
    curriculumData.value = currRes.data;
    sectionsData.value = secRes.data;
  } catch (err) {
    console.error('Failed to load coordinator data:', err);
  }
};

const openSubjectModal = (sub = null) => {
  if (sub) {
    subjectForm.value = {
      id: sub.id,
      code: sub.code,
      title: sub.title,
      description: sub.description || '',
      category: sub.category,
      grade_level_id: sub.grade_level_id,
      strand_id: sub.strand_id || null,
      semester: sub.semester || 'Full Year',
      lecture_hours: Number(sub.lecture_hours || 4),
      lab_hours: Number(sub.lab_hours || 0),
      units: Number(sub.units || 1),
      prerequisite_id: sub.prerequisite_id ? Number(sub.prerequisite_id) : null
    };
  } else {
    subjectForm.value = {
      id: null,
      code: '',
      title: '',
      description: '',
      category: 'JHS Core',
      grade_level_id: 1,
      strand_id: null,
      semester: 'Full Year',
      lecture_hours: 4,
      lab_hours: 0,
      units: 1,
      prerequisite_id: null
    };
  }
  showSubjectModal.value = true;
};

const saveSubject = async () => {
  try {
    await api.saveSubject(subjectForm.value);
    successMessage.value = `Subject ${subjectForm.value.code} saved to curriculum successfully!`;
    showSubjectModal.value = false;
    await loadData();
  } catch (err) {
    alert(err.message || 'Failed to save subject.');
  }
};

const curriculumLockModal = ref({
  isOpen: false,
  isProcessing: false
});

const toggleCurriculumDeclaration = () => {
  curriculumLockModal.value = {
    isOpen: true,
    isProcessing: false
  };
};

const executeCurriculumLockToggle = async () => {
  curriculumLockModal.value.isProcessing = true;
  try {
    const res = await api.toggleCurriculumLock();
    successMessage.value = res.message || 'Curriculum lock status updated successfully!';
    curriculumLockModal.value.isOpen = false;
    await loadData();
  } catch (err) {
    alert(err.message || 'Failed to update curriculum lock status.');
  } finally {
    curriculumLockModal.value.isProcessing = false;
  }
};

const confirmDeleteSubject = (sub) => {
  deleteModal.value = {
    isOpen: true,
    subject: sub,
    isDeleting: false
  };
};

const executeDeleteSubject = async () => {
  if (!deleteModal.value.subject) return;
  deleteModal.value.isDeleting = true;
  try {
    const res = await api.deleteSubject(deleteModal.value.subject.id);
    successMessage.value = res.message || 'Subject deleted from curriculum.';
    deleteModal.value.isOpen = false;
    await loadData();
  } catch (err) {
    alert(err.message || 'Failed to delete subject.');
  } finally {
    deleteModal.value.isDeleting = false;
  }
};

const openStrandModal = (st = null) => {
  if (curriculumData.value.curriculum_locked) {
    if (st) {
      openStrandDetailsModal(st);
    } else {
      alert('The school year curriculum is officially declared and locked. New strands cannot be created while in lock mode.');
    }
    return;
  }

  if (st) {
    strandForm.value = {
      id: st.id,
      track_id: st.track_id,
      code: st.code,
      name: st.name,
      description: st.description || '',
      status: st.status || 'Active'
    };
  } else {
    resetStrandForm();
  }
  showStrandModal.value = true;
};

const resetStrandForm = () => {
  strandForm.value = {
    id: null,
    track_id: curriculumData.value.tracks?.[0]?.id || 1,
    code: '',
    name: '',
    description: '',
    status: 'Active'
  };
};

const editStrand = (st) => {
  openStrandModal(st);
};

const saveStrand = async () => {
  try {
    await api.saveStrand(strandForm.value);
    successMessage.value = `Strand ${strandForm.value.code} saved successfully!`;
    showStrandModal.value = false;
    resetStrandForm();
    await loadData();
  } catch (err) {
    alert(err.message || 'Failed to save strand.');
  }
};

const updateStrandStatus = async (st, newStatus) => {
  try {
    await api.toggleStrandStatus({ id: st.id, status: newStatus });
    successMessage.value = `Strand ${st.code} is now ${newStatus}.`;
    await loadData();
  } catch (err) {
    alert(err.message || 'Failed to update strand status.');
  }
};

const openRemoveStrandModal = (st) => {
  removeStrandModal.value = {
    isOpen: true,
    strand: st,
    isRemoving: false
  };
};

const executeRemoveStrand = async () => {
  if (!removeStrandModal.value.strand) return;
  const st = removeStrandModal.value.strand;
  removeStrandModal.value.isRemoving = true;
  try {
    await api.toggleStrandStatus({ id: st.id, status: 'Archived' });
    successMessage.value = `Strand ${st.code} was removed from active offerings and moved to Archived Strands.`;
    removeStrandModal.value.isOpen = false;
    await loadData();
  } catch (err) {
    alert(err.message || 'Failed to remove strand.');
  } finally {
    removeStrandModal.value.isRemoving = false;
  }
};

const confirmDeleteStrand = (st) => {
  deleteStrandModal.value = {
    isOpen: true,
    strand: st,
    isDeleting: false
  };
};

const executeDeleteStrand = async () => {
  if (!deleteStrandModal.value.strand) return;
  const st = deleteStrandModal.value.strand;
  deleteStrandModal.value.isDeleting = true;
  try {
    const res = await api.deleteStrand(st.id);
    successMessage.value = res.message || `Strand ${st.code} deleted successfully.`;
    deleteStrandModal.value.isOpen = false;
    await loadData();
  } catch (err) {
    alert(err.message || 'Failed to delete strand.');
  } finally {
    deleteStrandModal.value.isDeleting = false;
  }
};

const openSectionModal = (sec = null) => {
  if (sec) {
    sectionForm.value = { ...sec };
  } else {
    sectionForm.value = {
      id: null,
      name: '',
      grade_level_id: 1,
      strand_id: null,
      max_capacity: 45,
      room: '',
      adviser_id: null
    };
  }
  showSectionModal.value = true;
};

const saveSection = async () => {
  try {
    await api.saveSection(sectionForm.value);
    successMessage.value = 'Section saved successfully!';
    showSectionModal.value = false;
    await loadData();
  } catch (err) {
    alert(err.message || 'Failed to save section.');
  }
};

const openRosterModal = async (sec) => {
  selectedRosterSection.value = sec;
  isLoadingRoster.value = true;
  rosterStudents.value = [];
  try {
    const res = await api.getSectionStudents(sec.id);
    rosterStudents.value = res.data;
  } catch (err) {
    console.error('Failed to load section students:', err);
  } finally {
    isLoadingRoster.value = false;
  }
};

const eligibleSections = computed(() => {
  if (!transferModal.value.student || !sectionsData.value.sections) return [];
  const stud = transferModal.value.student;
  return sectionsData.value.sections.filter(sec => {
    if (sec.id === (stud.section_id || selectedRosterSection.value?.id)) return false;
    if (selectedRosterSection.value && sec.grade_level_id !== selectedRosterSection.value.grade_level_id) return false;
    if (selectedRosterSection.value && selectedRosterSection.value.strand_id && sec.strand_id !== selectedRosterSection.value.strand_id) return false;
    return true;
  });
});

const openTransferModal = (stud) => {
  transferModal.value = {
    isOpen: true,
    student: {
      ...stud,
      section_id: selectedRosterSection.value?.id,
      section_name: stud.section_name || selectedRosterSection.value?.name
    },
    targetSectionId: '',
    reason: '',
    isTransferring: false,
    error: ''
  };
};

const closeTransferModal = () => {
  transferModal.value.isOpen = false;
  transferModal.value.student = null;
  transferModal.value.error = '';
};

const submitSectionTransfer = async () => {
  if (!transferModal.value.targetSectionId) return;
  transferModal.value.isTransferring = true;
  transferModal.value.error = '';
  try {
    await api.transferStudentSection({
      enrollment_id: transferModal.value.student.enrollment_id,
      student_id: transferModal.value.student.user_id,
      target_section_id: transferModal.value.targetSectionId,
      reason: transferModal.value.reason || 'Coordinator Section Adjustment'
    });
    
    successMessage.value = `Student ${transferModal.value.student.last_name} transferred successfully!`;
    closeTransferModal();
    
    await loadData();
    if (selectedRosterSection.value) {
      const updatedSec = sectionsData.value.sections.find(s => s.id === selectedRosterSection.value.id);
      if (updatedSec) selectedRosterSection.value = updatedSec;
      await openRosterModal(selectedRosterSection.value);
    }
  } catch (err) {
    transferModal.value.error = err.message || 'Failed to transfer section.';
  } finally {
    transferModal.value.isTransferring = false;
  }
};

// --- SCHEDULER & TIMETABLE METHODS ---
const currentSelectedSection = computed(() => {
  if (!sectionsData.value.sections) return null;
  return sectionsData.value.sections.find(s => s.id === selectedScheduleSectionId.value) || null;
});

const isCurrentSectionSHS = computed(() => {
  return currentSelectedSection.value && Number(currentSelectedSection.value.grade_level_id) >= 5;
});

const filteredScheduleSections = computed(() => {
  if (!sectionsData.value.sections) return [];
  return sectionsData.value.sections.filter(sec => {
    // 1. Grade filter
    if (scheduleGradeFilter.value === 'jhs' && Number(sec.grade_level_id) > 4) return false;
    if (scheduleGradeFilter.value === 'g11' && Number(sec.grade_level_id) !== 5) return false;
    if (scheduleGradeFilter.value === 'g12' && Number(sec.grade_level_id) !== 6) return false;

    // 2. Search query
    if (scheduleSectionSearch.value) {
      const q = scheduleSectionSearch.value.toLowerCase().trim();
      const matchName = sec.name?.toLowerCase().includes(q);
      const matchGrade = sec.grade_level_name?.toLowerCase().includes(q);
      const matchStrand = sec.strand_code?.toLowerCase().includes(q) || sec.strand_name?.toLowerCase().includes(q);
      const matchRoom = sec.room?.toLowerCase().includes(q);
      if (!matchName && !matchGrade && !matchStrand && !matchRoom) return false;
    }

    return true;
  });
});

const selectScheduleSection = async (sec) => {
  selectedScheduleSectionId.value = sec.id;
  isSectionDropdownOpen.value = false;
  scheduleSectionSearch.value = '';

  if (Number(sec.grade_level_id) <= 4) {
    selectedScheduleSemester.value = 'Full Year';
  } else {
    if (selectedScheduleSemester.value === 'Full Year' || !selectedScheduleSemester.value) {
      selectedScheduleSemester.value = '1st Semester';
    }
  }
  await loadSectionScheduleData();
};

const switchToSchedulesTab = async () => {
  activeTab.value = 'schedules';
  if (sectionsData.value.sections?.length > 0 && !selectedScheduleSectionId.value) {
    selectedScheduleSectionId.value = sectionsData.value.sections[0].id;
  }
  const sec = currentSelectedSection.value;
  if (sec) {
    if (Number(sec.grade_level_id) <= 4) {
      selectedScheduleSemester.value = 'Full Year';
    } else if (selectedScheduleSemester.value === 'Full Year' || !selectedScheduleSemester.value) {
      selectedScheduleSemester.value = '1st Semester';
    }
  }
  await loadSectionScheduleData();
};

const loadSectionScheduleData = async () => {
  if (!selectedScheduleSectionId.value) return;
  isLoadingSchedule.value = true;
  try {
    const sem = isCurrentSectionSHS.value ? selectedScheduleSemester.value : 'Full Year';
    const res = await api.getSectionSchedule(selectedScheduleSectionId.value, sem);
    activeSectionSchedule.value = res.data;
  } catch (err) {
    console.error('Failed to load section schedules:', err);
  } finally {
    isLoadingSchedule.value = false;
  }
};

const totalScheduledHours = computed(() => {
  if (!activeSectionSchedule.value?.schedules) return 0;
  return activeSectionSchedule.value.schedules.reduce((acc, s) => {
    return acc + Number(s.lecture_hours || 0) + Number(s.lab_hours || 0);
  }, 0);
});

const getSchedulesForDay = (day) => {
  if (!activeSectionSchedule.value?.schedules) return [];
  return activeSectionSchedule.value.schedules.filter(s => {
    if (s.day_of_week === day) return true;
    if (s.day_of_week === 'Mon-Fri' && ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'].includes(day)) return true;
    if (s.day_of_week === 'Mon-Wed-Fri' && ['Monday', 'Wednesday', 'Friday'].includes(day)) return true;
    if (s.day_of_week === 'Tue-Thu' && ['Tuesday', 'Thursday'].includes(day)) return true;
    return false;
  });
};

const openScheduleModal = (sch = null) => {
  scheduleError.value = '';
  const isSHS = isCurrentSectionSHS.value;
  if (sch) {
    scheduleForm.value = {
      id: sch.id,
      section_id: sch.section_id,
      subject_id: sch.subject_id,
      semester: isSHS ? (sch.semester || selectedScheduleSemester.value) : 'Full Year',
      teacher_id: sch.teacher_id,
      day_of_week: sch.day_of_week,
      time_start: sch.time_start ? sch.time_start.substring(0, 5) : '08:00',
      time_end: sch.time_end ? sch.time_end.substring(0, 5) : '09:30',
      room: sch.room || ''
    };
  } else {
    scheduleForm.value = {
      id: null,
      section_id: selectedScheduleSectionId.value,
      subject_id: activeSectionSchedule.value?.available_subjects?.[0]?.id || '',
      semester: isSHS ? selectedScheduleSemester.value : 'Full Year',
      teacher_id: null,
      day_of_week: isSHS ? 'Mon-Wed-Fri' : 'Mon-Fri',
      time_start: '08:00',
      time_end: isSHS ? '09:30' : '09:00',
      room: activeSectionSchedule.value?.section?.room || ''
    };
  }
  showScheduleModal.value = true;
};

const saveScheduleItem = async () => {
  isSavingSchedule.value = true;
  scheduleError.value = '';
  try {
    await api.saveSchedule({
      ...scheduleForm.value,
      section_id: selectedScheduleSectionId.value
    });
    successMessage.value = 'Class schedule item saved successfully!';
    showScheduleModal.value = false;
    await loadSectionScheduleData();
  } catch (err) {
    scheduleError.value = err.message || 'Conflict detected or invalid time range.';
  } finally {
    isSavingSchedule.value = false;
  }
};

const deleteScheduleModal = ref({
  isOpen: false,
  schedule: null,
  isDeleting: false
});

const openDeleteScheduleModal = (sch) => {
  deleteScheduleModal.value = {
    isOpen: true,
    schedule: sch,
    isDeleting: false
  };
};

const handleModalDeleteSchedule = () => {
  if (!scheduleForm.value.id) return;
  const sch = activeSectionSchedule.value?.schedules?.find(s => s.id === scheduleForm.value.id) || {
    id: scheduleForm.value.id,
    subject_code: activeSectionSchedule.value?.available_subjects?.find(s => s.id === scheduleForm.value.subject_id)?.code || '',
    subject_title: activeSectionSchedule.value?.available_subjects?.find(s => s.id === scheduleForm.value.subject_id)?.title || '',
    day_of_week: scheduleForm.value.day_of_week,
    time_start: scheduleForm.value.time_start,
    time_end: scheduleForm.value.time_end,
    room: scheduleForm.value.room
  };
  showScheduleModal.value = false;
  openDeleteScheduleModal(sch);
};

const executeDeleteSchedule = async () => {
  if (!deleteScheduleModal.value.schedule) return;
  deleteScheduleModal.value.isDeleting = true;
  try {
    await api.deleteSchedule(deleteScheduleModal.value.schedule.id);
    successMessage.value = 'Schedule period removed successfully.';
    deleteScheduleModal.value.isOpen = false;
    await loadSectionScheduleData();
  } catch (err) {
    scheduleError.value = err.message || 'Failed to remove schedule.';
  } finally {
    deleteScheduleModal.value.isDeleting = false;
  }
};

// --- SCHOOL EVENTS & ACADEMIC CALENDAR METHODS ---
const switchToEventsTab = async () => {
  activeTab.value = 'events';
  await loadEventsData();
};

const loadEventsData = async () => {
  try {
    let params = [];
    if (eventFilter.value.category) params.push(`category=${encodeURIComponent(eventFilter.value.category)}`);
    if (eventFilter.value.audience) params.push(`audience=${encodeURIComponent(eventFilter.value.audience)}`);
    const res = await api.getEvents(params.join('&'));
    calendarData.value = res.data;
  } catch (err) {
    console.error('Failed to load events:', err);
  }
};

const openEventModal = (ev = null) => {
  if (ev) {
    eventForm.value = {
      id: ev.id,
      title: ev.title,
      description: ev.description || '',
      event_category: ev.event_category || 'Academic',
      start_date: ev.start_date,
      end_date: ev.end_date || ev.start_date,
      start_time: ev.start_time ? ev.start_time.substring(0, 5) : '',
      end_time: ev.end_time ? ev.end_time.substring(0, 5) : '',
      location: ev.location || '',
      target_audience: ev.target_audience || 'All',
      is_published: ev.is_published ?? 1
    };
  } else {
    eventForm.value = {
      id: null,
      title: '',
      description: '',
      event_category: 'Academic',
      start_date: new Date().toISOString().substring(0, 10),
      end_date: new Date().toISOString().substring(0, 10),
      start_time: '08:00',
      end_time: '17:00',
      location: 'Campus Grounds',
      target_audience: 'All',
      is_published: 1
    };
  }
  showEventModal.value = true;
};

const saveSchoolEvent = async () => {
  isSavingEvent.value = true;
  try {
    await api.saveEvent(eventForm.value);
    successMessage.value = 'School event saved & broadcasted!';
    showEventModal.value = false;
    await loadEventsData();
  } catch (err) {
    alert(err.message || 'Failed to save event.');
  } finally {
    isSavingEvent.value = false;
  }
};

const deleteEventModal = ref({
  isOpen: false,
  event: null,
  isDeleting: false
});

const openDeleteEventModal = (ev) => {
  deleteEventModal.value = {
    isOpen: true,
    event: ev,
    isDeleting: false
  };
};

const executeDeleteEvent = async () => {
  if (!deleteEventModal.value.event) return;
  deleteEventModal.value.isDeleting = true;
  try {
    await api.deleteEvent(deleteEventModal.value.event.id);
    successMessage.value = 'Event deleted successfully.';
    deleteEventModal.value.isOpen = false;
    await loadEventsData();
  } catch (err) {
    alert(err.message || 'Failed to delete event.');
  } finally {
    deleteEventModal.value.isDeleting = false;
  }
};

// Formatting helpers
const formatTime = (timeStr) => {
  if (!timeStr) return '';
  const parts = timeStr.split(':');
  let h = parseInt(parts[0], 10);
  const m = parts[1] || '00';
  const ampm = h >= 12 ? 'PM' : 'AM';
  h = h % 12 || 12;
  return `${h}:${m} ${ampm}`;
};

const formatEventDate = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatEventDateRange = (startStr, endStr) => {
  if (!startStr) return '';
  if (!endStr || startStr === endStr) {
    return formatEventDate(startStr);
  }
  return `${formatEventDate(startStr)} - ${formatEventDate(endStr)}`;
};

const getEventCategoryBadgeClass = (cat) => {
  if (cat === 'Academic') return 'bg-purple-100 text-purple-800 border border-purple-200';
  if (cat === 'Examination') return 'bg-rose-100 text-rose-800 border border-rose-200';
  if (cat === 'Holiday') return 'bg-amber-100 text-amber-800 border border-amber-200';
  if (cat === 'Activity') return 'bg-emerald-100 text-emerald-800 border border-emerald-200';
  if (cat === 'Administrative') return 'bg-blue-100 text-blue-800 border border-blue-200';
  return 'bg-slate-100 text-slate-800';
};

onMounted(async () => {
  await loadData();
  await loadEventsData();
  if (sectionsData.value.sections?.length > 0) {
    selectedScheduleSectionId.value = sectionsData.value.sections[0].id;
  }
});
</script>
