<template>
  <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md border border-slate-200 mt-8">
    <div class="mb-6 text-center">
      <h3 class="text-xl font-bold text-slate-800">Sign In</h3>
      <p class="text-sm text-slate-500 mt-1">Authenticate to access student records and management</p>
    </div>

    <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 text-red-700 rounded-md text-sm">
      {{ errorMessage }}
    </div>

    <form @submit.prevent="handleLogin" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email:</label>
        <input 
          v-model="email" 
          type="email" 
          required 
          placeholder="e.g. earlfermilan0@gmail.com"
          class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" 
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Password:</label>
        <input 
          v-model="password" 
          type="password" 
          required 
          placeholder="••••••••"
          class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" 
        />
      </div>

      <button 
        type="submit" 
        :disabled="loading"
        class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition text-sm disabled:opacity-50"
      >
        {{ loading ? 'Signing in...' : 'Sign In' }}
      </button>
    </form>

    <div class="mt-6 pt-4 border-t border-slate-200">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 text-center">Quick Demo Accounts</p>
      <div class="grid grid-cols-2 gap-2">
        <button 
          type="button" 
          @click="fillCredentials('earlfermilan0@gmail.com', '12345678')" 
          class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium text-center transition"
        >
          🔑 Admin Account
        </button>
        <button 
          type="button" 
          @click="fillCredentials('efermilan@ssct.edu.ph', 'password123')" 
          class="py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium text-center transition"
        >
          👤 Regular User
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  emits: ['login-success'],
  data() {
    return {
      email: '',
      password: '',
      loading: false,
      errorMessage: ''
    };
  },
  methods: {
    fillCredentials(email, password) {
      this.email = email;
      this.password = password;
      this.errorMessage = '';
    },
    handleLogin() {
      this.loading = true;
      this.errorMessage = '';

      window.axios.post('/api/login', {
        email: this.email,
        password: this.password
      })
      .then(response => {
        this.loading = false;
        const { token, user } = response.data;
        this.$emit('login-success', { token, user });
      })
      .catch(error => {
        this.loading = false;
        if (error.response && error.response.data && error.response.data.message) {
          this.errorMessage = error.response.data.message;
        } else {
          this.errorMessage = 'Login failed. Please verify your credentials.';
        }
      });
    }
  }
};
</script>

