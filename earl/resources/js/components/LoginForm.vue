<template>
  <div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-lg border border-slate-200 mt-8">
    <div class="mb-6 text-center">
      <div class="inline-flex items-center justify-center w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full mb-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
      </div>
      <h3 class="text-xl font-bold text-slate-800">Sign In to Continue</h3>
      <p class="text-xs text-slate-500 mt-1">Laravel Sanctum Token Authentication & RBAC</p>
    </div>

    <!-- Error Banner Feedback -->
    <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-start space-x-2">
      <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>{{ errorMessage }}</span>
    </div>

    <!-- Controlled Form Inputs -->
    <form @submit.prevent="handleLogin" class="space-y-4">
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Email Address</label>
        <input 
          v-model="credentials.email" 
          type="email" 
          required 
          placeholder="e.g. earlfermilan0@gmail.com"
          :class="errors.email ? 'border-red-400 focus:ring-red-500' : 'border-slate-300 focus:ring-indigo-500'"
          class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 text-sm transition" 
        />
        <p v-if="errors.email" class="text-xs text-red-600 mt-1">{{ errors.email[0] }}</p>
      </div>

      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Password</label>
        <input 
          v-model="credentials.password" 
          type="password" 
          required 
          placeholder="••••••••"
          :class="errors.password ? 'border-red-400 focus:ring-red-500' : 'border-slate-300 focus:ring-indigo-500'"
          class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 text-sm transition" 
        />
        <p v-if="errors.password" class="text-xs text-red-600 mt-1">{{ errors.password[0] }}</p>
      </div>

      <!-- Submit button with loading state feedback -->
      <button 
        type="submit" 
        :disabled="loading"
        class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition text-sm disabled:opacity-50 flex items-center justify-center space-x-2 shadow-sm"
      >
        <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span>{{ loading ? 'Authenticating...' : 'Sign In' }}</span>
      </button>
    </form>

    <!-- Quick Seeded Test Credentials for Easy Video Demo -->
    <div class="mt-6 pt-5 border-t border-slate-200">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 text-center">Quick Demo Credentials</p>
      <div class="grid grid-cols-2 gap-2">
        <button 
          type="button" 
          @click="quickFill('earlfermilan0@gmail.com', '12345678')" 
          class="p-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-medium text-center transition flex flex-col items-center"
        >
          <span class="font-bold">🔑 Administrator</span>
          <span class="text-[10px] text-indigo-500">Full Access (Add/Edit/Delete)</span>
        </button>
        <button 
          type="button" 
          @click="quickFill('efermilan@ssct.edu.ph', 'password123')" 
          class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-medium text-center transition flex flex-col items-center"
        >
          <span class="font-bold">👤 Regular User</span>
          <span class="text-[10px] text-slate-500">Read-Only Access</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';

const emit = defineEmits(['login-success']);

// Manage login input state using reactive()
const credentials = reactive({
  email: '',
  password: ''
});

// Manage loading, error, and validation state using ref()
const loading = ref(false);
const errorMessage = ref('');
const errors = ref({});

const quickFill = (email, password) => {
  credentials.email = email;
  credentials.password = password;
  errorMessage.value = '';
  errors.value = {};
};

const handleLogin = async () => {
  loading.value = true;
  errorMessage.value = '';
  errors.value = {};

  try {
    const response = await window.axios.post('/api/login', {
      email: credentials.email,
      password: credentials.password
    });

    const { token, user } = response.data;
    emit('login-success', { token, user });
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {};
      errorMessage.value = 'Please correct the validation errors below.';
    } else if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = 'Failed to connect to authentication server.';
    }
  } finally {
    loading.value = false;
  }
};
</script>
