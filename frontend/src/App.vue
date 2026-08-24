<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800 font-sans antialiased">
    
    <!-- ========================================================================= -->
    <!-- 1. TOP INSTITUTIONAL BAR (School Hotlines, DepEd ID & Portals)             -->
    <!-- ========================================================================= -->
    <div class="no-print bg-[#0a192f] text-slate-200 border-b border-[#1e293b] text-xs py-2 px-4 sm:px-8 lg:px-12 xl:px-16">
      <div class="max-w-[1680px] 2xl:max-w-[1780px] w-full mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
        
        <!-- Left: Official DepEd & School Accreditation Info -->
        <div class="flex items-center space-x-3 text-[11px]">
          <span class="inline-flex items-center space-x-1.5 text-amber-400 font-bold">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
            <span>DepEd School ID: 405621</span>
          </span>
          <span class="text-slate-600 hidden md:inline">•</span>
          <span class="text-slate-300 hidden md:inline">PEAC-ESC & DepEd Voucher Accredited</span>
          <span class="text-slate-600 hidden lg:inline">•</span>
          <span class="text-slate-400 hidden lg:inline">Biringan City, Samar, Philippines</span>
        </div>

        <!-- Right: Admissions Hotline & Quick Gateways -->
        <div class="flex items-center space-x-4 text-[11px]">
          <div class="hidden sm:flex items-center space-x-1 text-slate-300">
            <Phone class="w-3 h-3 text-amber-400" />
            <span>Admissions: <strong>(055) 888-7766</strong> / <strong>0917-111-0001</strong></span>
          </div>
          <span class="text-slate-700 hidden sm:inline">|</span>
          <router-link to="/smtp-simulator" class="text-amber-400 hover:text-amber-300 transition flex items-center space-x-1 font-bold">
            <Mail class="w-3 h-3 text-amber-400" />
            <span>SMTP Simulator</span>
          </router-link>
        </div>

      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. MAIN SCHOOL HEADER (Official School Crest, Typography & Navigation)    -->
    <!-- ========================================================================= -->
    <header class="no-print bg-white text-slate-900 border-b-2 border-slate-200 shadow-md sticky top-0 z-50">
      <div class="max-w-[1680px] 2xl:max-w-[1780px] w-full mx-auto px-4 sm:px-8 lg:px-12 xl:px-16 h-20 flex items-center justify-between">
        
        <!-- School Crest & Official Branding -->
        <router-link :to="dashboardRoute" class="flex items-center space-x-3.5 group cursor-pointer">
          <!-- Heraldic Academic Crest Emblem -->
          <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#0c2340] via-[#163860] to-[#0c2340] p-1 border-2 border-amber-400 flex items-center justify-center shadow-lg shadow-blue-950/20 group-hover:scale-105 transition-all shrink-0">
            <div class="w-full h-full rounded-xl flex flex-col items-center justify-center text-center">
              <GraduationCap class="w-6 h-6 text-amber-400" />
              <span class="text-[7px] font-black text-amber-300 uppercase tracking-tighter leading-none mt-0.5">EST. 2012</span>
            </div>
          </div>

          <!-- Institutional Title -->
          <div>
            <div class="flex items-center space-x-2">
              <span class="font-black text-lg sm:text-xl tracking-tight text-[#0c2340] font-serif group-hover:text-blue-900 transition block">
                JJKINGS BIRINGAN SCHOOL
              </span>
              <span class="hidden sm:inline-block px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-300">
                BIRINGAN
              </span>
            </div>
            <span class="block text-[10px] sm:text-[11px] text-slate-500 font-bold uppercase tracking-wider">
              Junior High School & Senior High School (JHS & SHS Only)
            </span>
          </div>
        </router-link>

        <!-- Academic Navigation Links (Desktop) -->
        <nav class="hidden lg:flex items-center space-x-1">
          <!-- Public Navigation Links when NOT logged in -->
          <template v-if="!currentUser">
            <router-link 
              to="/" 
              class="px-3.5 py-2 rounded-xl text-xs font-extrabold text-slate-700 hover:text-[#0c2340] hover:bg-slate-100 transition"
              active-class="text-blue-900 bg-blue-50 font-black border-b-2 border-blue-900 rounded-b-none"
            >
              Home
            </router-link>
            <button 
              type="button" 
              @click="navigateToHomeTab('academics')"
              :class="route.name === 'Home' && currentActiveHomeTab === 'academics' ? 'text-blue-950 bg-blue-100/80 font-black shadow-xs' : 'text-slate-700 hover:text-[#0c2340] hover:bg-slate-100 font-bold'"
              class="px-3.5 py-2 rounded-xl text-xs transition cursor-pointer"
            >
              Academics & Strands
            </button>
            <button 
              type="button" 
              @click="navigateToHomeTab('pathway')"
              :class="route.name === 'Home' && currentActiveHomeTab === 'pathway' ? 'text-blue-950 bg-blue-100/80 font-black shadow-xs' : 'text-slate-700 hover:text-[#0c2340] hover:bg-slate-100 font-bold'"
              class="px-3.5 py-2 rounded-xl text-xs transition cursor-pointer"
            >
              Admission Steps
            </button>
            <button 
              type="button" 
              @click="navigateToHomeTab('vouchers')"
              :class="route.name === 'Home' && currentActiveHomeTab === 'vouchers' ? 'text-blue-950 bg-blue-100/80 font-black shadow-xs' : 'text-slate-700 hover:text-[#0c2340] hover:bg-slate-100 font-bold'"
              class="px-3.5 py-2 rounded-xl text-xs transition cursor-pointer flex items-center space-x-1"
            >
              <Award class="w-3.5 h-3.5 text-amber-600" />
              <span>SHS Vouchers</span>
            </button>
            <button 
              type="button" 
              @click="navigateToHomeTab('facilities')"
              :class="route.name === 'Home' && currentActiveHomeTab === 'facilities' ? 'text-blue-950 bg-blue-100/80 font-black shadow-xs' : 'text-slate-700 hover:text-[#0c2340] hover:bg-slate-100 font-bold'"
              class="px-3.5 py-2 rounded-xl text-xs transition cursor-pointer"
            >
              Campus Facilities
            </button>
          </template>

          <!-- Dynamic Role Portals when LOGGED IN -->
          <template v-if="currentUser">
            <router-link v-if="currentUser.role_slug === 'applicant'" to="/admission" class="px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 transition flex items-center space-x-1.5">
              <UserPlus class="w-3.5 h-3.5 text-emerald-700" />
              <span>My Admission Portal</span>
            </router-link>
            <router-link v-if="['registrar', 'admin'].includes(currentUser.role_slug)" to="/registrar" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 hover:text-blue-900 hover:bg-slate-100 transition">
              Registrar
            </router-link>
            <router-link v-if="['treasury', 'admin'].includes(currentUser.role_slug)" to="/treasury" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 hover:text-blue-900 hover:bg-slate-100 transition">
              Treasury / Cashier
            </router-link>
            <router-link v-if="['coordinator', 'admin'].includes(currentUser.role_slug)" to="/coordinator" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 hover:text-blue-900 hover:bg-slate-100 transition">
              Coordinator
            </router-link>
            <router-link v-if="['records', 'admin', 'registrar'].includes(currentUser.role_slug)" to="/records" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 hover:text-blue-900 hover:bg-slate-100 transition">
              School Records
            </router-link>
            <router-link v-if="currentUser.role_slug === 'student'" to="/student" class="px-3.5 py-2 rounded-xl text-xs font-bold text-blue-900 bg-blue-50 border border-blue-300 hover:bg-blue-100 transition flex items-center space-x-1.5">
              <GraduationCap class="w-3.5 h-3.5 text-blue-700" />
              <span>Student Portal</span>
            </router-link>
            <router-link v-if="currentUser.role_slug === 'admin'" to="/admin" class="px-3.5 py-2 rounded-xl text-xs font-bold text-amber-900 bg-amber-50 border border-amber-300 hover:bg-amber-100 transition">
              Admin Control
            </router-link>
          </template>
        </nav>

        <!-- Right Header Action CTAs -->
        <div class="flex items-center space-x-2.5 sm:space-x-3">
          
          <!-- When User is Authenticated -->
          <template v-if="currentUser">
            <div class="flex items-center space-x-2.5 bg-slate-100 px-3 py-1.5 rounded-2xl border border-slate-200">
              <div class="w-8 h-8 rounded-xl bg-[#0c2340] flex items-center justify-center text-xs font-bold text-amber-400 shadow-sm">
                {{ userInitials }}
              </div>
              <div class="hidden sm:block text-left text-xs">
                <div class="font-bold text-slate-900">{{ currentUser.first_name || currentUser.username }}</div>
                <div class="text-[10px] text-blue-700 font-bold uppercase tracking-wider">{{ currentUser.role_name || currentUser.role_slug }}</div>
              </div>
            </div>
            <button @click="showLogoutConfirm = true" class="p-2.5 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition cursor-pointer" title="Sign Out">
              <LogOut class="w-4 h-4" />
            </button>
          </template>

          <!-- When User is Guest / Public Visitor -->
          <template v-else>
            <router-link 
              to="/login" 
              class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-[#0c2340] hover:text-blue-900 hover:bg-blue-50 border border-slate-300 hover:border-blue-300 transition flex items-center space-x-1.5 cursor-pointer shadow-xs"
            >
              <UserCheck class="w-3.5 h-3.5 text-blue-700" />
              <span>Student Portal</span>
            </router-link>

            <router-link 
              to="/register" 
              class="px-4 py-2 rounded-xl text-xs sm:text-sm font-black bg-gradient-to-r from-amber-500 via-amber-600 to-amber-500 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-md shadow-amber-500/25 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center space-x-1.5 cursor-pointer border border-amber-400"
            >
              <span>Apply for S.Y. 2026-2027</span>
              <ArrowRight class="w-3.5 h-3.5 text-slate-950" />
            </router-link>
          </template>

        </div>

      </div>
    </header>

    <!-- ========================================================================= -->
    <!-- 3. MAIN ROUTER VIEW OUTLET                                                -->
    <!-- ========================================================================= -->
    <main class="flex-1">
      <router-view />
    </main>

    <!-- ========================================================================= -->
    <!-- 4. LOGOUT CONFIRMATION MODAL                                              -->
    <!-- ========================================================================= -->
    <div v-if="showLogoutConfirm" class="no-print fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-200 text-slate-900">
        <div class="w-12 h-12 rounded-2xl bg-rose-100 border border-rose-200 text-rose-600 flex items-center justify-center mx-auto mb-4 shadow-sm">
          <LogOut class="w-6 h-6" />
        </div>
        <div class="text-center">
          <h3 class="text-base font-extrabold text-slate-900">Confirm Sign Out</h3>
          <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
            Are you sure you want to end your current session? Any unsaved form progress will be lost.
          </p>
        </div>

        <div class="flex items-center space-x-2.5 mt-6">
          <button 
            type="button" 
            @click="showLogoutConfirm = false" 
            class="w-1/2 py-2.5 px-4 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer"
          >
            Cancel
          </button>
          <button 
            type="button" 
            @click="confirmLogout" 
            class="w-1/2 py-2.5 px-4 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-500 text-white shadow-md shadow-rose-600/20 transition flex items-center justify-center space-x-1.5 cursor-pointer"
          >
            <LogOut class="w-3.5 h-3.5" />
            <span>Sign Out</span>
          </button>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. INSTITUTIONAL SCHOOL FOOTER (DepEd Aligned & Complete Contact)         -->
    <!-- ========================================================================= -->
    <footer class="no-print bg-[#08182b] text-slate-300 border-t-4 border-amber-500 py-12 text-xs">
      <div class="max-w-[1680px] 2xl:max-w-[1780px] w-full mx-auto px-4 sm:px-8 lg:px-12 xl:px-16">
        
        <!-- 4-Column Academic Footer Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-slate-800">
          
          <!-- Column 1: School Identity & DepEd Accreditation -->
          <div class="space-y-3.5">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-xl bg-[#0c2340] border border-amber-400 flex items-center justify-center text-amber-400 shrink-0">
                <GraduationCap class="w-6 h-6" />
              </div>
              <div>
                <h4 class="text-sm font-black text-white font-serif tracking-tight">JJKINGS BIRINGAN SCHOOL</h4>
                <p class="text-[10px] text-amber-400 font-bold uppercase">Junior & Senior High School (JHS & SHS Only)</p>
              </div>
            </div>
            <p class="text-slate-400 text-xs leading-relaxed">
              A premier values-centered educational institution offering Philippine Department of Education (DepEd) K to 12 Junior High and Senior High School Academic & TVL tracks.
            </p>
            <div class="pt-1 text-[11px] text-slate-400 space-y-1">
              <div><strong>DepEd School ID:</strong> 405621</div>
              <div><strong>Accreditation:</strong> PEAC-ESC & DepEd Voucher Program</div>
            </div>
          </div>

          <!-- Column 2: Academic Programs -->
          <div class="space-y-2.5">
            <h5 class="text-xs font-black uppercase text-amber-400 tracking-wider font-serif">Academic Offerings (JHS & SHS)</h5>
            <ul class="text-slate-400 space-y-1.5 text-xs">
              <li><router-link to="/register" class="hover:text-white transition">Junior High School (Grades 7–10)</router-link></li>
              <li><router-link to="/register" class="hover:text-white transition">STEM (Science, Tech, Engineering & Math)</router-link></li>
              <li><router-link to="/register" class="hover:text-white transition">ABM (Accountancy, Business & Management)</router-link></li>
              <li><router-link to="/register" class="hover:text-white transition">HUMSS (Humanities & Social Sciences)</router-link></li>
              <li><router-link to="/register" class="hover:text-white transition">GAS (General Academic Strand)</router-link></li>
              <li><router-link to="/register" class="hover:text-white transition">TVL-ICT (Computer Systems & Programming)</router-link></li>
              <li><router-link to="/register" class="hover:text-white transition">TVL-HE (Cookery, Pastry & Tourism)</router-link></li>
            </ul>
          </div>

          <!-- Column 3: Admissions & Portal Gateways -->
          <div class="space-y-2.5">
            <h5 class="text-xs font-black uppercase text-amber-400 tracking-wider font-serif">Admissions & Portals</h5>
            <ul class="text-slate-400 space-y-1.5 text-xs">
              <li><router-link to="/register" class="hover:text-white transition">Online Admission Application</router-link></li>
              <li><router-link to="/login" class="hover:text-white transition">Student Portal Access</router-link></li>
              <li><a href="#/ #vouchers" @click.prevent="navigateToHomeTab('vouchers')" class="hover:text-white transition cursor-pointer">DepEd SHS Voucher Subsidy (100%)</a></li>
              <li><a href="#/ #requirements" @click.prevent="navigateToHomeTab('requirements')" class="hover:text-white transition cursor-pointer">Required Documents Checklist</a></li>
              <li><router-link to="/staff-login" class="hover:text-white transition">Registrar & Treasury Staff Portal</router-link></li>
            </ul>
          </div>

          <!-- Column 4: Campus Location & Office Hours -->
          <div class="space-y-2.5">
            <h5 class="text-xs font-black uppercase text-amber-400 tracking-wider font-serif">Campus Contact</h5>
            <div class="text-xs text-slate-400 space-y-2">
              <div class="flex items-start space-x-2">
                <MapPin class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" />
                <span>Academic Boulevard, Biringan City, Samar, Eastern Visayas 6700</span>
              </div>
              <div class="flex items-start space-x-2">
                <Phone class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" />
                <span>Tel: (055) 888-7766 • Mobile: 0917-111-0001</span>
              </div>
              <div class="flex items-start space-x-2">
                <Mail class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" />
                <span>admissions@jjkingsbiringan.edu.ph</span>
              </div>
              <div class="flex items-start space-x-2">
                <Clock class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" />
                <span>Office: Mon–Sat, 8:00 AM – 5:00 PM</span>
              </div>
            </div>
          </div>

        </div>

        <!-- Bottom Copyright & DepEd Note -->
        <div class="pt-6 flex flex-col md:flex-row items-center justify-between gap-4 text-slate-500 text-[11px]">
          <div>
            © {{ currentYear }} JJKINGS Biringan School Admission & Student Information System. All Rights Reserved.
          </div>
          <div class="flex items-center space-x-2 text-[10px] text-slate-400">
            <span>Republic of the Philippines</span>
            <span>•</span>
            <span>Department of Education (DepEd) K-12 & MATATAG Aligned</span>
          </div>
        </div>

      </div>
    </footer>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { 
  GraduationCap, 
  LogOut, 
  ArrowRight, 
  UserCheck, 
  UserPlus, 
  ShieldCheck, 
  Phone, 
  Mail, 
  MapPin, 
  Clock, 
  Award 
} from 'lucide-vue-next';
import { getRoleRoutePath } from './router';
import api from './services/api';

const router = useRouter();
const route = useRoute();
const currentUser = ref(null);
const showLogoutConfirm = ref(false);
const currentYear = new Date().getFullYear();

const dashboardRoute = computed(() => {
  return currentUser.value ? getRoleRoutePath(currentUser.value.role_slug) : '/';
});

const userInitials = computed(() => {
  if (!currentUser.value) return 'U';
  const f = currentUser.value.first_name?.[0] || currentUser.value.username?.[0] || 'U';
  const l = currentUser.value.last_name?.[0] || '';
  return (f + l).toUpperCase();
});

const loadCurrentUser = () => {
  const userJson = localStorage.getItem('sia_auth_user');
  if (userJson) {
    try {
      currentUser.value = JSON.parse(userJson);
    } catch (e) {
      currentUser.value = null;
    }
  } else {
    currentUser.value = null;
  }
};

const confirmLogout = async () => {
  showLogoutConfirm.value = false;
  try {
    await api.logout();
  } catch (e) {
    // Ignore error on logout
  }
  localStorage.removeItem('sia_auth_token');
  localStorage.removeItem('sia_auth_user');
  currentUser.value = null;
  window.dispatchEvent(new Event('auth-changed'));
  router.push('/login');
};

const currentActiveHomeTab = ref('academics');

const handleHomeTabSwitched = (e) => {
  if (e.detail) currentActiveHomeTab.value = e.detail;
};

const navigateToHomeTab = (tabId) => {
  currentActiveHomeTab.value = tabId;
  sessionStorage.setItem('sia_active_home_tab', tabId);
  window.dispatchEvent(new CustomEvent('switch-home-tab', { detail: tabId }));

  if (route.name !== 'Home') {
    router.push({ path: '/', query: { tab: tabId } }).then(() => {
      setTimeout(() => {
        window.dispatchEvent(new CustomEvent('switch-home-tab', { detail: tabId }));
        const el = document.getElementById('academic-hub');
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 100);
    });
  } else {
    router.replace({ path: '/', query: { tab: tabId } }).catch(() => {});
    const el = document.getElementById('academic-hub');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
};

onMounted(() => {
  loadCurrentUser();
  window.addEventListener('storage', loadCurrentUser);
  window.addEventListener('auth-changed', loadCurrentUser);
  window.addEventListener('switch-home-tab', handleHomeTabSwitched);
});

onUnmounted(() => {
  window.removeEventListener('storage', loadCurrentUser);
  window.removeEventListener('auth-changed', loadCurrentUser);
  window.removeEventListener('switch-home-tab', handleHomeTabSwitched);
});
</script>
