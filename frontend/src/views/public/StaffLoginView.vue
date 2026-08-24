<template>
  <div class="min-h-[calc(100vh-5rem)] flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8 bg-slate-100 text-slate-900 selection:bg-amber-500 selection:text-white">
    <div class="max-w-md w-full space-y-6 p-7 sm:p-9 rounded-3xl bg-white border-2 border-slate-200 shadow-xl">
      
      <!-- Header -->
      <div class="text-center">
        <div class="w-14 h-14 rounded-2xl bg-[#0c2340] border-2 border-amber-400 text-amber-400 flex items-center justify-center mx-auto mb-3.5 shadow-md">
          <ShieldCheck class="w-7 h-7" />
        </div>
        <div class="inline-flex items-center space-x-1.5 px-3 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-900 text-[10px] font-bold uppercase tracking-wider mb-2">
          <span>Administrative Gateway</span>
        </div>
        <h2 class="text-2xl font-black text-[#0c2340] tracking-tight font-serif">Faculty & Staff Portal</h2>
        <p class="mt-1 text-xs text-slate-500 font-medium">JJKINGS Biringan School Administration</p>
      </div>

      <!-- Error Alert -->
      <div v-if="errorMessage" class="p-3.5 rounded-xl bg-rose-50 border border-rose-300 text-rose-800 text-xs flex items-center space-x-2 animate-in fade-in duration-200 shadow-xs">
        <AlertCircle class="w-4 h-4 shrink-0 text-rose-600" />
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Login Form -->
      <form class="space-y-4" @submit.prevent="handleLogin">
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Staff Username / Email</label>
          <div class="relative">
            <input 
              v-model="form.identity" 
              type="text" 
              required 
              placeholder="e.g. admin, maria_registrar, maria_treasury"
              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-700 focus:ring-2 focus:ring-blue-100 text-sm font-medium transition"
            />
            <User class="w-4 h-4 text-slate-400 absolute right-3.5 top-3" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Staff Password</label>
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
            <span>Authenticate Staff Access</span>
            <ArrowRight class="w-4 h-4 text-amber-400" />
          </span>
        </button>
      </form>

      <!-- Quick Demo Staff Switcher -->
      <div class="pt-4 border-t border-slate-200">
        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2.5 text-center flex items-center justify-center space-x-1">
          <Key class="w-3 h-3 text-amber-600" />
          <span>Quick Demo Staff Roles (Password: <code class="text-blue-900 font-bold font-mono">password123</code>)</span>
        </div>
        <div class="grid grid-cols-2 gap-2 text-xs">
          <button @click="fillCredentials('admin')" type="button" class="p-2.5 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-200 hover:border-amber-400 text-left transition flex items-center justify-between cursor-pointer group shadow-2xs">
            <div>
              <span class="font-bold text-[#0c2340] block text-[11px]">Super Admin</span>
              <span class="text-[10px] text-slate-500 font-mono">admin</span>
            </div>
            <Shield class="w-3.5 h-3.5 text-amber-600 group-hover:scale-110 transition" />
          </button>
          <button @click="fillCredentials('coordinator')" type="button" class="p-2.5 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-200 hover:border-amber-400 text-left transition flex items-center justify-between cursor-pointer group shadow-2xs">
            <div>
              <span class="font-bold text-[#0c2340] block text-[11px]">Coordinator</span>
              <span class="text-[10px] text-slate-500 font-mono">maria_coordinator</span>
            </div>
            <Calendar class="w-3.5 h-3.5 text-purple-600 group-hover:scale-110 transition" />
          </button>
          <button @click="fillCredentials('registrar')" type="button" class="p-2.5 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-200 hover:border-amber-400 text-left transition flex items-center justify-between cursor-pointer group shadow-2xs">
            <div>
              <span class="font-bold text-[#0c2340] block text-[11px]">Registrar</span>
              <span class="text-[10px] text-slate-500 font-mono">maria_registrar</span>
            </div>
            <FileCheck class="w-3.5 h-3.5 text-blue-600 group-hover:scale-110 transition" />
          </button>
          <button @click="fillCredentials('treasury')" type="button" class="p-2.5 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-200 hover:border-amber-400 text-left transition flex items-center justify-between cursor-pointer group shadow-2xs">
            <div>
              <span class="font-bold text-[#0c2340] block text-[11px]">Treasury / Cashier</span>
              <span class="text-[10px] text-slate-500 font-mono">maria_treasury</span>
            </div>
            <CreditCard class="w-3.5 h-3.5 text-emerald-600 group-hover:scale-110 transition" />
          </button>
          <button @click="fillCredentials('records')" type="button" class="col-span-2 p-2.5 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-200 hover:border-amber-400 text-left transition flex items-center justify-between cursor-pointer group shadow-2xs">
            <div>
              <span class="font-bold text-[#0c2340] block text-[11px]">School Records Custodian</span>
              <span class="text-[10px] text-slate-500 font-mono">maria_records</span>
            </div>
            <FolderArchive class="w-3.5 h-3.5 text-teal-600 group-hover:scale-110 transition" />
          </button>
        </div>
      </div>

      <!-- Switch to Student Login -->
      <div class="text-center text-xs text-slate-600 pt-2 border-t border-slate-100">
        Are you an Enrolled Student or Applicant?
        <router-link to="/login" class="font-bold text-blue-900 hover:text-blue-700 ml-1 inline-flex items-center space-x-1 underline">
          <span>Student Portal Login →</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { 
  User, Eye, EyeOff, AlertCircle, ShieldCheck, 
  ArrowRight, Key, Shield, Calendar, FileCheck, CreditCard, FolderArchive 
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

const fillCredentials = (role) => {
  const map = {
    admin: { identity: 'admin', password: 'password123' },
    coordinator: { identity: 'maria_coordinator', password: 'password123' },
    registrar: { identity: 'maria_registrar', password: 'password123' },
    treasury: { identity: 'maria_treasury', password: 'password123' },
    records: { identity: 'maria_records', password: 'password123' }
  };
  if (map[role]) {
    form.value.identity = map[role].identity;
    form.value.password = map[role].password;
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
      errorMessage.value = res.message || 'Authentication failed. Please verify credentials.';
    }
  } catch (err) {
    errorMessage.value = err.message || 'An error occurred while connecting to the server.';
  } finally {
    isLoading.value = false;
  }
};
</script>
