<template>
  <div class="min-h-screen bg-slate-900 text-white selection:bg-emerald-500 selection:text-white">
    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-24 bg-gradient-to-b from-navy-950 via-slate-900 to-slate-950 border-b border-slate-800">
      <!-- Background glow elements -->
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute top-1/2 -left-40 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto">
          <!-- DepEd Badge -->
          <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-emerald-950/80 border border-emerald-500/30 text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-6 shadow-inner">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span>DepEd K-12 & MATATAG Curriculum • SY 2026-2027</span>
          </div>

          <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white leading-tight mb-6">
            Empowering Future Leaders through
            <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 bg-clip-text text-transparent">
              Excellence & Innovation
            </span>
          </h1>

          <p class="text-lg text-slate-300 mb-10 leading-relaxed">
            Welcome to the Official Web-Based Admission & Enrollment Portal for Junior High School (Grades 7–10) and Senior High School (Academic & TVL Tracks). Fast, paperless, and transparent.
          </p>

          <!-- Primary CTAs -->
          <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <router-link to="/register" class="w-full sm:w-auto px-8 py-4 rounded-xl text-base font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white shadow-xl shadow-emerald-600/30 hover:scale-[1.02] transition flex items-center justify-center space-x-2">
              <UserPlus class="w-5 h-5" />
              <span>Apply for Admission</span>
            </router-link>
            <router-link to="/login" class="w-full sm:w-auto px-8 py-4 rounded-xl text-base font-semibold bg-slate-800/90 hover:bg-slate-700/90 text-slate-200 border border-slate-700 hover:border-slate-600 transition flex items-center justify-center space-x-2">
              <LogIn class="w-5 h-5" />
              <span>Applicant / Staff Portal</span>
            </router-link>
          </div>
        </div>

        <!-- Admission Workflow Stepper Visualization -->
        <div class="mt-20 p-8 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-2xl backdrop-blur-xl">
          <div class="text-center mb-8">
            <h2 class="text-xs uppercase tracking-widest text-emerald-400 font-bold">Standard Admission & Enrollment Lifecycle</h2>
            <p class="text-xl font-bold text-white mt-1">From Application to Official Enrolled Student</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-7 gap-3 relative">
            <div v-for="(step, idx) in workflowSteps" :key="idx" class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60 hover:border-emerald-500/50 transition group">
              <div class="w-8 h-8 rounded-xl bg-emerald-950 border border-emerald-500/30 text-emerald-400 flex items-center justify-center font-bold text-xs mb-3 group-hover:scale-110 transition">
                {{ idx + 1 }}
              </div>
              <h3 class="font-bold text-xs text-white mb-1 leading-snug">{{ step.title }}</h3>
              <p class="text-[11px] text-slate-400 leading-tight">{{ step.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Junior & Senior High Offerings Explorer -->
    <section class="py-20 bg-slate-950 border-b border-slate-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
          <h2 class="text-xs uppercase tracking-widest text-emerald-400 font-bold">Academic Programs</h2>
          <h3 class="text-3xl font-extrabold text-white mt-2">Junior High & Senior High Strands</h3>
          <p class="text-sm text-slate-400 mt-3">Explore our DepEd-recognized curriculum offerings tailored to academic excellence and TESDA NC II certifications.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Junior High School Card -->
          <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/40 transition">
            <div class="w-12 h-12 rounded-xl bg-blue-950 border border-blue-500/30 text-blue-400 flex items-center justify-center mb-4">
              <BookOpen class="w-6 h-6" />
            </div>
            <div class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-950 text-blue-300 border border-blue-800 mb-2">
              GRADES 7 TO 10
            </div>
            <h4 class="text-lg font-bold text-white mb-2">Junior High School (JHS)</h4>
            <p class="text-xs text-slate-400 mb-4 leading-relaxed">
              Comprehensive DepEd K-12 and MATATAG curriculum covering English, Math, Integrated Science, AP, EsP, TLE, and MAPEH.
            </p>
            <ul class="text-xs text-slate-300 space-y-1.5">
              <li class="flex items-center space-x-2"><CheckCircle class="w-3.5 h-3.5 text-emerald-400" /> <span>DepEd ESC Subsidy Eligible</span></li>
              <li class="flex items-center space-x-2"><CheckCircle class="w-3.5 h-3.5 text-emerald-400" /> <span>Holistic TLE Skill Tracks</span></li>
            </ul>
          </div>

          <!-- SHS STEM -->
          <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/40 transition">
            <div class="w-12 h-12 rounded-xl bg-emerald-950 border border-emerald-500/30 text-emerald-400 flex items-center justify-center mb-4">
              <Compass class="w-6 h-6" />
            </div>
            <div class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-950 text-emerald-300 border border-emerald-800 mb-2">
              ACADEMIC TRACK
            </div>
            <h4 class="text-lg font-bold text-white mb-2">STEM (Science, Tech & Math)</h4>
            <p class="text-xs text-slate-400 mb-4 leading-relaxed">
              Specialized for aspiring Engineers, Scientists, Doctors, and Architects with Pre-Calculus, Chemistry, and Physics.
            </p>
            <ul class="text-xs text-slate-300 space-y-1.5">
              <li class="flex items-center space-x-2"><CheckCircle class="w-3.5 h-3.5 text-emerald-400" /> <span>Advanced Research & Robotics</span></li>
              <li class="flex items-center space-x-2"><CheckCircle class="w-3.5 h-3.5 text-emerald-400" /> <span>DepEd SHS Voucher Accepted</span></li>
            </ul>
          </div>

          <!-- SHS ABM -->
          <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/40 transition">
            <div class="w-12 h-12 rounded-xl bg-amber-950 border border-amber-500/30 text-amber-400 flex items-center justify-center mb-4">
              <TrendingUp class="w-6 h-6" />
            </div>
            <div class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-950 text-amber-300 border border-amber-800 mb-2">
              ACADEMIC TRACK
            </div>
            <h4 class="text-lg font-bold text-white mb-2">ABM (Accountancy & Business)</h4>
            <p class="text-xs text-slate-400 mb-4 leading-relaxed">
              Designed for future CPAs, Entrepreneurs, and Corporate Leaders with Fundamentals of Accounting and Business Finance.
            </p>
            <ul class="text-xs text-slate-300 space-y-1.5">
              <li class="flex items-center space-x-2"><CheckCircle class="w-3.5 h-3.5 text-emerald-400" /> <span>Business Enterprise Simulation</span></li>
              <li class="flex items-center space-x-2"><CheckCircle class="w-3.5 h-3.5 text-emerald-400" /> <span>Principles of Marketing</span></li>
            </ul>
          </div>

          <!-- SHS HUMSS -->
          <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/40 transition">
            <div class="w-12 h-12 rounded-xl bg-purple-950 border border-purple-500/30 text-purple-400 flex items-center justify-center mb-4">
              <Users class="w-6 h-6" />
            </div>
            <div class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-950 text-purple-300 border border-purple-800 mb-2">
              ACADEMIC TRACK
            </div>
            <h4 class="text-lg font-bold text-white mb-2">HUMSS (Humanities & Social)</h4>
            <p class="text-xs text-slate-400 mb-4 leading-relaxed">
              Ideal for future Lawyers, Journalists, Educators, and Diplomats with Creative Writing, Philippine Politics, and Sociology.
            </p>
            <ul class="text-xs text-slate-300 space-y-1.5">
              <li class="flex items-center space-x-2"><CheckCircle class="w-3.5 h-3.5 text-emerald-400" /> <span>Community Engagement Fieldwork</span></li>
              <li class="flex items-center space-x-2"><CheckCircle class="w-3.5 h-3.5 text-emerald-400" /> <span>Debate & Public Speaking</span></li>
            </ul>
          </div>

          <!-- SHS TVL-ICT -->
          <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/40 transition">
            <div class="w-12 h-12 rounded-xl bg-cyan-950 border border-cyan-500/30 text-cyan-400 flex items-center justify-center mb-4">
              <Cpu class="w-6 h-6" />
            </div>
            <div class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-950 text-cyan-300 border border-cyan-800 mb-2">
              TVL TRACK
            </div>
            <h4 class="text-lg font-bold text-white mb-2">TVL - ICT (Computer & Programming)</h4>
            <p class="text-xs text-slate-400 mb-4 leading-relaxed">
              Equips students with hands-on Computer System Servicing (CSS), Web Development, Java/C# programming, and networking.
            </p>
            <ul class="text-xs text-slate-300 space-y-1.5">
              <li class="flex items-center space-x-2"><CheckCircle class="w-3.5 h-3.5 text-emerald-400" /> <span>TESDA National Certificate (NC II)</span></li>
              <li class="flex items-center space-x-2"><CheckCircle class="w-3.5 h-3.5 text-emerald-400" /> <span>Direct Tech Industry Immersion</span></li>
            </ul>
          </div>

          <!-- SHS TVL-HE -->
          <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/40 transition">
            <div class="w-12 h-12 rounded-xl bg-rose-950 border border-rose-500/30 text-rose-400 flex items-center justify-center mb-4">
              <Coffee class="w-6 h-6" />
            </div>
            <div class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-950 text-rose-300 border border-rose-800 mb-2">
              TVL TRACK
            </div>
            <h4 class="text-lg font-bold text-white mb-2">TVL - Home Economics (HE)</h4>
            <p class="text-xs text-slate-400 mb-4 leading-relaxed">
              Focuses on Culinary Arts, Bread & Pastry Production, Food & Beverage Services, and Hotel & Tourism Management.
            </p>
            <ul class="text-xs text-slate-300 space-y-1.5">
              <li class="flex items-center space-x-2"><CheckCircle class="w-3.5 h-3.5 text-emerald-400" /> <span>Commercial Kitchen Workshops</span></li>
              <li class="flex items-center space-x-2"><CheckCircle class="w-3.5 h-3.5 text-emerald-400" /> <span>TESDA Cookery / BPP NC II</span></li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- DepEd Voucher Subsidy Notice -->
    <section class="py-16 bg-slate-900 border-b border-slate-800">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="p-8 rounded-3xl bg-gradient-to-r from-emerald-950/70 to-slate-900 border border-emerald-500/30 flex flex-col md:flex-row items-center justify-between gap-6">
          <div>
            <div class="text-xs font-bold uppercase tracking-wider text-emerald-400 mb-1">Government Assistance</div>
            <h3 class="text-2xl font-bold text-white">Senior High School Voucher Program (SHS-VP)</h3>
            <p class="text-sm text-slate-300 mt-2 max-w-2xl leading-relaxed">
              Public Junior High School Completers automatically receive a 100% voucher subsidy value (up to ₱17,500 - ₱22,500 depending on location). Private ESC Grantees and QVR applicants are also accepted.
            </p>
          </div>
          <router-link to="/register" class="whitespace-nowrap px-6 py-3.5 rounded-xl text-sm font-bold bg-emerald-500 hover:bg-emerald-400 text-navy-950 shadow-lg shadow-emerald-500/20 transition">
            Apply with Voucher
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { 
  UserPlus, LogIn, BookOpen, Compass, TrendingUp, Users, Cpu, Coffee, CheckCircle 
} from 'lucide-vue-next';
import { getRoleRouteName } from '../../router';

const router = useRouter();

const workflowSteps = [
  { title: '1. Home / Apply', desc: 'Initiate online application' },
  { title: '2. Temp Account', desc: 'Receive reference number' },
  { title: '3. Submit Docs', desc: 'Upload PSA, SF9, Good Moral' },
  { title: '4. Registrar Check', desc: 'Requirement verification' },
  { title: '5. Enrollment Queue', desc: 'Pre-enrollment slip & section' },
  { title: '6. Treasury Pay', desc: 'Cashier OR & Voucher applied' },
  { title: '7. Student Portal', desc: 'Permanent Student ID issued' }
];

onMounted(() => {
  const token = localStorage.getItem('sia_auth_token');
  const userJson = localStorage.getItem('sia_auth_user');
  if (token && userJson) {
    try {
      const user = JSON.parse(userJson);
      const target = getRoleRouteName(user.role_slug);
      if (target !== 'Home') {
        router.replace({ name: target });
      }
    } catch (e) {
      // Ignore parse error
    }
  }
});
</script>
