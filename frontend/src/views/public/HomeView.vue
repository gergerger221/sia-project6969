<template>
  <div class="min-h-screen bg-slate-100 text-slate-900 font-sans selection:bg-amber-500 selection:text-white pb-12">
    
    <!-- ========================================================================= -->
    <!-- 1. HERO CAMPUS SLIDER (Centerpiece - Dynamic Autoplay Carousel)           -->
    <!-- ========================================================================= -->
    <section 
      class="relative overflow-hidden bg-[#08182b] text-white select-none shadow-xl border-b-4 border-amber-500"
      @mouseenter="pauseSlider"
      @mouseleave="resumeSlider"
      @keydown.left="prevSlide"
      @keydown.right="nextSlide"
      tabindex="0"
      aria-label="JJKINGS Biringan School Hero Slider"
    >
      <div class="relative h-[480px] sm:h-[540px] lg:h-[580px] w-full overflow-hidden">
        <transition-group name="slide-fade">
          <div 
            v-for="(slide, index) in slides" 
            :key="slide.id"
            v-show="currentSlide === index"
            class="absolute inset-0 w-full h-full"
          >
            <!-- Background Image with Academic Contrast Gradient -->
            <div class="absolute inset-0 bg-[#08182b]">
              <img 
                :src="slide.image" 
                :alt="slide.titleHighlight" 
                class="w-full h-full object-cover object-center transform scale-105 animate-kenburns transition-all duration-1000"
              />
              <div class="absolute inset-0 bg-gradient-to-r from-[#08182b] via-[#08182b]/85 to-[#08182b]/35"></div>
              <div class="absolute inset-0 bg-gradient-to-t from-[#08182b] via-transparent to-[#08182b]/60"></div>
            </div>

            <!-- Slide Content Overlay -->
            <div class="relative z-10 max-w-7xl mx-auto h-full px-4 sm:px-6 lg:px-8 flex flex-col justify-center">
              <div class="max-w-3xl space-y-3 sm:space-y-4 pt-2">
                
                <!-- Ribbon Badge -->
                <div class="inline-flex items-center space-x-2 px-3.5 py-1 rounded-full bg-amber-500/20 backdrop-blur-md border border-amber-400/50 text-amber-300 text-xs font-black uppercase tracking-wider shadow-sm">
                  <Sparkles class="w-3.5 h-3.5 text-amber-400 shrink-0" />
                  <span>{{ slide.badge }}</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-[1.15] font-serif">
                  {{ slide.titlePrefix }}
                  <span class="block bg-gradient-to-r from-amber-300 via-amber-200 to-yellow-400 bg-clip-text text-transparent mt-0.5">
                    {{ slide.titleHighlight }}
                  </span>
                </h1>

                <!-- Subtitle / Mission Statement -->
                <p class="text-xs sm:text-sm lg:text-base text-slate-200 leading-relaxed max-w-2xl font-normal drop-shadow-xs">
                  {{ slide.description }}
                </p>

                <!-- Core Highlights Tags -->
                <div class="flex flex-wrap items-center gap-2 pt-1">
                  <div 
                    v-for="(feat, fIdx) in slide.features" 
                    :key="fIdx"
                    class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg bg-slate-900/80 backdrop-blur-md border border-slate-700 text-[11px] font-bold text-slate-200 shadow-xs"
                  >
                    <CheckCircle class="w-3.5 h-3.5 text-amber-400 shrink-0" />
                    <span>{{ feat }}</span>
                  </div>
                </div>

                <!-- Call to Action Buttons -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                  <router-link 
                    :to="slide.primaryCtaLink" 
                    class="px-6 py-3 rounded-xl text-xs sm:text-sm font-black bg-gradient-to-r from-amber-500 via-amber-600 to-amber-500 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-lg shadow-amber-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center space-x-2 cursor-pointer border border-amber-400"
                  >
                    <component :is="slide.primaryCtaIcon" class="w-4 h-4 text-slate-950" />
                    <span>{{ slide.primaryCtaText }}</span>
                  </router-link>

                  <button 
                    @click="activeHubTab = slide.hubTab" 
                    class="px-5 py-3 rounded-xl text-xs sm:text-sm font-bold bg-slate-900/90 hover:bg-slate-800 text-slate-200 hover:text-white border border-slate-600 hover:border-amber-400 backdrop-blur-md transition-all flex items-center space-x-1.5 cursor-pointer"
                  >
                    <span>{{ slide.secondaryCtaText }}</span>
                    <ArrowRight class="w-3.5 h-3.5 text-amber-400" />
                  </button>
                </div>

              </div>
            </div>
          </div>
        </transition-group>

        <!-- Slider Arrow Navigation Controls -->
        <button 
          @click="prevSlide" 
          class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-slate-900/80 hover:bg-amber-500 text-slate-200 hover:text-slate-950 border border-slate-700 hover:border-amber-400 backdrop-blur-md flex items-center justify-center shadow-xl transition-all hover:scale-105 active:scale-95 cursor-pointer"
          aria-label="Previous Slide"
        >
          <ChevronLeft class="w-5 h-5" />
        </button>

        <button 
          @click="nextSlide" 
          class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-slate-900/80 hover:bg-amber-500 text-slate-200 hover:text-slate-950 border border-slate-700 hover:border-amber-400 backdrop-blur-md flex items-center justify-center shadow-xl transition-all hover:scale-105 active:scale-95 cursor-pointer"
          aria-label="Next Slide"
        >
          <ChevronRight class="w-5 h-5" />
        </button>

        <!-- Slider Bottom Bar: Slide Selector Tabs & Timer Controls -->
        <div class="absolute bottom-5 sm:bottom-6 inset-x-0 z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2.5">
          
          <!-- Slide Preview Buttons -->
          <div class="flex items-center space-x-2 overflow-x-auto max-w-full pb-1 sm:pb-0 scrollbar-none">
            <button 
              v-for="(slide, index) in slides" 
              :key="slide.id"
              @click="goToSlide(index)"
              :class="[
                'group px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer',
                currentSlide === index 
                  ? 'bg-amber-500 text-slate-950 font-black shadow-md shadow-amber-500/20' 
                  : 'bg-slate-900/80 text-slate-400 hover:text-slate-200 border border-slate-800'
              ]"
            >
              <span>{{ slide.shortTitle }}</span>
            </button>
          </div>

          <!-- Slide Controls: Autoplay Pause/Play & Counter -->
          <div class="flex items-center space-x-2.5 text-xs text-slate-400 bg-slate-900/90 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-slate-800">
            <button 
              @click="toggleAutoplay" 
              class="hover:text-white transition flex items-center space-x-1 cursor-pointer" 
              :title="isAutoplayPaused ? 'Resume Autoplay' : 'Pause Autoplay'"
            >
              <Play v-if="isAutoplayPaused" class="w-3.5 h-3.5 text-amber-400" />
              <Pause v-else class="w-3.5 h-3.5 text-slate-400 hover:text-white" />
              <span class="text-[10px] font-mono">{{ isAutoplayPaused ? 'Paused' : 'Auto' }}</span>
            </button>
            <span class="text-slate-600">•</span>
            <span class="font-mono font-bold text-amber-300 text-[11px]">
              0{{ currentSlide + 1 }}/0{{ slides.length }}
            </span>
          </div>

        </div>

        <!-- Animated Slide Timer Progress Bar -->
        <div class="absolute bottom-0 inset-x-0 h-1 bg-slate-800 z-30">
          <div 
            class="h-full bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-300 transition-all duration-300"
            :style="{ width: `${progressPercent}%` }"
          ></div>
        </div>
      </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 2. QUICK SHORTCUT CARDS (Compact 4-Col Grid - No Overlap)                  -->
    <!-- ========================================================================= -->
    <section class="mt-6 sm:mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Action 1: Online Admission -->
        <router-link 
          to="/register" 
          class="p-4 rounded-2xl bg-white hover:bg-slate-50 border-2 border-slate-200 hover:border-amber-500 shadow-md transition-all duration-200 hover:-translate-y-0.5 group flex items-center space-x-3.5 cursor-pointer"
        >
          <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
            <UserPlus class="w-5 h-5" />
          </div>
          <div class="min-w-0 flex-1">
            <div class="text-[10px] font-extrabold uppercase text-emerald-700">S.Y. 2026-2027 Open</div>
            <h3 class="text-sm font-bold text-[#0c2340] group-hover:text-blue-900 font-serif leading-tight">Online Admission</h3>
            <p class="text-[11px] text-slate-500 truncate">Register & submit credentials</p>
          </div>
        </router-link>

        <!-- Action 2: Student Portal -->
        <router-link 
          to="/login" 
          class="p-4 rounded-2xl bg-white hover:bg-slate-50 border-2 border-slate-200 hover:border-blue-600 shadow-md transition-all duration-200 hover:-translate-y-0.5 group flex items-center space-x-3.5 cursor-pointer"
        >
          <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
            <GraduationCap class="w-5 h-5" />
          </div>
          <div class="min-w-0 flex-1">
            <div class="text-[10px] font-extrabold uppercase text-blue-700">Enrolled Students</div>
            <h3 class="text-sm font-bold text-[#0c2340] group-hover:text-blue-900 font-serif leading-tight">Student Portal</h3>
            <p class="text-[11px] text-slate-500 truncate">Schedules, sections & SOA</p>
          </div>
        </router-link>

        <!-- Action 3: SHS Voucher Subsidy -->
        <div 
          @click="activeHubTab = 'vouchers'"
          class="p-4 rounded-2xl bg-white hover:bg-slate-50 border-2 border-slate-200 hover:border-amber-500 shadow-md transition-all duration-200 hover:-translate-y-0.5 group flex items-center space-x-3.5 cursor-pointer"
        >
          <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
            <Award class="w-5 h-5" />
          </div>
          <div class="min-w-0 flex-1">
            <div class="text-[10px] font-extrabold uppercase text-amber-700">100% Subsidy</div>
            <h3 class="text-sm font-bold text-[#0c2340] group-hover:text-blue-900 font-serif leading-tight">SHS Voucher Guide</h3>
            <p class="text-[11px] text-slate-500 truncate">Public JHS & ESC Grants</p>
          </div>
        </div>

        <!-- Action 4: Track Explorer -->
        <div 
          @click="activeHubTab = 'academics'"
          class="p-4 rounded-2xl bg-white hover:bg-slate-50 border-2 border-slate-200 hover:border-purple-500 shadow-md transition-all duration-200 hover:-translate-y-0.5 group flex items-center space-x-3.5 cursor-pointer"
        >
          <div class="w-11 h-11 rounded-xl bg-purple-50 border border-purple-200 text-purple-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
            <BookOpen class="w-5 h-5" />
          </div>
          <div class="min-w-0 flex-1">
            <div class="text-[10px] font-extrabold uppercase text-purple-700">JHS & SHS Only</div>
            <h3 class="text-sm font-bold text-[#0c2340] group-hover:text-blue-900 font-serif leading-tight">Academic Strands</h3>
            <p class="text-[11px] text-slate-500 truncate">JHS, STEM, ABM, TVL</p>
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
              <span>1. Academics (JHS & SHS)</span>
            </button>

            <button 
              @click="activeHubTab = 'pathway'"
              :class="activeHubTab === 'pathway' ? 'bg-[#0c2340] text-amber-400 font-black shadow-sm' : 'text-slate-600 hover:text-slate-900 font-bold'"
              class="px-4 py-2 rounded-xl text-xs transition flex items-center space-x-1.5 cursor-pointer"
            >
              <Layers class="w-3.5 h-3.5" />
              <span>2. Admission Pathway</span>
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
            <h3 class="text-lg font-black text-[#0c2340] font-serif">Official 7-Stage Admission Pathway</h3>
            <p class="text-xs text-slate-500">Step-by-step walkthrough from registration to official student ID creation</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div 
              v-for="(step, idx) in workflowSteps" 
              :key="idx" 
              class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between"
            >
              <div>
                <div class="flex items-center justify-between mb-2">
                  <div class="w-7 h-7 rounded-lg bg-[#0c2340] text-amber-400 flex items-center justify-center text-xs font-black">
                    {{ idx + 1 }}
                  </div>
                  <span class="text-[10px] uppercase font-bold text-slate-400">{{ step.tag }}</span>
                </div>
                <h4 class="font-bold text-xs text-[#0c2340] font-serif">{{ step.title }}</h4>
                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">{{ step.desc }}</p>
              </div>
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
                Public Junior High School Completers automatically qualify for a 100% DepEd voucher subsidy value (₱17,500 – ₱22,500/year). Zero tuition top-up options are available on selected strands.
              </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-slate-700">
              <div class="p-3 rounded-xl bg-slate-900 border border-amber-400/40 text-center">
                <div class="text-xs text-amber-400 font-bold">Public JHS Completers</div>
                <div class="text-2xl font-black font-mono mt-1">100% Grant</div>
                <div class="text-[10px] text-slate-400 mt-0.5">No Voucher Code Needed</div>
              </div>
              <div class="p-3 rounded-xl bg-slate-900 border border-slate-700 text-center">
                <div class="text-xs text-blue-300 font-bold">Private ESC Grantees</div>
                <div class="text-2xl font-black font-mono mt-1">80% Subsidy</div>
                <div class="text-[10px] text-slate-400 mt-0.5">ESC Certificate Applied</div>
              </div>
              <div class="p-3 rounded-xl bg-slate-900 border border-slate-700 text-center">
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
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
              <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">Grade 7 (JHS)</span>
              <h4 class="font-bold text-sm text-[#0c2340] font-serif mt-2 mb-2">Junior High Requirements</h4>
              <ul class="text-xs text-slate-600 space-y-1.5">
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>Original PSA Birth Certificate</span></li>
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>Grade 6 SF9 Report Card</span></li>
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>Good Moral Certificate</span></li>
                <li class="flex items-center space-x-1.5"><Check class="w-3.5 h-3.5 text-emerald-600" /> <span>2 pcs 2x2 ID Photos</span></li>
              </ul>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border-2 border-amber-400">
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

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
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
  ChevronLeft, 
  ChevronRight, 
  Play, 
  Pause, 
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
  Briefcase
} from 'lucide-vue-next';
import { getRoleRouteName } from '../../router';
import api from '../../services/api';

const router = useRouter();
const route = useRoute();

// ============================================================================
// HERO SLIDER STATE & DATA
// ============================================================================
const currentSlide = ref(0);
const autoplayInterval = ref(null);
const isAutoplayPaused = ref(false);
const progressPercent = ref(0);
const progressTimer = ref(null);
const SLIDE_DURATION = 5500;

const activeHubTab = ref('academics'); // 'academics', 'pathway', 'vouchers', 'requirements', 'facilities', 'faqs'

// Modal State
const showStrandModal = ref(false);
const selectedModalStrand = ref(null);

const slides = [
  {
    id: 1,
    shortTitle: '01 Campus',
    badge: 'ENROLLMENT FOR S.Y. 2026-2027 IS OFFICIALLY OPEN',
    titlePrefix: 'Welcome to JJKINGS Biringan School:',
    titleHighlight: 'Where Academic Excellence Meets Character',
    description: 'A distinguished DepEd-recognized Junior & Senior High School (JHS & SHS) committed to nurturing competitive students through modern science laboratories and values-centered formation in Biringan City.',
    features: ['DepEd School ID: 405621', '100% DepEd Voucher Subsidy', 'Junior & Senior High School Only'],
    image: 'https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?q=80&w=1600&auto=format&fit=crop',
    primaryCtaText: 'Apply for Admission',
    primaryCtaLink: '/register',
    primaryCtaIcon: UserPlus,
    secondaryCtaText: 'Academic Offerings',
    hubTab: 'academics'
  },
  {
    id: 2,
    shortTitle: '02 STEM Labs',
    badge: 'Senior High School Academic Track Specialization',
    titlePrefix: 'STEM & Advanced Sciences:',
    titleHighlight: 'Inspiring Future Engineers, Doctors & Scientists',
    description: 'Equipped with dedicated physics, chemistry, biology, and robotics research laboratories empowering students with rigorous capstone scientific investigation.',
    features: ['Robotics & Science Labs', 'Pre-Calculus & Physics', 'Senior High Academic Excellence'],
    image: 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?q=80&w=1600&auto=format&fit=crop',
    primaryCtaText: 'Explore STEM',
    primaryCtaLink: '/register',
    primaryCtaIcon: Compass,
    secondaryCtaText: 'View All Strands',
    hubTab: 'academics'
  },
  {
    id: 3,
    shortTitle: '03 Vouchers',
    badge: '100% Government Tuition Subsidy Program',
    titlePrefix: 'DepEd Senior High Voucher:',
    titleHighlight: 'Quality Private Education with 100% Voucher Subsidy',
    description: 'Public Junior High School Completers receive full 100% tuition subsidy. Zero base tuition top-up on selected strands. Private ESC and QVR grantees welcome.',
    features: ['Up to ₱22,500 Yearly Grant', 'No Entrance Fees', 'Automatic Voucher Crediting'],
    image: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=1600&auto=format&fit=crop',
    primaryCtaText: 'Apply with Voucher',
    primaryCtaLink: '/register',
    primaryCtaIcon: Award,
    secondaryCtaText: 'Voucher Guide',
    hubTab: 'vouchers'
  },
  {
    id: 4,
    shortTitle: '04 TVL Hubs',
    badge: 'Technical-Vocational-Livelihood Track',
    titlePrefix: 'TVL-ICT & Culinary Arts:',
    titleHighlight: 'Job-Ready TESDA National Certifications (NC II)',
    description: 'Hands-on training in Computer Systems Servicing (CSS), Web Programming, Commercial Cookery, and Hotel Hospitality with high-tech PC/Mac hubs and culinary kitchens.',
    features: ['TESDA NC II Assessment', 'Industry Immersion', 'Modern PC & Culinary Labs'],
    image: 'https://images.unsplash.com/photo-1531482615713-2afd69097998?q=80&w=1600&auto=format&fit=crop',
    primaryCtaText: 'Explore TVL',
    primaryCtaLink: '/register',
    primaryCtaIcon: Cpu,
    secondaryCtaText: 'Admission Pathway',
    hubTab: 'pathway'
  },
  {
    id: 5,
    shortTitle: '05 Campus Life',
    badge: 'Holistic Student Formation & Athletics',
    titlePrefix: 'Vibrant Campus Community:',
    titleHighlight: 'Digital Library, Sports Complex & Leadership Guilds',
    description: 'Nurturing well-rounded intellect, faith, and leadership through quiet research commons, varsity athletics, performing arts, and community service organizations.',
    features: ['Academic Library Commons', 'Championship Varsity Sports', 'Christian Leadership Guilds'],
    image: 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=1600&auto=format&fit=crop',
    primaryCtaText: 'Join JJKINGS Biringan School',
    primaryCtaLink: '/register',
    primaryCtaIcon: UserPlus,
    secondaryCtaText: 'Campus Facilities',
    hubTab: 'facilities'
  }
];

// Slider Methods
const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % slides.length;
  resetProgress();
};

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length;
  resetProgress();
};

const goToSlide = (index) => {
  currentSlide.value = index;
  resetProgress();
};

const pauseSlider = () => {
  isAutoplayPaused.value = true;
};

const resumeSlider = () => {
  isAutoplayPaused.value = false;
};

const toggleAutoplay = () => {
  isAutoplayPaused.value = !isAutoplayPaused.value;
};

const resetProgress = () => {
  progressPercent.value = 0;
};

const startSliderLoop = () => {
  const stepMs = 50;
  const increment = (stepMs / SLIDE_DURATION) * 100;

  progressTimer.value = setInterval(() => {
    if (!isAutoplayPaused.value) {
      progressPercent.value += increment;
      if (progressPercent.value >= 100) {
        nextSlide();
      }
    }
  }, stepMs);
};

// ============================================================================
// ACADEMIC DATA (JHS & SHS ONLY - NO COLLEGE TOPICS)
// ============================================================================
const selectedTrackCategory = ref('all');

const workflowSteps = [
  { title: '1. Online Registration', desc: 'Create applicant account & receive reference code.', tag: 'Applicant' },
  { title: '2. Program & Track', desc: 'Select JHS or SHS Track, LRN, and parent info.', tag: 'Applicant' },
  { title: '3. Upload Credentials', desc: 'Upload scans of PSA Birth Cert, SF9, & Good Moral.', tag: 'Verification' },
  { title: '4. Registrar Review', desc: 'Registrar validates credentials and checks status.', tag: 'Registrar' },
  { title: '5. Section & Schedule', desc: 'Assign section seat, timetable schedule, and queue.', tag: 'Coordinator' },
  { title: '6. Assessment & Fees', desc: 'DepEd Voucher applied. Pay downpayment online/walk-in.', tag: 'Treasury' },
  { title: '7. Official Enrolled', desc: 'Permanent Student ID issued with portal access.', tag: 'Student' }
];

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
  // Store chosen strand into storage so it automatically pre-populates
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
      // If user is already logged in as applicant, automatically update their application with this strand!
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

  // Not logged in: Route directly to registration with pre-selected strand
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

  startSliderLoop();

  // Handle direct tab switching event from header navigation
  const onSwitchTabEvent = (e) => {
    if (e.detail && ['academics', 'pathway', 'vouchers', 'requirements', 'facilities', 'faqs'].includes(e.detail)) {
      activeHubTab.value = e.detail;
    }
  };
  window.addEventListener('switch-home-tab', onSwitchTabEvent);

  // Check if routed with query tab or sessionStorage
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

onUnmounted(() => {
  if (progressTimer.value) clearInterval(progressTimer.value);
  if (autoplayInterval.value) clearInterval(autoplayInterval.value);
});
</script>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: opacity 0.7s ease, transform 0.7s ease;
}

.slide-fade-enter-from {
  opacity: 0;
  transform: scale(1.02);
}

.slide-fade-leave-to {
  opacity: 0;
  transform: scale(0.98);
}

@keyframes kenburns {
  0% { transform: scale(1); }
  50% { transform: scale(1.04); }
  100% { transform: scale(1); }
}

.animate-kenburns {
  animation: kenburns 22s infinite ease-in-out;
}
</style>
