<template>
  <div class="max-w-3xl mx-auto p-4 font-sans text-slate-800">
    <h2 class="text-xl font-bold mb-4">Student Management System</h2>

    <!-- LOGIN VIEW ONLY: Do not show the add student form or table while in login form -->
    <div v-if="!isAuthenticated">
      <LoginForm @login-success="onLoginSuccess" />
    </div>

    <!-- MAIN VIEW: When logged in -->
    <div v-else>
      <!-- Simple header info and logout -->
      <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-300 text-sm">
        <div>
          Logged in as: <strong>{{ currentUser?.name }}</strong> (Role: {{ currentUser?.role }})
        </div>
        <button 
          @click="logout" 
          class="border border-slate-400 bg-slate-100 hover:bg-slate-200 px-3 py-1 text-xs cursor-pointer"
        >
          Switch User / Logout
        </button>
      </div>

      <!-- Feedback messages -->
      <div v-if="successMessage" class="mb-3 p-2 bg-green-50 border border-green-300 text-green-800 text-sm">
        {{ successMessage }}
      </div>
      <div v-if="errorMessage" class="mb-3 p-2 bg-red-50 border border-red-300 text-red-700 text-sm">
        {{ errorMessage }}
      </div>

      <!-- Notice for Regular User -->
      <div v-if="!isAdmin" class="mb-3 p-2 bg-slate-100 border border-slate-200 text-slate-700 text-sm">
        Regular User mode: You have read-only access to the student list. Only Administrators can add or edit students.
      </div>

      <!-- Add/Edit Student Form: Only visible for Administrator -->
      <StudentForm 
        v-if="isAdmin"
        :selectedStudent="selectedStudent" 
        :submitting="submitting"
        @save-student="saveStudent" 
        @cancel-edit="cancelEdit" 
      />

      <!-- Table of Students -->
      <StudentList 
        :students="students" 
        :loading="loading" 
        :canManage="isAdmin"
        :deletingId="deletingId"
        @edit-student="editStudent" 
        @delete-student="deleteStudent" 
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import LoginForm from './components/LoginForm.vue';
import StudentForm from './components/StudentForm.vue';
import StudentList from './components/StudentList.vue';

// State managed via Composition API
const students = ref([]);
const selectedStudent = ref(null);
const loading = ref(false);
const submitting = ref(false);
const deletingId = ref(null);

const successMessage = ref('');
const errorMessage = ref('');

// Auth state
const token = ref(localStorage.getItem('token') || '');
const currentUser = ref(JSON.parse(localStorage.getItem('user') || 'null'));

const isAuthenticated = computed(() => !!token.value);
const isAdmin = computed(() => currentUser.value?.role === 'admin');

const onLoginSuccess = ({ token: newToken, user: newUser }) => {
  token.value = newToken;
  currentUser.value = newUser;
  localStorage.setItem('token', newToken);
  localStorage.setItem('user', JSON.stringify(newUser));
  window.axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
  errorMessage.value = '';
  successMessage.value = `Welcome, ${newUser.name}!`;
  setTimeout(() => { successMessage.value = ''; }, 3000);
  fetchStudents();
};

const logout = async () => {
  try {
    if (token.value) {
      await window.axios.post('/api/logout');
    }
  } catch (e) {
    // ignore
  } finally {
    token.value = '';
    currentUser.value = null;
    students.value = [];
    selectedStudent.value = null;
    errorMessage.value = '';
    successMessage.value = '';
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    delete window.axios.defaults.headers.common['Authorization'];
  }
};

const fetchStudents = async () => {
  if (!token.value) return;
  loading.value = true;
  try {
    const res = await window.axios.get('/api/students');
    students.value = res.data;
  } catch (err) {
    if (err.response?.status === 401) {
      logout();
      errorMessage.value = 'Session expired. Please log in again.';
    } else {
      errorMessage.value = 'Failed to fetch student records.';
    }
  } finally {
    loading.value = false;
  }
};

const saveStudent = async (data) => {
  submitting.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    const res = data.id 
      ? await window.axios.put(`/api/students/${data.id}`, data)
      : await window.axios.post('/api/students', data);

    successMessage.value = res.data?.message || (data.id ? 'Student updated successfully!' : 'Student created successfully!');
    await fetchStudents();
    cancelEdit();
    setTimeout(() => { successMessage.value = ''; }, 3000);
  } catch (err) {
    if (err.response?.status === 422 && err.response.data?.errors) {
      const firstKey = Object.keys(err.response.data.errors)[0];
      errorMessage.value = err.response.data.errors[firstKey][0];
    } else {
      errorMessage.value = err.response?.data?.message || 'Failed to save student record.';
    }
  } finally {
    submitting.value = false;
  }
};

const deleteStudent = async (id) => {
  if (!confirm('Are you sure you want to delete this student?')) return;
  deletingId.value = id;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    const res = await window.axios.delete(`/api/students/${id}`);
    successMessage.value = res.data?.message || 'Student deleted successfully!';
    await fetchStudents();
    setTimeout(() => { successMessage.value = ''; }, 3000);
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Failed to delete student.';
  } finally {
    deletingId.value = null;
  }
};

const editStudent = (student) => {
  selectedStudent.value = { ...student };
  errorMessage.value = '';
  successMessage.value = '';
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelEdit = () => {
  selectedStudent.value = null;
};

onMounted(() => {
  if (token.value) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
    fetchStudents();
  }
});
</script>
