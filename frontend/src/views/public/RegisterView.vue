<template>
  <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-900">
    <div class="max-w-lg w-full space-y-6 p-8 rounded-3xl bg-slate-950 border border-slate-800 shadow-2xl">
      <div class="text-center">
        <div class="w-12 h-12 rounded-2xl bg-emerald-950 border border-emerald-500/30 text-emerald-400 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-950">
          <UserPlus class="w-6 h-6" />
        </div>
        <h2 class="text-2xl font-extrabold text-white tracking-tight">Temporary Admission Account</h2>
        <p class="mt-2 text-xs text-slate-400">
          Create an applicant account to start your Junior or Senior High School admission procedure.
        </p>
      </div>

      <div v-if="errorMessage" class="p-3.5 rounded-xl bg-rose-950/80 border border-rose-800 text-rose-300 text-xs flex items-center space-x-2">
        <AlertCircle class="w-4 h-4 shrink-0 text-rose-400" />
        <span>{{ errorMessage }}</span>
      </div>

      <form class="space-y-4" @submit.prevent="handleRegister">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">First Name *</label>
            <input 
              v-model="form.first_name" 
              type="text" 
              required 
              placeholder="e.g. Juan"
              class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Last Name *</label>
            <input 
              v-model="form.last_name" 
              type="text" 
              required 
              placeholder="e.g. Dela Cruz"
              class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 mb-1">Middle Name *</label>
          <input 
            v-model="form.middle_name" 
            type="text" 
            required
            placeholder="e.g. Protacio"
            class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address *</label>
            <input 
              v-model="form.email" 
              type="email" 
              required 
              placeholder="juan@gmail.com"
              class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Contact Mobile No. *</label>
            <input 
              v-model="form.contact_number" 
              type="tel" 
              required 
              maxlength="11"
              pattern="[0-9]{11}"
              @input="form.contact_number = form.contact_number.replace(/\D/g, '').slice(0, 11)"
              placeholder="09171234567"
              class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm font-mono"
            />
            <span class="text-[10px] text-slate-400">11-digit mobile number (e.g. 09171234567)</span>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-300 mb-1">Set Password *</label>
          <div class="relative">
            <input 
              v-model="form.password" 
              :type="showPassword ? 'text' : 'password'" 
              required 
              placeholder="At least 6 characters"
              class="w-full px-3.5 py-2 pr-10 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm font-mono"
            />
            <button 
              type="button" 
              @click="showPassword = !showPassword" 
              class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-200 transition focus:outline-none"
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
          <span v-else>Register & Get Application Ref #</span>
        </button>
      </form>

      <div class="text-center text-xs text-slate-400">
        Already have a temporary admission account?
        <router-link to="/login" class="font-semibold text-emerald-400 hover:text-emerald-300 ml-1">
          Login here
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { UserPlus, AlertCircle, Eye, EyeOff } from 'lucide-vue-next';
import api from '../../services/api';

const router = useRouter();
const showPassword = ref(false);
const form = ref({
  first_name: '',
  last_name: '',
  middle_name: '',
  email: '',
  contact_number: '',
  password: ''
});
const isLoading = ref(false);
const errorMessage = ref('');

const DRAFT_KEY = 'sia_registration_draft';

// Restore saved form draft on mount
onMounted(() => {
  try {
    const savedDraft = sessionStorage.getItem(DRAFT_KEY) || localStorage.getItem(DRAFT_KEY);
    if (savedDraft) {
      const parsed = JSON.parse(savedDraft);
      form.value.first_name = parsed.first_name || '';
      form.value.last_name = parsed.last_name || '';
      form.value.middle_name = parsed.middle_name || '';
      form.value.email = parsed.email || '';
      form.value.contact_number = parsed.contact_number || '';
      form.value.password = parsed.password || '';
    }
  } catch (e) {
    // Ignore parse error
  }
});

// Auto-save form inputs whenever user types
watch(form, (newVal) => {
  try {
    const draftData = {
      first_name: newVal.first_name,
      last_name: newVal.last_name,
      middle_name: newVal.middle_name,
      email: newVal.email,
      contact_number: newVal.contact_number,
      password: newVal.password
    };
    sessionStorage.setItem(DRAFT_KEY, JSON.stringify(draftData));
    localStorage.setItem(DRAFT_KEY, JSON.stringify(draftData));
  } catch (e) {
    // Ignore storage quota error
  }
}, { deep: true });

const handleRegister = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    const res = await api.registerApplicant(form.value);
    const user = res.data;

    // Clear saved draft on successful registration
    sessionStorage.removeItem(DRAFT_KEY);
    localStorage.removeItem(DRAFT_KEY);

    localStorage.setItem('sia_auth_token', user.token);
    localStorage.setItem('sia_auth_user', JSON.stringify(user));
    window.dispatchEvent(new Event('auth-changed'));

    router.push('/admission');
  } catch (err) {
    errorMessage.value = err.message || 'Registration failed.';
  } finally {
    isLoading.value = false;
  }
};
</script>
