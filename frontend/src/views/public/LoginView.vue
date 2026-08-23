<template>
  <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-900">
    <div class="max-w-md w-full space-y-8 p-8 rounded-3xl bg-slate-950 border border-slate-800 shadow-2xl">
      <div class="text-center">
        <div class="w-12 h-12 rounded-2xl bg-emerald-950 border border-emerald-500/30 text-emerald-400 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-950">
          <Lock class="w-6 h-6" />
        </div>
        <h2 class="text-2xl font-extrabold text-white tracking-tight">Portal Authentication</h2>
        <p class="mt-2 text-xs text-slate-400">Sign in to your Staff, Student, or Admission account</p>
      </div>

      <!-- Error Alert -->
      <div v-if="errorMessage" class="p-3.5 rounded-xl bg-rose-950/80 border border-rose-800 text-rose-300 text-xs flex items-center space-x-2">
        <AlertCircle class="w-4 h-4 shrink-0 text-rose-400" />
        <span>{{ errorMessage }}</span>
      </div>

      <form class="mt-8 space-y-4" @submit.prevent="handleLogin">
        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Username / Email / Student ID</label>
          <div class="relative">
            <input 
              v-model="form.identity" 
              type="text" 
              required 
              placeholder="e.g. admin, registrar, student2026"
              class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm"
            />
            <User class="w-4 h-4 text-slate-500 absolute right-3.5 top-3" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Password</label>
          <div class="relative">
            <input 
              v-model="form.password" 
              :type="showPassword ? 'text' : 'password'" 
              required 
              placeholder="••••••••"
              class="w-full px-4 py-2.5 pr-11 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm"
            />
            <button 
              type="button" 
              @click="showPassword = !showPassword" 
              class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-200 transition focus:outline-none"
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
          class="w-full py-3 px-4 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white shadow-lg shadow-emerald-600/30 transition flex items-center justify-center space-x-2"
        >
          <span v-if="isLoading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
          <span v-else>Sign In to Account</span>
        </button>
      </form>

      <!-- Quick Demo Role Switcher for School Consultation & Testing -->
      <div class="pt-4 border-t border-slate-800">
        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 text-center">
          Demo Accounts (Password: <code class="text-emerald-400 font-mono">password123</code>)
        </div>
        <div class="grid grid-cols-2 gap-1.5 text-xs">
          <button @click="fillCredentials('admin')" type="button" class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-800 text-left transition flex items-center justify-between">
            <span class="font-medium text-amber-300">Admin</span>
            <span class="text-[10px] text-slate-500 font-mono">admin</span>
          </button>
          <button @click="fillCredentials('coordinator')" type="button" class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-800 text-left transition flex items-center justify-between">
            <span class="font-medium text-purple-300">Coordinator</span>
            <span class="text-[10px] text-slate-500 font-mono">coordinator</span>
          </button>
          <button @click="fillCredentials('registrar')" type="button" class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-800 text-left transition flex items-center justify-between">
            <span class="font-medium text-blue-300">Registrar</span>
            <span class="text-[10px] text-slate-500 font-mono">registrar</span>
          </button>
          <button @click="fillCredentials('treasury')" type="button" class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-800 text-left transition flex items-center justify-between">
            <span class="font-medium text-emerald-300">Treasury</span>
            <span class="text-[10px] text-slate-500 font-mono">treasury</span>
          </button>
          <button @click="fillCredentials('records')" type="button" class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-800 text-left transition flex items-center justify-between">
            <span class="font-medium text-cyan-300">Records</span>
            <span class="text-[10px] text-slate-500 font-mono">records</span>
          </button>
          <button @click="fillCredentials('student2026')" type="button" class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-800 text-left transition flex items-center justify-between">
            <span class="font-medium text-teal-300">Enrolled Student</span>
            <span class="text-[10px] text-slate-500 font-mono">student2026</span>
          </button>
        </div>
      </div>

      <div class="text-center text-xs text-slate-400">
        New applicant? 
        <router-link to="/register" class="font-semibold text-emerald-400 hover:text-emerald-300 ml-1">
          Create Temporary Admission Account
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { Lock, User, KeyRound, AlertCircle, Eye, EyeOff } from 'lucide-vue-next';
import api from '../../services/api';

const router = useRouter();
const showPassword = ref(false);
const form = ref({
  identity: '',
  password: ''
});
const isLoading = ref(false);
const errorMessage = ref('');

const fillCredentials = (username) => {
  form.value.identity = username;
  form.value.password = 'password123';
};

const handleLogin = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    const res = await api.login({
      username: form.value.identity,
      password: form.value.password
    });

    const user = res.data;
    localStorage.setItem('sia_auth_token', user.token);
    localStorage.setItem('sia_auth_user', JSON.stringify(user));
    window.dispatchEvent(new Event('auth-changed'));

    // Route based on role
    if (user.role_slug === 'applicant') {
      router.push('/admission');
    } else if (user.role_slug === 'registrar') {
      router.push('/registrar');
    } else if (user.role_slug === 'treasury') {
      router.push('/treasury');
    } else if (user.role_slug === 'coordinator') {
      router.push('/coordinator');
    } else if (user.role_slug === 'records') {
      router.push('/records');
    } else if (user.role_slug === 'student') {
      router.push('/student');
    } else if (user.role_slug === 'admin') {
      router.push('/admin');
    } else {
      router.push('/');
    }
  } catch (err) {
    errorMessage.value = err.message || 'Login failed. Please check your credentials.';
  } finally {
    isLoading.value = false;
  }
};
</script>
