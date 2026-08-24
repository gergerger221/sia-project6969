<template>
  <div class="min-h-[calc(100vh-5rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-900 via-[#0c2340] to-slate-950 text-slate-100 selection:bg-amber-500 selection:text-white relative overflow-hidden">
    
    <!-- Academic Decorative Background Elements -->
    <div class="absolute inset-0 bg-[radial-gradient(#1e3a8a_1px,transparent_1px)] [background-size:24px_24px] opacity-20 pointer-events-none"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-blue-600/15 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-amber-500/15 blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full space-y-6 p-7 sm:p-9 rounded-3xl bg-white/95 backdrop-blur-xl border-2 border-amber-400/60 shadow-2xl relative z-10 text-slate-900 animate-in fade-in zoom-in-95 duration-200">
      
      <!-- Institutional Header Emblem & Titles -->
      <div class="text-center">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#0c2340] to-[#163860] border-2 border-amber-400 text-amber-400 flex items-center justify-center mx-auto mb-3.5 shadow-lg shadow-blue-950/30">
          <GraduationCap class="w-8 h-8 text-amber-400" />
        </div>
        <div class="inline-flex items-center space-x-1.5 px-3 py-0.5 rounded-full bg-blue-100 border border-blue-300 text-blue-950 text-[10px] font-extrabold uppercase tracking-wider mb-2 shadow-xs">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
          <span>Enrolled Student Gateway</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-black text-[#0c2340] tracking-tight font-serif">Student Portal Login</h2>
        <p class="mt-1 text-xs text-slate-600 font-medium">Junior High School & Senior High School (Grades 7–12)</p>
      </div>

      <!-- Error Alert Banner -->
      <div v-if="errorMessage" class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-400 text-rose-900 text-xs space-y-2 animate-in fade-in duration-200 shadow-sm">
        <div class="flex items-start space-x-2.5">
          <AlertCircle class="w-4 h-4 shrink-0 text-rose-600 mt-0.5" />
          <span class="font-bold leading-relaxed">{{ errorMessage }}</span>
        </div>
        <div v-if="isStaffAccountAttempt" class="pt-1 border-t border-rose-200/80 flex items-center justify-between">
          <span class="text-[11px] text-rose-700">Need administrative access?</span>
          <router-link to="/staff-login" class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] transition inline-flex items-center space-x-1">
            <span>Open Staff Login</span>
            <ArrowRight class="w-3 h-3" />
          </router-link>
        </div>
      </div>

      <!-- Login Form -->
      <form class="space-y-4" @submit.prevent="handleLogin">
        <div>
          <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">Official Student ID / Email / LRN</label>
          <div class="relative">
            <input 
              v-model="form.identity" 
              type="text" 
              required 
              placeholder="e.g. 2026-SHS-0005, 2026-JHS-0001, or email"
              class="w-full px-4 py-3 rounded-xl bg-slate-50 border-2 border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-800 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition shadow-inner"
            />
            <User class="w-4 h-4 text-slate-400 absolute right-3.5 top-3.5" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">Password</label>
          <div class="relative">
            <input 
              v-model="form.password" 
              :type="showPassword ? 'text' : 'password'" 
              required 
              placeholder="••••••••"
              class="w-full px-4 py-3 pr-11 rounded-xl bg-slate-50 border-2 border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-800 focus:ring-2 focus:ring-blue-100 text-sm transition shadow-inner"
            />
            <button 
              type="button" 
              @click="showPassword = !showPassword" 
              class="absolute right-3.5 top-3.5 text-slate-400 hover:text-slate-700 transition focus:outline-none cursor-pointer"
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
          class="w-full py-3.5 px-4 rounded-xl text-xs sm:text-sm font-black bg-[#0c2340] hover:bg-blue-900 disabled:opacity-50 text-amber-400 shadow-lg shadow-blue-950/25 transition-all duration-200 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center space-x-2 cursor-pointer border-2 border-amber-400"
        >
          <span v-if="isLoading" class="w-4 h-4 border-2 border-amber-400 border-t-transparent rounded-full animate-spin"></span>
          <span v-else class="flex items-center space-x-2">
            <span>Sign In to Student Portal</span>
            <ArrowRight class="w-4 h-4 text-amber-400" />
          </span>
        </button>
      </form>

      <!-- Quick Demo Student Switcher -->
      <div class="pt-4 border-t-2 border-slate-100">
        <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-2.5 text-center flex items-center justify-center space-x-1.5">
          <Key class="w-3 h-3 text-amber-600" />
          <span>Demo Enrolled Student Accounts (Password: <code class="text-blue-950 font-bold font-mono">password123</code>)</span>
        </div>
        <div class="grid grid-cols-2 gap-2 text-xs">
          <button 
            @click="fillCredentials('shs')" 
            type="button" 
            class="p-2.5 rounded-xl bg-slate-50 hover:bg-amber-50 border-2 border-slate-200 hover:border-amber-400 text-left transition flex flex-col justify-between cursor-pointer shadow-xs"
          >
            <span class="font-bold text-[#0c2340] text-[11px]">Senior High (STEM)</span>
            <span class="text-[10px] text-slate-500 font-mono">2026-SHS-0005</span>
          </button>
          <button 
            @click="fillCredentials('jhs')" 
            type="button" 
            class="p-2.5 rounded-xl bg-slate-50 hover:bg-amber-50 border-2 border-slate-200 hover:border-amber-400 text-left transition flex flex-col justify-between cursor-pointer shadow-xs"
          >
            <span class="font-bold text-[#0c2340] text-[11px]">Junior High (Grade 7)</span>
            <span class="text-[10px] text-slate-500 font-mono">2026-JHS-0001</span>
          </button>
        </div>
      </div>

      <!-- Links: Registration & Staff Portal -->
      <div class="space-y-2 pt-2 border-t border-slate-100 text-center text-xs text-slate-600">
        <div>
          New Student Applicant? 
          <router-link to="/register" class="font-bold text-blue-950 hover:text-blue-700 ml-1 inline-flex items-center space-x-0.5 underline">
            <span>Apply for Admission Now</span>
            <ArrowRight class="w-3.5 h-3.5" />
          </router-link>
        </div>
        <div>
          Are you a Teacher or Administrator? 
          <router-link to="/staff-login" class="font-bold text-amber-700 hover:text-amber-800 ml-1 inline-flex items-center space-x-0.5 underline">
            <span>Faculty & Staff Portal →</span>
          </router-link>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { User, Eye, EyeOff, AlertCircle, GraduationCap, ArrowRight, Key } from 'lucide-vue-next';
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

const isStaffAccountAttempt = computed(() => {
  return errorMessage.value.toLowerCase().includes('staff login');
});

const fillCredentials = (type) => {
  const map = {
    shs: { identity: 'student2026', password: 'password123' },
    jhs: { identity: '2026-JHS-0001', password: 'password123' }
  };
  if (map[type]) {
    form.value.identity = map[type].identity;
    form.value.password = map[type].password;
  }
};

const handleLogin = async () => {
  errorMessage.value = '';
  isLoading.value = true;

  try {
    const res = await api.login({
      username: form.value.identity.trim(),
      password: form.value.password,
      portal_type: 'student'
    });

    if (res.data && res.data.token) {
      localStorage.setItem('sia_auth_token', res.data.token);
      localStorage.setItem('sia_auth_user', JSON.stringify(res.data));

      const targetRoute = getRoleRouteName(res.data.role_slug);
      router.push({ name: targetRoute });
    } else {
      errorMessage.value = 'Failed to authenticate student account.';
    }
  } catch (err) {
    errorMessage.value = err.message || 'Login failed. Please check your credentials.';
  } finally {
    isLoading.value = false;
  }
};
</script>
