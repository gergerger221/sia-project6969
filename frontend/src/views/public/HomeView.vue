<template>
  <div class="min-h-screen bg-slate-100 text-slate-900 font-sans selection:bg-amber-500 selection:text-white pb-12">
    
    <!-- ========================================================================= -->
    <!-- 1. HERO SECTION: JUNIOR & SENIOR HIGH SCHOOL ENROLLMENT SYSTEM            -->
    <!-- ========================================================================= -->
    <section 
      class="relative overflow-hidden bg-gradient-to-br from-[#061322] via-[#0c2340] to-[#0a1b2f] text-white border-b-4 border-amber-500 shadow-2xl"
    >
      <!-- Academic Background Elements & Contrast Overlay -->
      <div class="absolute inset-0 bg-[radial-gradient(#1e3a8a_1px,transparent_1px)] [background-size:28px_28px] opacity-20 pointer-events-none"></div>
      <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-blue-500/15 blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-amber-500/15 blur-3xl pointer-events-none"></div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20 flex flex-col lg:flex-row items-center justify-between gap-10">
        
        <!-- Left: Hero Headline & Clear Primary Call-to-Actions -->
        <div class="max-w-2xl space-y-5 text-center lg:text-left z-10">
          
          <!-- Official Institutional Badge -->
          <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-amber-500/15 backdrop-blur-md border border-amber-400/40 text-amber-300 text-xs font-black uppercase tracking-wider shadow-sm">
            <Sparkles class="w-3.5 h-3.5 text-amber-400 shrink-0" />
            <span>DepEd School ID: 405621 • S.Y. 2026-2027 ENROLLMENT OPEN</span>
          </div>

          <!-- Main Title -->
          <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.12] font-serif">
            Junior & Senior High School
            <span class="block bg-gradient-to-r from-amber-300 via-amber-200 to-yellow-400 bg-clip-text text-transparent mt-1">
              Enrollment System
            </span>
          </h1>

          <!-- Short Description -->
          <p class="text-sm sm:text-base text-slate-200 leading-relaxed font-normal max-w-xl mx-auto lg:mx-0 drop-shadow-xs">
            Enroll, submit requirements, track your application, and access your student records online for <strong>Junior High School (Grades 7–10)</strong> and <strong>Senior High School (Grades 11–12)</strong>.
          </p>

          <!-- Core Feature Badges -->
          <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2 pt-1">
            <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md border border-slate-700 text-xs font-bold text-slate-200 shadow-xs">
              <CheckCircle class="w-3.5 h-3.5 text-emerald-400 shrink-0" />
              <span>Grades 7 to 10 (JHS)</span>
            </div>
            <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md border border-slate-700 text-xs font-bold text-slate-200 shadow-xs">
              <CheckCircle class="w-3.5 h-3.5 text-amber-400 shrink-0" />
              <span>Grades 11 & 12 (SHS Strands)</span>
            </div>
            <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md border border-slate-700 text-xs font-bold text-slate-200 shadow-xs">
              <CheckCircle class="w-3.5 h-3.5 text-blue-400 shrink-0" />
              <span>100% DepEd Voucher Subsidy</span>
            </div>
          </div>

          <!-- Primary Call-to-Action Buttons -->
          <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 pt-3">
            
            <!-- 1. Apply for Enrollment -->
            <router-link 
              to="/register" 
              class="px-6 py-3.5 rounded-xl text-xs sm:text-sm font-black bg-gradient-to-r from-amber-500 via-amber-600 to-amber-500 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-xl shadow-amber-500/25 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center space-x-2 cursor-pointer border-2 border-amber-300"
            >
              <UserPlus class="w-4 h-4 text-slate-950" />
              <span>Apply for Enrollment</span>
              <ArrowRight class="w-4 h-4 text-slate-950" />
            </router-link>

            <!-- 2. Student Login -->
            <router-link 
              to="/login" 
              class="px-5 py-3.5 rounded-xl text-xs sm:text-sm font-extrabold bg-blue-900/90 hover:bg-blue-800 text-white border-2 border-blue-400/50 hover:border-amber-400 backdrop-blur-md shadow-lg transition-all flex items-center space-x-2 cursor-pointer"
            >
              <GraduationCap class="w-4 h-4 text-amber-300" />
              <span>Student Login</span>
            </router-link>

            <!-- 3. Staff Login -->
            <router-link 
              to="/staff-login" 
              class="px-5 py-3.5 rounded-xl text-xs sm:text-sm font-bold bg-slate-900/90 hover:bg-slate-800 text-slate-200 hover:text-white border-2 border-slate-700 hover:border-amber-400 backdrop-blur-md transition-all flex items-center space-x-2 cursor-pointer"
            >
              <ShieldCheck class="w-4 h-4 text-amber-400" />
              <span>Staff Login</span>
            </router-link>

          </div>

        </div>

        <!-- Right: Educational Visual Showcase Card (Representing both JHS & SHS) -->
        <div class="w-full lg:max-w-md z-10">
          <div class="p-6 rounded-3xl bg-slate-900/90 backdrop-blur-xl border-2 border-amber-400/50 shadow-2xl space-y-4 text-slate-100">
            
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
              <div class="flex items-center space-x-2.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/50 text-amber-400 flex items-center justify-center">
                  <GraduationCap class="w-5 h-5" />
                </div>
                <div>
                  <h2 class="text-sm font-black font-serif text-white">Academic Pathways</h2>
                  <p class="text-[10px] text-slate-400">JHS & SHS Programs Available</p>
                </div>
              </div>
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                Active S.Y.
              </span>
            </div>

            <!-- Quick Program Highlights -->
            <div class="space-y-2.5 text-xs">
              <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800 flex items-center justify-between hover:border-blue-400 transition">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 rounded-lg bg-blue-600/20 text-blue-400 flex items-center justify-center font-black text-xs font-mono">
                    JHS
                  </div>
                  <div>
                    <h3 class="font-bold text-white text-xs">Junior High School</h3>
                    <p class="text-[10px] text-slate-400">Grades 7, 8, 9, 10 • MATATAG</p>
                  </div>
                </div>
                <span class="text-[10px] text-amber-300 font-bold">ESC Subsidy</span>
              </div>

              <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800 flex items-center justify-between hover:border-amber-400 transition">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center font-black text-xs font-mono">
                    SHS
                  </div>
                  <div>
                    <h3 class="font-bold text-white text-xs">SHS Academic Track</h3>
                    <p class="text-[10px] text-slate-400">STEM, ABM, HUMSS, GAS</p>
                  </div>
                </div>
                <span class="text-[10px] text-emerald-400 font-bold">100% Voucher</span>
              </div>

              <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800 flex items-center justify-between hover:border-purple-400 transition">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center font-black text-xs font-mono">
                    TVL
                  </div>
                  <div>
                    <h3 class="font-bold text-white text-xs">SHS TVL Track</h3>
                    <p class="text-[10px] text-slate-400">ICT (Programming) & HE (Culinary)</p>
                  </div>
                </div>
                <span class="text-[10px] text-purple-300 font-bold">TESDA NC II</span>
              </div>
            </div>

            <!-- Quick Action in Card -->
            <div class="pt-2 border-t border-slate-800 flex items-center justify-between text-xs">
              <span class="text-slate-400 text-[11px]">Ready to enroll?</span>
              <button 
                type="button"
                @click="activeHubTab = 'academics'" 
                class="text-amber-400 hover:text-amber-300 font-bold inline-flex items-center space-x-1 cursor-pointer"
              >
                <span>Explore All Programs</span>
                <ArrowRight class="w-3.5 h-3.5" />
              </button>
            </div>

          </div>
        </div>

      </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 2. ENROLLMENT LEVELS & PROGRAMS (Clear JHS & SHS Separation)               -->
    <!-- ========================================================================= -->
    <section class="mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="text-center max-w-2xl mx-auto mb-6">
        <div class="inline-block px-3 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-900 border border-blue-200 mb-1.5">
          Academic Levels Supported
        </div>
        <h2 class="text-2xl sm:text-3xl font-black text-[#0c2340] font-serif tracking-tight">
          Comprehensive Secondary Education
        </h2>
        <p class="text-xs sm:text-sm text-slate-600 mt-1">
          Offering complete DepEd-recognized curriculums from Grade 7 through Grade 12 with full government tuition subsidy programs.
        </p>
      </div>

      <!-- Side-by-Side Dual Program Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- CARD 1: JUNIOR HIGH SCHOOL (GRADES 7 TO 10) -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white border-2 border-slate-200 hover:border-blue-600 shadow-md hover:shadow-xl transition-all duration-200 flex flex-col justify-between space-y-4">
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center justify-center shadow-xs">
                <BookOpen class="w-6 h-6" />
              </div>
              <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-blue-100 text-blue-900 border border-blue-300">
                Grades 7, 8, 9, 10
              </span>
            </div>

            <div>
              <h3 class="text-xl font-black text-[#0c2340] font-serif">Junior High School (JHS)</h3>
              <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                Foundational secondary education aligned with DepEd K-12 and MATATAG curriculum guidelines.
              </p>
            </div>

            <!-- JHS Feature List -->
            <ul class="space-y-2 text-xs text-slate-700 pt-1">
              <li class="flex items-center space-x-2">
                <CheckCircle class="w-4 h-4 text-emerald-600 shrink-0" />
                <span><strong>Grade Levels:</strong> Grade 7, Grade 8, Grade 9, Grade 10</span>
              </li>
              <li class="flex items-center space-x-2">
                <CheckCircle class="w-4 h-4 text-emerald-600 shrink-0" />
                <span><strong>Core Disciplines:</strong> Integrated Science, Mathematics, English, TLE, AP, MAPEH</span>
              </li>
              <li class="flex items-center space-x-2">
                <CheckCircle class="w-4 h-4 text-emerald-600 shrink-0" />
                <span><strong>Tuition Support:</strong> PEAC Educational Service Contracting (ESC) Subsidy</span>
              </li>
              <li class="flex items-center space-x-2">
                <CheckCircle class="w-4 h-4 text-emerald-600 shrink-0" />
                <span><strong>Modern Learning:</strong> Interactive PC Hubs & Junior Science Laboratories</span>
              </li>
            </ul>
          </div>

          <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
            <button 
              type="button" 
              @click="openStrandModal(academicPrograms[0])" 
              class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer"
            >
              View JHS Details
            </button>
            <router-link 
              to="/register" 
              class="px-5 py-2.5 rounded-xl text-xs font-black bg-blue-900 hover:bg-blue-800 text-white shadow-md transition flex items-center space-x-1.5 cursor-pointer"
            >
              <span>Enroll for JHS</span>
              <ArrowRight class="w-3.5 h-3.5" />
            </router-link>
          </div>
        </div>

        <!-- CARD 2: SENIOR HIGH SCHOOL (GRADES 11 & 12) -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white border-2 border-slate-200 hover:border-amber-500 shadow-md hover:shadow-xl transition-all duration-200 flex flex-col justify-between space-y-4">
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center shadow-xs">
                <Award class="w-6 h-6" />
              </div>
              <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-900 border border-amber-300">
                Grades 11 & 12
              </span>
            </div>

            <div>
              <h3 class="text-xl font-black text-[#0c2340] font-serif">Senior High School (SHS)</h3>
              <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                Specialized Academic and TVL tracks preparing learners for college, research, or industry certification.
              </p>
            </div>

            <!-- SHS Feature List -->
            <ul class="space-y-2 text-xs text-slate-700 pt-1">
              <li class="flex items-center space-x-2">
                <CheckCircle class="w-4 h-4 text-emerald-600 shrink-0" />
                <span><strong>Academic Tracks:</strong> STEM, ABM, HUMSS, General Academic (GAS)</span>
              </li>
              <li class="flex items-center space-x-2">
                <CheckCircle class="w-4 h-4 text-emerald-600 shrink-0" />
                <span><strong>TVL Tracks:</strong> ICT (Programming/CSS) & Home Economics (Culinary/Cookery)</span>
              </li>
              <li class="flex items-center space-x-2">
                <CheckCircle class="w-4 h-4 text-emerald-600 shrink-0" />
                <span><strong>100% DepEd Voucher:</strong> Zero tuition top-up for Public JHS Completers</span>
              </li>
              <li class="flex items-center space-x-2">
                <CheckCircle class="w-4 h-4 text-emerald-600 shrink-0" />
                <span><strong>Certifications:</strong> TESDA NC II Assessment & Capstone Research Labs</span>
              </li>
            </ul>
          </div>

          <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
            <button 
              type="button" 
              @click="activeHubTab = 'academics'" 
              class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer"
            >
              Explore SHS Strands
            </button>
            <router-link 
              to="/register" 
              class="px-5 py-2.5 rounded-xl text-xs font-black bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-md transition flex items-center space-x-1.5 cursor-pointer border border-amber-400"
            >
              <span>Enroll for SHS</span>
              <ArrowRight class="w-3.5 h-3.5" />
            </router-link>
          </div>
        </div>

      </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 3. COMPACT INTERACTIVE ACADEMIC & ADMISSION HUB (Tabbed View)              -->
    <!-- ========================================================================= -->
    <section id="academic-hub" class="mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-lg p-5 sm:p-7">
        
        <!-- Navigation Tabs Bar -->
        <div class="flex items-center justify-between border-b-2 border-slate-100 pb-4 mb-6 flex-wrap gap-3">
          <div class="flex items-center space-x-1.5 bg-slate-100 p-1.5 rounded-2xl overflow-x-auto max-w-full scrollbar-none">
            <button 
              @click="activeHubTab = 'academics'"
              :class="activeHubTab === 'academics' ? 'bg-[#0c2340] text-amber-400 font-black shadow-sm' : 'text-slate-600 hover:text-slate-900 font-bold'"
              class="px-4 py-2 rounded-xl text-xs transition flex items-center space-x-1.5 cursor-pointer"
            >
              <BookOpen class="w-3.5 h-3.5" />
              <span>1. Curriculums & Strands</span>
            </button>

            <button 
              @click="activeHubTab = 'pathway'"
              :class="activeHubTab === 'pathway' ? 'bg-[#0c2340] text-amber-400 font-black shadow-sm' : 'text-slate-600 hover:text-slate-900 font-bold'"
              class="px-4 py-2 rounded-xl text-xs transition flex items-center space-x-1.5 cursor-pointer"
            >
              <Layers class="w-3.5 h-3.5" />
              <span>2. Admission Steps</span>
            </button>

            <button 
              @click="activeHubTab = 'vouchers'"
              :class="activeHubTab === 'vouchers' ? 'bg-[#0c2340] text-amber-400 font-black shadow-sm' : 'text-slate-600 hover:text-slate-900 font-bold'"
              class="px-4 py-2 rounded-xl text-xs transition flex items-center space-x-1.5 cursor-pointer"
            >
              <Award class="w-3.5 h-3.5" />
              <span>3. DepEd Vouchers</span>
            </button>

            <button 
              @click="activeHubTab = 'requirements'"
              :class="activeHubTab === 'requirements' ? 'bg-[#0c2340] text-amber-400 font-black shadow-sm' : 'text-slate-600 hover:text-slate-900 font-bold'"
              class="px-4 py-2 rounded-xl text-xs transition flex items-center space-x-1.5 cursor-pointer"
            >
              <FileText class="w-3.5 h-3.5" />
              <span>4. Document Checklist</span>
            </button>

            <button 
              @click="activeHubTab = 'facilities'"
              :class="activeHubTab === 'facilities' ? 'bg-[#0c2340] text-amber-400 font-black shadow-sm' : 'text-slate-600 hover:text-slate-900 font-bold'"
              class="px-4 py-2 rounded-xl text-xs transition flex items-center space-x-1.5 cursor-pointer"
            >
              <Building class="w-3.5 h-3.5" />
              <span>5. Campus Facilities</span>
            </button>

            <button 
              @click="activeHubTab = 'faqs'"
              :class="activeHubTab === 'faqs' ? 'bg-[#0c2340] text-amber-400 font-black shadow-sm' : 'text-slate-600 hover:text-slate-900 font-bold'"
              class="px-4 py-2 rounded-xl text-xs transition flex items-center space-x-1.5 cursor-pointer"
            >
              <HelpCircle class="w-3.5 h-3.5" />
              <span>6. FAQs</span>
            </button>
          </div>

          <router-link 
            to="/register" 
            class="px-4 py-2 rounded-xl text-xs font-black bg-amber-500 hover:bg-amber-400 text-slate-950 transition flex items-center space-x-1.5 shadow-sm border border-amber-400"
          >
            <span>Apply Now</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>
        </div>

        <!-- TAB 1: ACADEMICS & STRANDS -->
        <div v-show="activeHubTab === 'academics'" class="space-y-5 animate-in fade-in duration-200">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h3 class="text-lg font-black text-[#0c2340] font-serif">DepEd Recognized JHS & SHS Curriculums</h3>
              <p class="text-xs text-slate-500">Exclusively offering Junior High School (Grades 7–10) and Senior High School (Grades 11–12)</p>
            </div>
            <!-- Sub Filter -->
            <div class="flex items-center space-x-1 bg-slate-100 p-1 rounded-xl shrink-0">
              <button 
                v-for="cat in ['all', 'JHS', 'ACAD', 'TVL']" 
                :key="cat"
                @click="selectedTrackCategory = cat"
                :class="selectedTrackCategory === cat ? 'bg-white text-[#0c2340] font-black shadow-xs' : 'text-slate-500'"
                class="px-2.5 py-1 rounded-lg text-[11px] capitalize cursor-pointer"
              >
                {{ cat === 'all' ? 'All Offerings' : cat }}
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div 
              v-for="prog in filteredPrograms" 
              :key="prog.id" 
              class="p-4 rounded-2xl bg-slate-50 border border-slate-200 hover:border-amber-400 transition flex flex-col justify-between"
            >
              <div>
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-black text-amber-700 font-mono">{{ prog.code }}</span>
                  <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-slate-200 text-slate-700">
                    {{ prog.category }}
                  </span>
                </div>
                <h4 class="font-bold text-sm text-[#0c2340] font-serif leading-snug">{{ prog.name }}</h4>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ prog.description }}</p>
                <div class="mt-3 space-y-1">
                  <div v-for="(h, hIdx) in prog.highlights" :key="hIdx" class="text-[11px] text-slate-600 flex items-center space-x-1.5">
                    <CheckCircle class="w-3 h-3 text-emerald-600 shrink-0" />
                    <span>{{ h }}</span>
                  </div>
                </div>
              </div>
              <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
                <span class="text-[10px] text-emerald-700 font-bold">100% Voucher Eligible</span>
                <!-- View Details Button -->
                <button 
                  type="button"
                  @click="openStrandModal(prog)" 
                  class="px-3.5 py-1.5 rounded-xl font-bold bg-[#0c2340] hover:bg-blue-900 text-amber-400 text-xs transition flex items-center space-x-1.5 shadow-xs cursor-pointer"
                >
                  <Eye class="w-3.5 h-3.5" />
                  <span>View Details</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- TAB 2: ADMISSION PATHWAY -->
        <div v-show="activeHubTab === 'pathway'" class="space-y-5 animate-in fade-in duration-200">
          <div>
            <h3 class="text-lg font-black text-[#0c2340] font-serif">4-Stage Streamlined Admission Pathway</h3>
            <p class="text-xs text-slate-500">Step-by-step walkthrough from online registration to official student ID creation</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl bg-slate-50 border-2 border-slate-200 flex flex-col justify-between space-y-2">
              <div>
                <div class="w-8 h-8 rounded-xl bg-[#0c2340] text-amber-400 flex items-center justify-center text-xs font-black font-mono mb-2">
                  01
                </div>
                <h4 class="font-bold text-sm text-[#0c2340] font-serif">Online Registration</h4>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                  Create your applicant account, provide personal info, and enter your 12-digit DepEd LRN.
                </p>
              </div>
              <span class="text-[10px] font-bold text-blue-700 uppercase">Step 1: Account</span>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border-2 border-slate-200 flex flex-col justify-between space-y-2">
              <div>
                <div class="w-8 h-8 rounded-xl bg-[#0c2340] text-amber-400 flex items-center justify-center text-xs font-black font-mono mb-2">
                  02
                </div>
                <h4 class="font-bold text-sm text-[#0c2340] font-serif">Upload Credentials</h4>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                  Upload clear digital scans or photos of PSA Birth Certificate, SF9 Report Card, Good Moral, & 2x2 photo.
                </p>
              </div>
              <span class="text-[10px] font-bold text-amber-700 uppercase">Step 2: Documents</span>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border-2 border-slate-200 flex flex-col justify-between space-y-2">
              <div>
                <div class="w-8 h-8 rounded-xl bg-[#0c2340] text-amber-400 flex items-center justify-center text-xs font-black font-mono mb-2">
                  03
                </div>
                <h4 class="font-bold text-sm text-[#0c2340] font-serif">Registrar Evaluation</h4>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                  Registrar authenticates requirements, assigns official section seating, and generates fee assessment.
                </p>
              </div>
              <span class="text-[10px] font-bold text-emerald-700 uppercase">Step 3: Approval</span>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border-2 border-slate-200 flex flex-col justify-between space-y-2">
              <div>
                <div class="w-8 h-8 rounded-xl bg-[#0c2340] text-amber-400 flex items-center justify-center text-xs font-black font-mono mb-2">
                  04
                </div>
                <h4 class="font-bold text-sm text-[#0c2340] font-serif">Downpayment & COR</h4>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                  Settle downpayment via PayMongo online or Walk-in Cashier to receive your official COR and Student ID.
                </p>
              </div>
              <span class="text-[10px] font-bold text-purple-700 uppercase">Step 4: Enrollment</span>
            </div>
          </div>
        </div>

        <!-- TAB 3: DEPED VOUCHERS -->
        <div v-show="activeHubTab === 'vouchers'" class="space-y-5 animate-in fade-in duration-200">
          <div class="p-6 rounded-2xl bg-[#0c2340] text-white border border-amber-400">
            <div class="max-w-2xl space-y-2">
              <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase bg-amber-500 text-slate-950">Government Subsidy</span>
              <h3 class="text-xl font-black font-serif text-amber-300">Senior High School Voucher Program (SHS-VP)</h3>
              <p class="text-xs text-slate-300 leading-relaxed">
                Public Junior High School Completers automatically qualify for a 100% DepEd voucher subsidy value (₱17,500 – ₱22,500/year). Zero base tuition top-up options are available on selected strands.
              </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-slate-700">
              <div class="p-3.5 rounded-xl bg-slate-900 border border-amber-400/40 text-center">
                <div class="text-xs text-amber-400 font-bold">Public JHS Completers</div>
                <div class="text-2xl font-black font-mono mt-1">100% Grant</div>
                <div class="text-[10px] text-slate-400 mt-0.5">No Voucher Code Needed</div>
              </div>
              <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-700 text-center">
                <div class="text-xs text-blue-300 font-bold">Private ESC Grantees</div>
                <div class="text-2xl font-black font-mono mt-1">80% Subsidy</div>
                <div class="text-[10px] text-slate-400 mt-0.5">ESC Certificate Applied</div>
              </div>
              <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-700 text-center">
                <div class="text-xs text-purple-300 font-bold">Private Non-ESC / QVR</div>
                <div class="text-2xl font-black font-mono mt-1">50% Subsidy</div>
                <div class="text-[10px] text-slate-400 mt-0.5">QVR Voucher Grant</div>
              </div>
            </div>
          </div>
        </div>

        <!-- TAB 4: DOCUMENT CHECKLIST -->
        <div v-show="activeHubTab === 'requirements'" class="space-y-5 animate-in fade-in duration-200">
          <div>
            <h3 class="text-lg font-black text-[#0c2340] font-serif">Required Documents by Grade Level</h3>
            <p class="text-xs text-slate-500">Scans/photos (PDF, JPG, PNG) can be submitted through the online admission portal</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
              <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">Grade 7 (JHS)</span>
              <h4 class="font-bold text-sm text-[#0c2340] font-serif mt-2 mb-2">Junior High Requirements</h4>
              <ul class="text-xs text-slate-600 space-y-1.5">
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>Original PSA Birth Certificate</span></li>
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>Grade 6 SF9 Report Card</span></li>
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>Good Moral Certificate</span></li>
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>2 pcs 2x2 ID Photos</span></li>
              </ul>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border-2 border-amber-400">
              <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900">Grade 11 (SHS)</span>
              <h4 class="font-bold text-sm text-[#0c2340] font-serif mt-2 mb-2">Senior High Requirements</h4>
              <ul class="text-xs text-slate-600 space-y-1.5">
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>Original PSA Birth Certificate</span></li>
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>Grade 10 SF9 Report Card (with LRN)</span></li>
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>JHS Completion Certificate</span></li>
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>Good Moral Certificate</span></li>
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>ESC/QVR Cert (If Private JHS)</span></li>
              </ul>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
              <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">Transferees</span>
              <h4 class="font-bold text-sm text-[#0c2340] font-serif mt-2 mb-2">Transferee Requirements</h4>
              <ul class="text-xs text-slate-600 space-y-1.5">
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>Original PSA Birth Certificate</span></li>
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>Official SF10 / Form 137 Transcript</span></li>
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>Certificate of Honorable Dismissal</span></li>
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>Good Moral Certificate</span></li>
              </ul>
            </div>
          </div>
        </div>

        <!-- TAB 5: CAMPUS FACILITIES -->
        <div v-show="activeHubTab === 'facilities'" class="space-y-5 animate-in fade-in duration-200">
          <div>
            <h3 class="text-lg font-black text-[#0c2340] font-serif">Campus Laboratories & Learning Spaces</h3>
            <p class="text-xs text-slate-500">Dedicated facilities for Junior & Senior High hands-on scientific and technical research</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div v-for="(fac, idx) in facilities" :key="idx" class="rounded-2xl border border-slate-200 overflow-hidden bg-slate-50">
              <img :src="fac.image" :alt="fac.title" class="w-full h-36 object-cover" />
              <div class="p-3.5">
                <h4 class="font-bold text-xs text-[#0c2340] font-serif">{{ fac.title }}</h4>
                <p class="text-[11px] text-slate-500 mt-1">{{ fac.desc }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- TAB 6: FAQS -->
        <div v-show="activeHubTab === 'faqs'" class="space-y-3 animate-in fade-in duration-200">
          <div>
            <h3 class="text-lg font-black text-[#0c2340] font-serif">Frequently Asked Questions</h3>
            <p class="text-xs text-slate-500">Quick answers on JHS & SHS admission, vouchers, and payment methods</p>
          </div>

          <div class="space-y-2">
            <div 
              v-for="(faq, fIdx) in faqs" 
              :key="fIdx" 
              class="rounded-xl border border-slate-200 bg-slate-50 p-3.5 text-xs"
            >
              <div class="font-bold text-[#0c2340] font-serif mb-1">{{ faq.q }}</div>
              <div class="text-slate-600 leading-relaxed">{{ faq.a }}</div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 4. INSTITUTIONAL CONTACT & ACCREDITATION BANNER                            -->
    <!-- ========================================================================= -->
    <section class="mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="p-5 sm:p-6 rounded-3xl bg-[#0c2340] text-white flex flex-col md:flex-row items-center justify-between gap-4 border-2 border-amber-400 shadow-md">
        <div class="flex items-center space-x-3.5">
          <div class="w-11 h-11 rounded-2xl bg-white/10 border border-amber-400 flex items-center justify-center shrink-0">
            <MapPin class="w-5 h-5 text-amber-400" />
          </div>
          <div>
            <div class="font-bold text-sm font-serif text-white">JJKINGS Biringan School Main Campus</div>
            <div class="text-xs text-slate-300">Academic Boulevard, Biringan City, Samar, Eastern Visayas • Tel: (055) 888-7766</div>
          </div>
        </div>

        <div class="flex items-center space-x-3">
          <router-link 
            to="/register" 
            class="px-5 py-2.5 rounded-xl text-xs font-black bg-amber-500 hover:bg-amber-400 text-slate-950 transition flex items-center space-x-1.5 border border-amber-400 cursor-pointer shadow-sm"
          >
            <span>Apply for S.Y. 2026-2027</span>
            <ArrowRight class="w-3.5 h-3.5 text-slate-950" />
          </router-link>
        </div>
      </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 5. DETAILED STRAND INFORMATION MODAL                                      -->
    <!-- ========================================================================= -->
    <div 
      v-if="showStrandModal && selectedModalStrand" 
      class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6 overflow-y-auto animate-in fade-in duration-200"
      @click.self="closeStrandModal"
    >
      <div class="bg-white rounded-3xl max-w-2xl w-full overflow-hidden shadow-2xl border-2 border-amber-400 my-auto text-slate-900">
        
        <!-- Modal Image Banner Header -->
        <div class="relative h-48 sm:h-56 w-full bg-[#08182b] overflow-hidden">
          <img 
            :src="selectedModalStrand.image" 
            :alt="selectedModalStrand.name" 
            class="w-full h-full object-cover object-center"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-[#0c2340] via-[#0c2340]/70 to-transparent"></div>
          
          <!-- Close Button -->
          <button 
            @click="closeStrandModal" 
            class="absolute top-3.5 right-3.5 w-8 h-8 rounded-full bg-slate-900/80 text-white hover:bg-rose-600 flex items-center justify-center transition cursor-pointer shadow-md"
            title="Close"
          >
            <X class="w-4 h-4" />
          </button>

          <!-- Banner Badges & Title -->
          <div class="absolute bottom-4 left-4 right-4 text-white">
            <div class="flex items-center space-x-2 mb-1.5">
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500 text-slate-950 font-mono">
                {{ selectedModalStrand.code }}
              </span>
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-white/20 backdrop-blur-md text-amber-200 border border-white/30">
                {{ selectedModalStrand.category }}
              </span>
            </div>
            <h3 class="text-xl sm:text-2xl font-black font-serif text-white leading-tight">
              {{ selectedModalStrand.name }}
            </h3>
          </div>
        </div>

        <!-- Modal Body Content -->
        <div class="p-5 sm:p-7 space-y-5 max-h-[60vh] overflow-y-auto">
          
          <!-- Overview Description -->
          <div>
            <h4 class="text-xs font-black uppercase text-slate-400 tracking-wider mb-1.5">Program Overview</h4>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
              {{ selectedModalStrand.detailedDescription || selectedModalStrand.description }}
            </p>
          </div>

          <!-- Key Learning Areas & Specialized Subjects -->
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
            <h4 class="text-xs font-black uppercase text-[#0c2340] tracking-wider flex items-center space-x-1.5 font-serif">
              <BookOpen class="w-3.5 h-3.5 text-amber-600" />
              <span>Specialized Curriculum Subjects</span>
            </h4>
            <p class="text-xs text-slate-600 leading-relaxed">
              {{ selectedModalStrand.specializedSubjects }}
            </p>
          </div>

          <!-- Career & Senior High Pathways -->
          <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200 space-y-2">
            <h4 class="text-xs font-black uppercase text-amber-900 tracking-wider flex items-center space-x-1.5 font-serif">
              <Briefcase class="w-3.5 h-3.5 text-amber-700" />
              <span>Career Opportunities & Senior High Specializations</span>
            </h4>
            <p class="text-xs text-amber-950 leading-relaxed font-medium">
              {{ selectedModalStrand.careerPathways }}
            </p>
          </div>

          <!-- Dedicated Facilities & Voucher Note -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div class="p-3 rounded-xl bg-slate-100 border border-slate-200">
              <span class="text-[10px] uppercase font-bold text-slate-500 block">Dedicated Facilities</span>
              <strong class="text-slate-800 text-xs">{{ selectedModalStrand.facilitiesNote }}</strong>
            </div>

            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200">
              <span class="text-[10px] uppercase font-bold text-emerald-700 block">DepEd Voucher Subsidy</span>
              <strong class="text-emerald-900 text-xs">100% Voucher Grant Accepted</strong>
            </div>
          </div>

        </div>

        <!-- Modal Action Footer -->
        <div class="p-4 sm:p-5 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3">
          <button 
            type="button" 
            @click="closeStrandModal" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 transition cursor-pointer"
          >
            Close
          </button>

          <!-- Automatic Enrollment Button -->
          <button 
            type="button" 
            @click="enrollInStrand(selectedModalStrand)" 
            class="px-6 py-2.5 rounded-xl text-xs sm:text-sm font-black bg-gradient-to-r from-amber-500 via-amber-600 to-amber-500 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-md shadow-amber-500/25 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center space-x-2 cursor-pointer border border-amber-400"
          >
            <GraduationCap class="w-4 h-4 text-slate-950" />
            <span>Enroll in {{ selectedModalStrand.code || selectedModalStrand.name }}</span>
            <ArrowRight class="w-3.5 h-3.5 text-slate-950" />
          </button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { 
  UserPlus, 
  GraduationCap, 
  Award, 
  ArrowRight, 
  CheckCircle, 
  Check, 
  BookOpen, 
  Compass, 
  TrendingUp, 
  Users, 
  Cpu, 
  Coffee, 
  FileText, 
  Layers, 
  HelpCircle, 
  Building, 
  MapPin, 
  Sparkles,
  Eye,
  X,
  Briefcase,
  ShieldCheck
} from 'lucide-vue-next';
import { getRoleRouteName } from '../../router';
import api from '../../services/api';

const router = useRouter();
const route = useRoute();

const activeHubTab = ref('academics'); // 'academics', 'pathway', 'vouchers', 'requirements', 'facilities', 'faqs'

// Modal State
const showStrandModal = ref(false);
const selectedModalStrand = ref(null);

// ============================================================================
// ACADEMIC DATA (JHS & SHS ONLY - NO COLLEGE TOPICS)
// ============================================================================
const selectedTrackCategory = ref('all');

const academicPrograms = [
  {
    id: 'jhs',
    code: 'JHS',
    name: 'Junior High School',
    category: 'JHS (Grades 7–10)',
    type: 'JHS',
    grade_level_id: 1,
    track_id: null,
    strand_id: null,
    image: 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?q=80&w=1000&auto=format&fit=crop',
    description: 'Comprehensive DepEd K-12 and MATATAG curriculum delivering strong foundations in English, Math, Integrated Science, TLE, AP, and MAPEH.',
    detailedDescription: 'Our Junior High School department provides an exemplary foundational education emphasizing scientific inquiry, mathematical rigor, linguistic fluency, and values-centered formation. Students gain comprehensive preparation for all Senior High School tracks.',
    specializedSubjects: 'English, Mathematics, Integrated Science (Biology, Chemistry, Physics, Earth Science), Araling Panlipunan, Filipino, Edukasyon sa Pagpapakatao (EsP), Technology and Livelihood Education (TLE), and MAPEH (Music, Arts, Physical Education, Health).',
    careerPathways: 'Prepares learners for smooth transition to all Senior High School tracks (STEM, ABM, HUMSS, GAS, TVL-ICT, and TVL-HE).',
    facilitiesNote: 'Junior Science Lab, Speech & Multimedia Laboratory, Library Commons, Basketball & Badminton Gymnasium.',
    highlights: ['DepEd ESC Subsidy Eligible', 'Interactive Science & PC Labs']
  },
  {
    id: 'stem',
    code: 'STEM',
    name: 'Science, Tech, Engineering & Math',
    category: 'SHS Academic Track',
    type: 'ACAD',
    grade_level_id: 5,
    track_id: 1,
    strand_id: 1,
    image: 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?q=80&w=1000&auto=format&fit=crop',
    description: 'Rigorous preparatory program for aspiring Doctors, Engineers, Architects, Scientists, and Developers with Pre-Calculus, Physics, and Biology.',
    detailedDescription: 'The STEM strand is designed for inquisitive learners who aim to pioneer scientific discovery and technical innovation. With advanced hands-on laboratory experiments and capstone research, students excel in national scientific investigations and technological innovations.',
    specializedSubjects: 'General Physics 1 & 2, Pre-Calculus, Basic Calculus, General Chemistry 1 & 2, General Biology 1 & 2, Capstone Scientific Research, and Disaster Readiness.',
    careerPathways: 'Medical Sciences, Civil & Software Engineering, Architecture, Computer Systems, Biotechnology, Nursing, and Applied Sciences.',
    facilitiesNote: 'Advanced Robotics Hub, Chemical Fume Hood Benches, Optical Microscopes, High-Performance Computing Lab.',
    highlights: ['Advanced Robotics Lab', 'Scientific Research Capstone']
  },
  {
    id: 'abm',
    code: 'ABM',
    name: 'Accountancy, Business & Management',
    category: 'SHS Academic Track',
    type: 'ACAD',
    grade_level_id: 5,
    track_id: 1,
    strand_id: 2,
    image: 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?q=80&w=1000&auto=format&fit=crop',
    description: 'Specialized for future Certified Public Accountants, Financial Analysts, and Business Owners with Accounting and Finance principles.',
    detailedDescription: 'The ABM strand develops the financial acumen, entrepreneurial mindset, and executive leadership required in modern business operations. Features business enterprise simulation, financial modeling, and marketing campaigns.',
    specializedSubjects: 'Fundamentals of Accountancy, Business & Management (FABM 1 & 2), Business Finance, Principles of Marketing, Business Mathematics, Business Ethics, and Organization & Management.',
    careerPathways: 'Corporate Accountancy, Business Administration, Banking & Financial Management, Marketing Management, Corporate Economics, and Entrepreneurship.',
    facilitiesNote: 'Business Incubation Center, Financial Modeling Software Hub, Boardroom Simulation Hall.',
    highlights: ['Business Simulation Hub', 'Principles of Marketing']
  },
  {
    id: 'humss',
    code: 'HUMSS',
    name: 'Humanities & Social Sciences',
    category: 'SHS Academic Track',
    type: 'ACAD',
    grade_level_id: 5,
    track_id: 1,
    strand_id: 3,
    image: 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=1000&auto=format&fit=crop',
    description: 'Designed for future Lawyers, Diplomats, Journalists, Educators, and Psychologists with Creative Writing, Politics, and Sociology.',
    detailedDescription: 'The HUMSS strand immerses students in the study of human behavior, societal structures, governance, and communication. Students master persuasive speech, critical debate, and community leadership.',
    specializedSubjects: 'Creative Writing, Philippine Politics & Governance, Trends & Critical Thinking in the 21st Century, Disciplines and Ideas in the Social Sciences (DISS), and Community Engagement.',
    careerPathways: 'Legal Studies, Public Governance, Foreign Affairs, Journalism, Media Arts, Social Work, and Educational Leadership.',
    facilitiesNote: 'Mock Courtroom, Speech & Audio-Visual Studio, Debate Society Hall, Digital Journalism Newsroom.',
    highlights: ['Community Immersion', 'Debate & Public Speaking']
  },
  {
    id: 'gas',
    code: 'GAS',
    name: 'General Academic Strand',
    category: 'SHS Academic Track',
    type: 'ACAD',
    grade_level_id: 5,
    track_id: 1,
    strand_id: 4,
    image: 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?q=80&w=1000&auto=format&fit=crop',
    description: 'Flexible multidisciplinary academic strand combining social sciences, management, and humanities electives for diverse Senior High specializations.',
    detailedDescription: 'The GAS strand offers customizable academic pathways for students who wish to explore multidisciplinary subjects across social sciences, management, and arts during Senior High School.',
    specializedSubjects: 'Humanities Electives, Applied Economics, Organization & Management, Philippine Politics, Disaster Readiness, Contemporary Philippine Arts, and Social Inquiry.',
    careerPathways: 'Liberal Arts, Public Administration, Criminology, Interdisciplinary Studies, Communications, and Custom Senior High Career Tracks.',
    facilitiesNote: 'Integrated Academic Library Commons, Discussion Pods, Career Counseling Center.',
    highlights: ['Customizable Electives', 'Career Exploration']
  },
  {
    id: 'tvl-ict',
    code: 'TVL-ICT',
    name: 'Information & Communications Tech',
    category: 'SHS TVL Track',
    type: 'TVL',
    grade_level_id: 5,
    track_id: 2,
    strand_id: 5,
    image: 'https://images.unsplash.com/photo-1531482615713-2afd69097998?q=80&w=1000&auto=format&fit=crop',
    description: 'Hands-on technical training in Web Development, Python/C# Programming, Computer System Servicing (CSS), and Networking.',
    detailedDescription: 'The TVL-ICT strand empowers students with hands-on software development, IT systems servicing, and network infrastructure skills. Prepares graduates for direct TESDA NC II certifications and rapid tech industry employment.',
    specializedSubjects: 'Computer Systems Servicing (CSS NC II), Web Development & UI/UX Design, Java & Python Programming, Database Administration, and Industry Tech Immersion.',
    careerPathways: 'TESDA CSS NC II Certified, Junior Full-Stack Developer, Computer Systems Technician, IT Support Specialist, and Network Administrator.',
    facilitiesNote: 'Dual-Monitor PC & Mac Workstations, Hardware Servicing Lab, Networking Server Racks, Fiber-Optic Test Benches.',
    highlights: ['TESDA CSS NC II Certified', 'Direct IT Industry Immersion']
  },
  {
    id: 'tvl-he',
    code: 'TVL-HE',
    name: 'Home Economics (Culinary & Tourism)',
    category: 'SHS TVL Track',
    type: 'TVL',
    grade_level_id: 5,
    track_id: 2,
    strand_id: 6,
    image: 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?q=80&w=1000&auto=format&fit=crop',
    description: 'Master Commercial Cooking, Bread & Pastry Production, Food & Beverage Services, and Hotel & Tourism operations.',
    detailedDescription: 'The TVL-HE strand focuses on professional culinary arts, baking, hotel management, and hospitality services. Students train in state-of-the-art industrial kitchens and complete luxury hospitality apprenticeships.',
    specializedSubjects: 'Commercial Cookery NC II, Bread & Pastry Production (BPP NC II), Food & Beverage Services (FBS), Front Office Operations, and Tourism Promotion Services.',
    careerPathways: 'TESDA Cookery NC II, TESDA BPP NC II, Professional Chef de Partie, Pastry Artisan, Hotel & Cruise Hospitality Staff, and Restaurant Manager.',
    facilitiesNote: 'Commercial Stainless-Steel Culinary Kitchen, Industrial Deck Baking Ovens, Mock Hotel Suite & Front Desk, Dining Room Simulation Hall.',
    highlights: ['TESDA Cookery & BPP NC II', 'Commercial Kitchen Workshops']
  }
];

const filteredPrograms = computed(() => {
  if (selectedTrackCategory.value === 'all') return academicPrograms;
  return academicPrograms.filter(p => p.type === selectedTrackCategory.value);
});

const facilities = [
  {
    title: 'Science & STEM Laboratories',
    desc: 'Equipped with chemical safety fume hoods, optical microscopes, and precision physics apparatus.',
    image: 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?q=80&w=800&auto=format&fit=crop'
  },
  {
    title: 'Digital IT & Computing Hubs',
    desc: 'High-speed gigabit workstations for coding, computer hardware servicing, and multimedia design.',
    image: 'https://images.unsplash.com/photo-1531482615713-2afd69097998?q=80&w=800&auto=format&fit=crop'
  },
  {
    title: 'Academic Library Commons',
    desc: 'Comprehensive DepEd curriculum reference collection, quiet study carrels, and online journal access.',
    image: 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=800&auto=format&fit=crop'
  }
];

const faqs = [
  {
    q: 'How does the Senior High School DepEd Voucher Program work at JJKINGS Biringan School?',
    a: 'Public Junior High School completers automatically qualify for a 100% DepEd voucher subsidy value (₱17,500 – ₱22,500/year). Private school ESC grantees receive an 80% subsidy. The voucher is automatically calculated and deducted from the gross assessment by our Treasury department.'
  },
  {
    q: 'Can I submit my admission application and documents online without visiting campus?',
    a: 'Yes! Our web portal allows you to complete registration, choose your track/strand, and upload clear digital photos or scans of your PSA Birth Certificate and SF9 Report Card. You will receive real-time review updates directly on your applicant dashboard.'
  },
  {
    q: 'What payment methods are supported for tuition downpayments?',
    a: 'We accept both Walk-in Cashier payments (with automated priority queue tickets) and Online Payments (GCash, Maya, and Bank Direct Transfer). For online payments, simply submit your reference number and receipt screenshot on the portal for instant verification.'
  },
  {
    q: 'When will I receive my permanent Student Number and Student Portal access?',
    a: 'As soon as your initial minimum downpayment (₱3,000) or voucher verification is finalized by the Treasury Cashier, the system automatically creates your permanent Student ID (e.g. 2026-SHS-0001) and sets up your official student portal credentials.'
  }
];

// ============================================================================
// MODAL & AUTOMATIC ENROLLMENT HANDLER
// ============================================================================
const openStrandModal = (prog) => {
  selectedModalStrand.value = prog;
  showStrandModal.value = true;
};

const closeStrandModal = () => {
  showStrandModal.value = false;
  selectedModalStrand.value = null;
};

const enrollInStrand = async (strand) => {
  const strandSelection = {
    id: strand.id,
    code: strand.code,
    name: strand.name,
    grade_level_id: strand.grade_level_id,
    track_id: strand.track_id,
    strand_id: strand.strand_id,
    type: strand.type
  };

  sessionStorage.setItem('selected_enroll_strand', JSON.stringify(strandSelection));
  localStorage.setItem('selected_enroll_strand', JSON.stringify(strandSelection));

  closeStrandModal();

  const token = localStorage.getItem('sia_auth_token');
  const userJson = localStorage.getItem('sia_auth_user');

  if (token && userJson) {
    try {
      const user = JSON.parse(userJson);
      if (user.role_slug === 'applicant') {
        try {
          await api.updateApplication({
            grade_level_id: strand.grade_level_id,
            track_id: strand.track_id,
            strand_id: strand.strand_id
          });
        } catch (e) {
          // Continue to admission portal
        }
        router.push('/admission');
        return;
      }
    } catch (e) {
      // Continue to register
    }
  }

  router.push({
    path: '/register',
    query: {
      strand: strand.code,
      track: strand.type
    }
  });
};

onMounted(() => {
  const token = localStorage.getItem('sia_auth_token');
  const userJson = localStorage.getItem('sia_auth_user');
  if (token && userJson) {
    try {
      const user = JSON.parse(userJson);
      const target = getRoleRouteName(user.role_slug);
      if (target !== 'Home') {
        router.replace({ name: target });
        return;
      }
    } catch (e) {
      // Ignore parse error
    }
  }

  const onSwitchTabEvent = (e) => {
    if (e.detail && ['academics', 'pathway', 'vouchers', 'requirements', 'facilities', 'faqs'].includes(e.detail)) {
      activeHubTab.value = e.detail;
    }
  };
  window.addEventListener('switch-home-tab', onSwitchTabEvent);

  const savedTab = sessionStorage.getItem('sia_active_home_tab');
  const targetTab = route.query.tab || savedTab;
  if (targetTab && ['academics', 'pathway', 'vouchers', 'requirements', 'facilities', 'faqs'].includes(targetTab)) {
    activeHubTab.value = targetTab;
    setTimeout(() => {
      const el = document.getElementById('academic-hub');
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 150);
  }
});

watch(() => route.query.tab, (newTab) => {
  if (newTab && ['academics', 'pathway', 'vouchers', 'requirements', 'facilities', 'faqs'].includes(newTab)) {
    activeHubTab.value = newTab;
    setTimeout(() => {
      const el = document.getElementById('academic-hub');
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 150);
  }
});

watch(activeHubTab, (newTab) => {
  window.dispatchEvent(new CustomEvent('switch-home-tab', { detail: newTab }));
});
</script>
