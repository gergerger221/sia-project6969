<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800">
    <!-- Top Global Header (Hidden in Print) -->
    <header class="no-print bg-navy-950 text-white border-b border-navy-800 sticky top-0 z-50 shadow-md">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Logo & Branding -->
        <router-link :to="dashboardRoute" class="flex items-center space-x-3 group">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-emerald-400 flex items-center justify-center shadow-lg shadow-brand-500/20 group-hover:scale-105 transition">
            <GraduationCap class="w-6 h-6 text-white" />
          </div>
          <div>
            <span class="font-bold text-lg tracking-tight bg-gradient-to-r from-white via-slate-100 to-emerald-300 bg-clip-text text-transparent">
              SIA High School
            </span>
            <span class="block text-[10px] text-slate-400 font-medium tracking-wider uppercase">JHS & SHS Admission Portal</span>
          </div>
        </router-link>

        <!-- Navigation Links -->
        <nav class="hidden md:flex items-center space-x-1">
          <router-link v-if="!currentUser" to="/" class="px-3 py-1.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-white/10 transition">
            Home
          </router-link>

          <!-- Dynamic Role Portals -->
          <template v-if="currentUser">
            <router-link v-if="currentUser.role_slug === 'applicant'" to="/admission" class="px-3 py-1.5 rounded-lg text-sm font-medium text-emerald-400 bg-emerald-950/40 border border-emerald-500/30 hover:bg-emerald-900/50 transition">
              My Admission Portal
            </router-link>
            <router-link v-if="['registrar', 'admin'].includes(currentUser.role_slug)" to="/registrar" class="px-3 py-1.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-white/10 transition">
              Registrar
            </router-link>
            <router-link v-if="['treasury', 'admin'].includes(currentUser.role_slug)" to="/treasury" class="px-3 py-1.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-white/10 transition">
              Treasury / Cashier
            </router-link>
            <router-link v-if="['coordinator', 'admin'].includes(currentUser.role_slug)" to="/coordinator" class="px-3 py-1.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-white/10 transition">
              Coordinator
            </router-link>
            <router-link v-if="['records', 'admin', 'registrar'].includes(currentUser.role_slug)" to="/records" class="px-3 py-1.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-white/10 transition">
              School Records
            </router-link>
            <router-link v-if="currentUser.role_slug === 'student'" to="/student" class="px-3 py-1.5 rounded-lg text-sm font-medium text-blue-400 bg-blue-950/40 border border-blue-500/30 hover:bg-blue-900/50 transition">
              Student Portal
            </router-link>
            <router-link v-if="currentUser.role_slug === 'admin'" to="/admin" class="px-3 py-1.5 rounded-lg text-sm font-medium text-amber-400 bg-amber-950/40 border border-amber-500/30 hover:bg-amber-900/50 transition">
              Admin
            </router-link>
          </template>
        </nav>

        <!-- Right User Actions -->
        <div class="flex items-center space-x-3">
          <template v-if="currentUser">
            <div class="flex items-center space-x-2.5 bg-slate-900/80 px-3 py-1.5 rounded-xl border border-slate-800">
              <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-xs font-bold text-emerald-400">
                {{ userInitials }}
              </div>
              <div class="hidden sm:block text-left text-xs">
                <div class="font-semibold text-slate-200">{{ currentUser.first_name || currentUser.username }}</div>
                <div class="text-[10px] text-emerald-400 font-medium capitalize">{{ currentUser.role_name || currentUser.role_slug }}</div>
              </div>
            </div>
            <button @click="handleLogout" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Logout">
              <LogOut class="w-5 h-5" />
            </button>
          </template>
          <template v-else>
            <router-link to="/login" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-200 hover:text-white hover:bg-white/10 transition">
              Staff & Student Login
            </router-link>
            <router-link to="/register" class="px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition flex items-center space-x-1.5">
              <span>Apply Now</span>
              <ArrowRight class="w-4 h-4" />
            </router-link>
          </template>
        </div>
      </div>
    </header>

    <!-- Main View Outlet -->
    <main class="flex-1">
      <router-view />
    </main>

    <!-- Global Footer (Hidden in Print) -->
    <footer class="no-print bg-slate-900 text-slate-400 border-t border-slate-800 py-8 text-xs">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
          <div class="w-6 h-6 rounded bg-emerald-600 flex items-center justify-center text-white text-[10px] font-bold">
            SIA
          </div>
          <div>
            <p class="font-semibold text-slate-300">Junior & Senior High School Online Admission & Enrollment System</p>
            <p class="text-slate-500 text-[11px]">Philippine DepEd K to 12 & MATATAG Curriculum Compliant</p>
          </div>
        </div>
        <div class="text-slate-500 text-center sm:text-right">
          <p>© {{ currentYear }} School Information & Accounting System.</p>
          <p class="text-[11px]">Offline Localhost Build • MySQL & PHP Backend</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { GraduationCap, LogOut, ArrowRight } from 'lucide-vue-next';
import { getRoleRoutePath } from './router';
import api from './services/api';

const router = useRouter();
const currentUser = ref(null);
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

const handleLogout = async () => {
  try {
    await api.logout();
  } catch (e) {
    // Ignore error on logout
  }
  localStorage.removeItem('sia_auth_token');
  localStorage.removeItem('sia_auth_user');
  currentUser.value = null;
  router.push('/login');
};

onMounted(() => {
  loadCurrentUser();
  window.addEventListener('storage', loadCurrentUser);
  window.addEventListener('auth-changed', loadCurrentUser);
});
</script>
