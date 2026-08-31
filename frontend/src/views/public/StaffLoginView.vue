<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-950 via-[#08182b] to-slate-900 text-slate-100 selection:bg-blue-900 selection:text-white relative overflow-hidden">
    
    <!-- Academic Administrative Decorative Background Elements -->
    <div class="absolute inset-0 bg-[radial-gradient(#1e3a8a_1px,transparent_1px)] [background-size:32px_32px] opacity-20 pointer-events-none"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-blue-600/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-blue-900/15 blur-3xl pointer-events-none"></div>

    <!-- Quick Back to Website Trigger (Top Left) -->
    <router-link 
      to="/" 
      class="absolute top-6 left-6 inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-slate-700 text-slate-300 hover:text-white text-xs font-semibold backdrop-blur-md transition-all shadow-md z-20 cursor-pointer"
    >
      <ArrowLeft class="w-4 h-4 text-blue-300" />
      <span>Back to Public Website</span>
    </router-link>

    <div class="max-w-md w-full space-y-6 p-7 sm:p-9 rounded-3xl bg-slate-900/95 backdrop-blur-xl border border-slate-800 shadow-2xl relative z-10 text-white animate-in fade-in zoom-in-95 duration-200">
      
      <!-- Administrative Emblem & Header -->
      <div class="text-center">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-800 to-[#08182b] text-white flex items-center justify-center mx-auto mb-3.5 shadow-lg shadow-blue-950/40 border border-blue-500/40">
          <ShieldCheck class="w-8 h-8 text-blue-200" />
        </div>
        <div class="inline-flex items-center space-x-1.5 px-3 py-0.5 rounded-full bg-blue-900/40 border border-blue-700/50 text-blue-200 text-[10px] font-bold uppercase tracking-wider mb-2 shadow-2xs">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
          <span>Authorized School Personnel Gateway</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight font-serif">Faculty & Staff Portal</h2>
        <p class="mt-1 text-xs text-slate-300 font-medium">Biringan Science & Leadership Academy Administration</p>
      </div>

      <!-- Error Alert Banner -->
      <div v-if="errorMessage" class="p-4 rounded-2xl bg-rose-950/90 border-2 border-rose-500 text-rose-200 text-xs space-y-2 animate-in fade-in duration-200 shadow-md">
        <div class="flex items-start space-x-2.5">
          <AlertCircle class="w-4 h-4 shrink-0 text-rose-400 mt-0.5" />
          <span class="font-bold leading-relaxed text-white">{{ errorMessage }}</span>
        </div>
        <div v-if="isStudentAccountAttempt" class="pt-1 border-t border-rose-500/40 flex items-center justify-between">
          <span class="text-[11px] text-rose-300">Are you an enrolled student?</span>
          <router-link to="/login" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] transition inline-flex items-center space-x-1">
            <span>Open Student Login</span>
            <ArrowRight class="w-3 h-3" />
          </router-link>
        </div>
      </div>

      <!-- Login Form -->
      <form class="space-y-4" @submit.prevent="handleLogin">
        <div>
          <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider mb-1.5">Official Staff Username / Email</label>
          <div class="relative">
            <input 
              v-model="form.identity" 
              type="text" 
              required 
              placeholder="e.g. admin, coordinator, registrar"
              class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm font-medium transition shadow-inner"
            />
            <User class="w-4 h-4 text-slate-500 absolute right-3.5 top-3.5" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider mb-1.5">Staff Password</label>
          <div class="relative">
            <input 
              v-model="form.password" 
              :type="showPassword ? 'text' : 'password'" 
              required 
              placeholder="••••••••"
              class="w-full px-4 py-3 pr-11 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm transition shadow-inner"
            />
            <button 
              type="button" 
              @click="showPassword = !showPassword" 
              class="absolute right-3.5 top-3.5 text-slate-400 hover:text-white transition focus:outline-none cursor-pointer"
              :title="showPassword ? 'Hide password' : 'Show password'"
            >
              <EyeOff v-if="showPassword" class="w-4 h-4" />
              <Eye v-else class="w-4 h-4" />
            </button>
          </div>
        </div>

        <button 
          type="submit" 
          :disabled="isLoading"
          class="w-full py-3.5 px-4 rounded-xl text-xs sm:text-sm font-semibold bg-blue-900 hover:bg-blue-800 disabled:opacity-50 text-white shadow-lg shadow-blue-950/40 transition-all duration-200 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center space-x-2 cursor-pointer border border-blue-700/50"
        >
          <span v-if="isLoading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
          <span v-else class="flex items-center space-x-2">
            <span>Authenticate Staff Access</span>
            <ArrowRight class="w-4 h-4 text-white" />
          </span>
        </button>
      </form>

      <!-- Quick Demo Staff Switcher -->
      <div class="pt-4 border-t-2 border-slate-800">
        <div class="text-[10px] font-bold text-blue-200 uppercase tracking-wider mb-2.5 text-center flex items-center justify-center space-x-1.5">
          <Key class="w-3 h-3 text-blue-300" />
          <span>Demo Staff Roles (Password: <code class="text-white font-bold font-mono">password123</code>)</span>
        </div>
        <div class="grid grid-cols-2 gap-2 text-xs">
          <button @click="fillCredentials('admin')" type="button" class="p-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-700 hover:border-blue-500 text-left transition flex items-center justify-between cursor-pointer group shadow-2xs">
            <div>
              <span class="font-bold text-white block text-[11px]">Super Admin</span>
              <span class="text-[10px] text-slate-400 font-mono">admin</span>
            </div>
            <Shield class="w-3.5 h-3.5 text-blue-400 group-hover:scale-110 transition" />
          </button>
          <button @click="fillCredentials('coordinator')" type="button" class="p-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-700 hover:border-blue-500 text-left transition flex items-center justify-between cursor-pointer group shadow-2xs">
            <div>
              <span class="font-bold text-white block text-[11px]">Coordinator</span>
              <span class="text-[10px] text-slate-400 font-mono">coordinator</span>
            </div>
            <Calendar class="w-3.5 h-3.5 text-blue-300 group-hover:scale-110 transition" />
          </button>
          <button @click="fillCredentials('registrar')" type="button" class="p-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-700 hover:border-blue-500 text-left transition flex items-center justify-between cursor-pointer group shadow-2xs">
            <div>
              <span class="font-bold text-white block text-[11px]">Registrar</span>
              <span class="text-[10px] text-slate-400 font-mono">registrar</span>
            </div>
            <FileCheck class="w-3.5 h-3.5 text-blue-400 group-hover:scale-110 transition" />
          </button>
          <button @click="fillCredentials('treasury')" type="button" class="p-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-700 hover:border-blue-500 text-left transition flex items-center justify-between cursor-pointer group shadow-2xs">
            <div>
              <span class="font-bold text-white block text-[11px]">Treasury / Cashier</span>
              <span class="text-[10px] text-slate-400 font-mono">treasury</span>
            </div>
            <CreditCard class="w-3.5 h-3.5 text-emerald-400 group-hover:scale-110 transition" />
          </button>
          <button @click="fillCredentials('records')" type="button" class="col-span-2 p-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-700 hover:border-blue-500 text-left transition flex items-center justify-between cursor-pointer group shadow-2xs">
            <div>
              <span class="font-bold text-white block text-[11px]">School Records Custodian</span>
              <span class="text-[10px] text-slate-400 font-mono">records</span>
            </div>
            <FolderArchive class="w-3.5 h-3.5 text-blue-300 group-hover:scale-110 transition" />
          </button>
        </div>
      </div>

      <!-- Switch to Student Login -->
      <div class="text-center text-xs text-slate-400 pt-2 border-t border-slate-800">
        Are you an Enrolled Student or Applicant?
        <router-link to="/login" class="font-bold text-blue-300 hover:text-blue-200 ml-1 inline-flex items-center space-x-1 underline">
          <span>Student Portal Login →</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { 
  User, Eye, EyeOff, AlertCircle, ShieldCheck, 
  ArrowRight, ArrowLeft, Key, Shield, Calendar, FileCheck, CreditCard, FolderArchive 
} from 'lucide-vue-next';
import api from '../../services/api';
import { getRoleRouteName } from '../../router';

const router = useRouter();

const form = ref({
  identity: '',
  password: ''
});

const showPassword = ref(false);
const isLoading = ref(false);
const errorMessage = ref('');

const isStudentAccountAttempt = computed(() => {
  return errorMessage.value.toLowerCase().includes('student portal');
});

const fillCredentials = (role) => {
  const credentialsMap = {
    admin: { identity: 'admin', password: 'password123' },
    coordinator: { identity: 'coordinator', password: 'password123' },
    registrar: { identity: 'registrar', password: 'password123' },
    treasury: { identity: 'treasury', password: 'password123' },
    records: { identity: 'records', password: 'password123' }
  };

  if (credentialsMap[role]) {
    form.value.identity = credentialsMap[role].identity;
    form.value.password = credentialsMap[role].password;
  }
};

const handleLogin = async () => {
  errorMessage.value = '';
  isLoading.value = true;

  try {
    const res = await api.login({
      username: form.value.identity.trim(),
      password: form.value.password,
      portal_type: 'staff'
    });

    if (res.data && res.data.token) {
      sessionStorage.setItem('sia_auth_token', res.data.token);
      sessionStorage.setItem('sia_auth_user', JSON.stringify(res.data));
      localStorage.removeItem('sia_auth_token');
      localStorage.removeItem('sia_auth_user');
      window.dispatchEvent(new Event('auth-changed'));

      const targetRoute = getRoleRouteName(res.data.role_slug);
      router.push({ name: targetRoute });
    } else {
      errorMessage.value = 'Failed to authenticate staff account.';
    }
  } catch (err) {
    errorMessage.value = err.message || 'Staff authentication failed. Please check your credentials.';
  } finally {
    isLoading.value = false;
  }
};
</script>
