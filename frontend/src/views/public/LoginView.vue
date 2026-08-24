<template>
  <div class="min-h-[calc(100vh-5rem)] flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8 bg-slate-100 text-slate-900 selection:bg-amber-500 selection:text-white">
    <div class="max-w-md w-full space-y-6 p-7 sm:p-9 rounded-3xl bg-white border-2 border-slate-200 shadow-xl">
      
      <!-- Institutional Header Emblem & Titles -->
      <div class="text-center">
        <div class="w-14 h-14 rounded-2xl bg-[#0c2340] border-2 border-amber-400 text-amber-400 flex items-center justify-center mx-auto mb-3.5 shadow-md">
          <GraduationCap class="w-7 h-7" />
        </div>
        <div class="inline-flex items-center space-x-1.5 px-3 py-0.5 rounded-full bg-blue-50 border border-blue-200 text-blue-900 text-[10px] font-bold uppercase tracking-wider mb-2">
          <span>Student & Applicant Gateway</span>
        </div>
        <h2 class="text-2xl font-black text-[#0c2340] tracking-tight font-serif">Student Portal Login</h2>
        <p class="mt-1 text-xs text-slate-500 font-medium">JJKINGS Biringan School (JHS & SHS Only)</p>
      </div>

      <!-- Error Alert -->
      <div v-if="errorMessage" class="p-3.5 rounded-xl bg-rose-50 border border-rose-300 text-rose-800 text-xs flex items-center space-x-2 animate-in fade-in duration-200 shadow-xs">
        <AlertCircle class="w-4 h-4 shrink-0 text-rose-600" />
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Login Form -->
      <form class="space-y-4" @submit.prevent="handleLogin">
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Student ID / Email / LRN</label>
          <div class="relative">
            <input 
              v-model="form.identity" 
              type="text" 
              required 
              placeholder="e.g. 2026-SHS-0005, student2026, or email"
              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-700 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition"
            />
            <User class="w-4 h-4 text-slate-400 absolute right-3.5 top-3" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
          <div class="relative">
            <input 
              v-model="form.password" 
              :type="showPassword ? 'text' : 'password'" 
              required 
              placeholder="••••••••"
              class="w-full px-4 py-2.5 pr-11 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-700 focus:ring-2 focus:ring-blue-100 text-sm transition"
            />
            <button 
              type="button" 
              @click="showPassword = !showPassword" 
              class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-700 transition focus:outline-none cursor-pointer"
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
          class="w-full py-3 px-4 rounded-xl text-xs sm:text-sm font-black bg-[#0c2340] hover:bg-blue-900 disabled:opacity-50 text-amber-400 shadow-md shadow-blue-950/20 transition flex items-center justify-center space-x-2 cursor-pointer border border-amber-400/50"
        >
          <span v-if="isLoading" class="w-4 h-4 border-2 border-amber-400 border-t-transparent rounded-full animate-spin"></span>
          <span v-else class="flex items-center space-x-1.5">
            <span>Sign In to Student Portal</span>
            <ArrowRight class="w-4 h-4 text-amber-400" />
          </span>
        </button>
      </form>

      <!-- Quick Demo Student Switcher -->
      <div class="pt-4 border-t border-slate-200">
        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2.5 text-center flex items-center justify-center space-x-1">
          <Key class="w-3 h-3 text-amber-600" />
          <span>Demo Student Accounts (Password: <code class="text-blue-900 font-bold font-mono">password123</code>)</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
          <button 
            @click="fillCredentials('shs')" 
            type="button" 
            class="p-2.5 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-200 hover:border-amber-400 text-left transition flex flex-col justify-between cursor-pointer shadow-2xs"
          >
            <span class="font-bold text-[#0c2340] text-[11px]">SHS STEM</span>
            <span class="text-[10px] text-slate-500 font-mono">2026-SHS-0005</span>
          </button>
          <button 
            @click="fillCredentials('jhs')" 
            type="button" 
            class="p-2.5 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-200 hover:border-amber-400 text-left transition flex flex-col justify-between cursor-pointer shadow-2xs"
          >
            <span class="font-bold text-[#0c2340] text-[11px]">JHS Grade 7</span>
            <span class="text-[10px] text-slate-500 font-mono">2026-JHS-0001</span>
          </button>
          <button 
            @click="fillCredentials('queue')" 
            type="button" 
            class="p-2.5 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-200 hover:border-amber-400 text-left transition flex flex-col justify-between cursor-pointer shadow-2xs"
          >
            <span class="font-bold text-[#0c2340] text-[11px]">Enrollee in Queue</span>
            <span class="text-[10px] text-slate-500 font-mono">student2026</span>
          </button>
        </div>
      </div>

      <!-- Registration Link -->
      <div class="text-center text-xs text-slate-600 pt-2 border-t border-slate-100">
        New Student Applicant? 
        <router-link to="/register" class="font-bold text-blue-900 hover:text-blue-700 ml-1 inline-flex items-center space-x-0.5 underline">
          <span>Apply for Admission Now</span>
          <ArrowRight class="w-3.5 h-3.5" />
        </router-link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
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

const fillCredentials = (type) => {
  const map = {
    shs: { identity: '2026-SHS-0005', password: 'password123' },
    jhs: { identity: '2026-JHS-0001', password: 'password123' },
    queue: { identity: 'student2026', password: 'password123' }
  };
  if (map[type]) {
    form.value.identity = map[type].identity;
    form.value.password = map[type].password;
  }
};

const handleLogin = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    const res = await api.login({
      username: form.value.identity,
      password: form.value.password
    });

    if (res && res.success && res.data) {
      const user = res.data;
      localStorage.setItem('sia_auth_token', user.token);
      localStorage.setItem('sia_auth_user', JSON.stringify(user));

      window.dispatchEvent(new Event('auth-changed'));

      const targetRoute = getRoleRouteName(user.role_slug);
      router.push({ name: targetRoute });
    } else {
      errorMessage.value = res.message || 'Authentication failed. Please verify your credentials.';
    }
  } catch (err) {
    errorMessage.value = err.message || 'An error occurred while connecting to the server.';
  } finally {
    isLoading.value = false;
  }
};
</script>
