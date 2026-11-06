<template>
  <div class="max-w-sm mx-auto border border-slate-300 p-4 bg-white mt-6">
    <h2 class="text-lg font-bold mb-3">Login</h2>

    <div v-if="errorMessage" class="mb-3 p-2 bg-red-50 border border-red-300 text-red-700 text-xs">
      {{ errorMessage }}
    </div>

    <form @submit.prevent="handleLogin" class="space-y-3">
      <div>
        <label class="block text-xs font-medium mb-1">Email:</label>
        <input 
          v-model="credentials.email" 
          type="email" 
          required 
          placeholder="email"
          class="border border-slate-300 px-2 py-1 w-full text-sm" 
        />
      </div>

      <div>
        <label class="block text-xs font-medium mb-1">Password:</label>
        <input 
          v-model="credentials.password" 
          type="password" 
          required 
          placeholder="password"
          class="border border-slate-300 px-2 py-1 w-full text-sm" 
        />
      </div>

      <button 
        type="submit" 
        :disabled="loading"
        class="border border-slate-400 bg-slate-100 hover:bg-slate-200 px-4 py-1.5 text-sm font-medium w-full cursor-pointer disabled:opacity-50"
      >
        {{ loading ? 'Logging in...' : 'Login' }}
      </button>
    </form>

    <div class="mt-4 pt-3 border-t border-slate-200 text-xs">
      <p class="text-slate-600 mb-2">Fill Demo Credentials:</p>
      <div class="flex gap-2">
        <button 
          type="button" 
          @click="fillCredentials('earlfermilan0@gmail.com', '12345678')" 
          class="border border-slate-300 px-2 py-1 bg-slate-50 hover:bg-slate-100 flex-1 text-center cursor-pointer"
        >
          Admin
        </button>
        <button 
          type="button" 
          @click="fillCredentials('efermilan@ssct.edu.ph', 'password123')" 
          class="border border-slate-300 px-2 py-1 bg-slate-50 hover:bg-slate-100 flex-1 text-center cursor-pointer"
        >
          Regular User
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';

const emit = defineEmits(['login-success']);

const credentials = reactive({
  email: '',
  password: ''
});

const loading = ref(false);
const errorMessage = ref('');

const fillCredentials = (email, password) => {
  credentials.email = email;
  credentials.password = password;
  errorMessage.value = '';
};

const handleLogin = async () => {
  if (!credentials.email || !credentials.password) {
    errorMessage.value = 'Please enter email and password.';
    return;
  }

  loading.value = true;
  errorMessage.value = '';

  try {
    const response = await window.axios.post('/api/login', {
      email: credentials.email,
      password: credentials.password
    });

    const { token, user } = response.data;
    emit('login-success', { token, user });
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Login failed. Invalid credentials.';
  } finally {
    loading.value = false;
  }
};
</script>
