<template>
  <div class="max-w-4xl mx-auto p-6 font-sans">
    <header class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-5">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">Student Management Interface</h2>
        <p class="text-sm text-slate-500">Laravel RESTful API + Vue 3 Integration</p>
      </div>

      <div v-if="isAuthenticated" class="flex items-center space-x-3">
        <div class="text-right">
          <p class="text-sm font-semibold text-slate-800">{{ currentUser?.name }}</p>
          <span 
            :class="isAdmin ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-700'"
            class="text-xs px-2 py-0.5 rounded-full font-medium uppercase tracking-wider"
          >
            {{ currentUser?.role }}
          </span>
        </div>
        <button 
          @click="logout" 
          class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-md transition"
        >
          Sign Out
        </button>
      </div>
    </header>

    <!-- If not authenticated, show the Login Form -->
    <LoginForm v-if="!isAuthenticated" @login-success="onLoginSuccess" />

    <!-- If authenticated, show management UI -->
    <div v-else>
      <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 text-red-700 rounded-md text-sm">
        {{ errorMessage }}
      </div>

      <!-- Notice for regular user (read-only) -->
      <div v-if="!isAdmin" class="mb-6 p-4 bg-sky-50 border border-sky-200 text-sky-800 rounded-md text-sm flex items-center justify-between">
        <span>Logged in as <strong>Regular User</strong> (Read-Only access). Only Administrators can add, edit, or delete student records.</span>
      </div>

      <!-- Admin-only Student Form -->
      <StudentForm 
        v-if="isAdmin"
        :selectedStudent="selectedStudent" 
        @save-student="saveStudent" 
        @cancel-edit="cancelEdit" 
      />

      <!-- Student Records List -->
      <StudentList 
        :students="students" 
        :loading="loading" 
        :canManage="isAdmin"
        @edit-student="editStudent" 
        @delete-student="deleteStudent" 
      />
    </div>
  </div>
</template>

<script>
import LoginForm from './components/LoginForm.vue';
import StudentForm from './components/StudentForm.vue';
import StudentList from './components/StudentList.vue';

export default {
  components: { LoginForm, StudentForm, StudentList },
  data() {
    return {
      students: [],
      selectedStudent: null,
      errorMessage: '',
      loading: false,
      token: localStorage.getItem('token') || '',
      currentUser: JSON.parse(localStorage.getItem('user') || 'null')
    };
  },
  computed: {
    isAuthenticated() {
      return !!this.token;
    },
    isAdmin() {
      return this.currentUser && this.currentUser.role === 'admin';
    }
  },
  mounted() {
    if (this.token) {
      window.axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
      this.fetchStudents();
    }
  },
  methods: {
    onLoginSuccess({ token, user }) {
      this.token = token;
      this.currentUser = user;
      localStorage.setItem('token', token);
      localStorage.setItem('user', JSON.stringify(user));
      window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      this.errorMessage = '';
      this.fetchStudents();
    },
    logout() {
      if (this.token) {
        window.axios.post('/api/logout').catch(() => {});
      }
      this.token = '';
      this.currentUser = null;
      this.students = [];
      this.selectedStudent = null;
      this.errorMessage = '';
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      delete window.axios.defaults.headers.common['Authorization'];
    },
    fetchStudents() {
      if (!this.token) return;

      this.loading = true;
      this.errorMessage = '';
      window.axios.get('/api/students')
        .then(response => {
          this.students = response.data;
          this.loading = false;
        })
        .catch(error => {
          this.loading = false;
          if (error.response && error.response.status === 401) {
            this.logout();
            this.errorMessage = 'Session expired or invalid. Please sign in again.';
          } else {
            this.errorMessage = 'Failed to fetch student records.';
          }
        });
    },
    saveStudent(studentData) {
      const request = studentData.id 
        ? window.axios.put(`/api/students/${studentData.id}`, studentData)
        : window.axios.post('/api/students', studentData);

      request.then(() => {
        this.fetchStudents();
        this.cancelEdit();
      }).catch(error => {
        if (error.response && error.response.data && error.response.data.errors) {
          const firstKey = Object.keys(error.response.data.errors)[0];
          this.errorMessage = error.response.data.errors[firstKey][0];
        } else if (error.response && error.response.data && error.response.data.message) {
          this.errorMessage = error.response.data.message;
        } else {
          this.errorMessage = 'Failed to save student record.';
        }
      });
    },
    editStudent(student) {
      this.selectedStudent = { ...student };
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    cancelEdit() {
      this.selectedStudent = null;
    },
    deleteStudent(id) {
      if (confirm('Are you sure you want to delete this student?')) {
        window.axios.delete(`/api/students/${id}`)
          .then(() => {
            this.fetchStudents();
          })
          .catch(error => {
            if (error.response && error.response.data && error.response.data.message) {
              this.errorMessage = error.response.data.message;
            } else {
              this.errorMessage = 'Failed to delete student record.';
            }
          });
      }
    }
  }
};
</script>
