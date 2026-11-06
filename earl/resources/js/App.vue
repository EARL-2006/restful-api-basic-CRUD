<template>
  <div class="max-w-5xl mx-auto p-4 sm:p-6 font-sans antialiased text-slate-800">
    <!-- Top Header -->
    <header class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-5">
      <div>
        <div class="flex items-center space-x-2">
          <span class="inline-flex items-center justify-center p-1.5 bg-indigo-600 text-white rounded-lg shadow-xs">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            </svg>
          </span>
          <h2 class="text-2xl font-black text-slate-900 tracking-tight">Student Management System</h2>
        </div>
        <p class="text-xs text-slate-500 mt-1">Vue 3 Composition API + Laravel 11 Sanctum RBAC</p>
      </div>

      <!-- Authenticated User Profile Bar -->
      <div v-if="isAuthenticated" class="flex flex-wrap items-center gap-3">
        <!-- Bearer Token Inspector Toggle (For Video Demonstration) -->
        <button 
          @click="showTokenInspector = !showTokenInspector"
          type="button"
          class="px-2.5 py-1 text-xs font-medium rounded-lg border border-slate-200 hover:bg-slate-100 transition flex items-center space-x-1 text-slate-600 cursor-pointer"
          title="Toggle Bearer Token Inspector for demonstration"
        >
          <span>🔑</span>
          <span>{{ showTokenInspector ? 'Hide Token' : 'Show Bearer Token' }}</span>
        </button>

        <!-- User Identity & Role Badge -->
        <div class="text-right">
          <p class="text-xs font-bold text-slate-900 leading-tight">{{ currentUser?.name }}</p>
          <span 
            :class="isAdmin ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200'"
            class="inline-block text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider border"
          >
            {{ currentUser?.role === 'admin' ? 'Administrator' : 'Regular User' }}
          </span>
        </div>

        <!-- Sign Out Button -->
        <button 
          @click="logout" 
          type="button"
          class="px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition cursor-pointer"
        >
          Sign Out
        </button>
      </div>
    </header>

    <!-- Bearer Token Details Inspector (Helps satisfy the video demo requirements) -->
    <div v-if="isAuthenticated && showTokenInspector" class="mb-6 p-4 bg-slate-900 text-slate-200 rounded-xl shadow-lg text-xs space-y-2 border border-slate-800">
      <div class="flex items-center justify-between border-b border-slate-800 pb-2">
        <span class="font-mono font-semibold text-emerald-400">🛡️ Sanctum Bearer Token Inspector</span>
        <span class="text-[10px] text-slate-400">Attached to headers.Authorization</span>
      </div>
      <div class="font-mono break-all bg-slate-950 p-2.5 rounded border border-slate-800 text-slate-300">
        <span class="text-slate-500">Authorization: </span><span class="text-indigo-400">Bearer</span> {{ token }}
      </div>
      <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1">
        <span>Logged in user ID: <strong class="text-slate-200">{{ currentUser?.id }}</strong> ({{ currentUser?.email }})</span>
        <span>Role: <strong class="text-emerald-400 uppercase">{{ currentUser?.role }}</strong></span>
      </div>
    </div>

    <!-- View 1: If Not Authenticated, show Login Form -->
    <LoginForm v-if="!isAuthenticated" @login-success="onLoginSuccess" />

    <!-- View 2: If Authenticated, show Main Interface -->
    <main v-else class="space-y-6">
      <!-- Global Success Feedback Alert -->
      <div 
        v-if="globalSuccess" 
        class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-xl text-sm flex items-center justify-between shadow-xs transition"
      >
        <div class="flex items-center space-x-2">
          <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="font-medium">{{ globalSuccess }}</span>
        </div>
        <button @click="globalSuccess = ''" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold cursor-pointer">✕</button>
      </div>

      <!-- Global Error Feedback Alert -->
      <div 
        v-if="globalError" 
        class="p-4 bg-red-50 border border-red-300 text-red-700 rounded-xl text-sm flex items-center justify-between shadow-xs transition"
      >
        <div class="flex items-center space-x-2">
          <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="font-medium">{{ globalError }}</span>
        </div>
        <button @click="globalError = ''" class="text-red-600 hover:text-red-800 text-xs font-bold cursor-pointer">✕</button>
      </div>

      <!-- Role-Based Action Notice for Regular Users -->
      <div v-if="!isAdmin" class="p-4 bg-sky-50 border border-sky-200 text-sky-900 rounded-xl text-xs sm:text-sm flex items-start space-x-3 shadow-xs">
        <svg class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
          <p class="font-bold">Role: Regular User (Read-Only Access)</p>
          <p class="text-sky-700 mt-0.5">
            You are authorized to view student records via the protected <code class="bg-sky-100 px-1 py-0.5 rounded text-[11px]">GET /api/students</code> endpoint. Administrative privileges (<code class="bg-sky-100 px-1 py-0.5 rounded text-[11px]">role:admin</code>) are required to create, update, or delete records.
          </p>
        </div>
      </div>

      <!-- Administrator-Only Student Management Form -->
      <StudentForm 
        v-if="isAdmin"
        :selectedStudent="selectedStudent" 
        :successMessage="formSuccess"
        :errorMessage="formError"
        :validationErrors="formValidationErrors"
        :submitting="submitting"
        @save-student="saveStudent" 
        @cancel-edit="cancelEdit" 
      />

      <!-- Student Records Directory -->
      <StudentList 
        :students="students" 
        :loading="loading" 
        :canManage="isAdmin"
        :deletingId="deletingId"
        @edit-student="editStudent" 
        @delete-student="deleteStudent" 
      />
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import LoginForm from './components/LoginForm.vue';
import StudentForm from './components/StudentForm.vue';
import StudentList from './components/StudentList.vue';

// ==========================================
// Application State managed via Composition API
// ==========================================
const students = ref([]);
const selectedStudent = ref(null);
const loading = ref(false);
const submitting = ref(false);
const deletingId = ref(null);

// Feedback notifications state
const globalSuccess = ref('');
const globalError = ref('');
const formSuccess = ref('');
const formError = ref('');
const formValidationErrors = ref({});

// Authentication & Session state
const token = ref(localStorage.getItem('token') || '');
const currentUser = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const showTokenInspector = ref(false);

// Computed properties for RBAC
const isAuthenticated = computed(() => !!token.value);
const isAdmin = computed(() => currentUser.value?.role === 'admin');

// ==========================================
// Methods / Actions
// ==========================================

const onLoginSuccess = ({ token: newToken, user: newUser }) => {
  token.value = newToken;
  currentUser.value = newUser;

  localStorage.setItem('token', newToken);
  localStorage.setItem('user', JSON.stringify(newUser));

  // Configure Sanctum Bearer token globally on Axios instance
  window.axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;

  clearFeedback();
  globalSuccess.value = `Welcome back, ${newUser.name}! Signed in as ${newUser.role.toUpperCase()}.`;
  setTimeout(() => {
    globalSuccess.value = '';
  }, 4000);

  fetchStudents();
};

const logout = async () => {
  try {
    if (token.value) {
      await window.axios.post('/api/logout');
    }
  } catch (err) {
    // Proceed with client logout even if token revocation fails on server
  } finally {
    token.value = '';
    currentUser.value = null;
    students.value = [];
    selectedStudent.value = null;
    showTokenInspector.value = false;
    clearFeedback();

    localStorage.removeItem('token');
    localStorage.removeItem('user');
    delete window.axios.defaults.headers.common['Authorization'];

    globalSuccess.value = 'You have been successfully signed out.';
    setTimeout(() => {
      globalSuccess.value = '';
    }, 4000);
  }
};

const fetchStudents = async () => {
  if (!token.value) return;

  loading.value = true;
  globalError.value = '';

  try {
    const response = await window.axios.get('/api/students');
    students.value = response.data;
  } catch (error) {
    if (error.response && error.response.status === 401) {
      logout();
      globalError.value = 'Your session has expired. Please sign in again.';
    } else {
      globalError.value = 'Failed to fetch student records from API.';
    }
  } finally {
    loading.value = false;
  }
};

const saveStudent = async (studentData) => {
  submitting.value = true;
  formError.value = '';
  formSuccess.value = '';
  formValidationErrors.value = {};

  const isEdit = !!studentData.id;

  try {
    const response = isEdit
      ? await window.axios.put(`/api/students/${studentData.id}`, studentData)
      : await window.axios.post('/api/students', studentData);

    const msg = response.data?.message || (isEdit ? 'Student updated successfully!' : 'Student created successfully!');
    formSuccess.value = msg;
    globalSuccess.value = msg;

    // Refresh student list from protected API
    await fetchStudents();
    cancelEdit();

    setTimeout(() => {
      formSuccess.value = '';
      globalSuccess.value = '';
    }, 4000);
  } catch (error) {
    if (error.response) {
      if (error.response.status === 422) {
        formValidationErrors.value = error.response.data.errors || {};
        formError.value = 'Please correct the validation errors below.';
      } else if (error.response.status === 403) {
        formError.value = 'Unauthorized: Only Administrators can create or update student records.';
      } else {
        formError.value = error.response.data?.message || 'Failed to save student record.';
      }
    } else {
      formError.value = 'Network error: Unable to reach API server.';
    }
  } finally {
    submitting.value = false;
  }
};

const deleteStudent = async (id) => {
  if (!confirm('Are you sure you want to delete this student record?')) {
    return;
  }

  deletingId.value = id;
  clearFeedback();

  try {
    const response = await window.axios.delete(`/api/students/${id}`);
    const msg = response.data?.message || 'Student record deleted successfully!';
    globalSuccess.value = msg;

    await fetchStudents();

    setTimeout(() => {
      globalSuccess.value = '';
    }, 4000);
  } catch (error) {
    if (error.response?.status === 403) {
      globalError.value = 'Forbidden: Only Administrators have permission to delete students.';
    } else {
      globalError.value = error.response?.data?.message || 'Failed to delete student record.';
    }
  } finally {
    deletingId.value = null;
  }
};

const editStudent = (student) => {
  selectedStudent.value = { ...student };
  formError.value = '';
  formSuccess.value = '';
  formValidationErrors.value = {};
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelEdit = () => {
  selectedStudent.value = null;
  formError.value = '';
  formValidationErrors.value = {};
};

const clearFeedback = () => {
  globalSuccess.value = '';
  globalError.value = '';
  formSuccess.value = '';
  formError.value = '';
  formValidationErrors.value = {};
};

// ==========================================
// Lifecycle Hook
// ==========================================
onMounted(() => {
  if (token.value) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
    fetchStudents();
  }
});
</script>
