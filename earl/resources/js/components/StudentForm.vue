<template>
  <div class="bg-white p-6 rounded-xl shadow-md mb-6 border border-slate-200">
    <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
      <h3 class="text-lg font-bold text-slate-800 flex items-center space-x-2">
        <span class="inline-block w-2.5 h-2.5 rounded-full" :class="isEditing ? 'bg-amber-500' : 'bg-indigo-600'"></span>
        <span>{{ isEditing ? 'Edit Student Record' : 'Add New Student Record' }}</span>
      </h3>
      <span v-if="isEditing" class="text-xs bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full font-medium">
        Editing Student #{{ form.student_id }}
      </span>
    </div>
    
    <!-- Success Feedback Banner -->
    <div v-if="successMessage" class="mb-4 p-3 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-lg text-sm flex items-center space-x-2 shadow-xs">
      <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span class="font-medium">{{ successMessage }}</span>
    </div>

    <!-- General Error Banner -->
    <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-300 text-red-700 rounded-lg text-sm flex items-center space-x-2 shadow-xs">
      <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>{{ errorMessage }}</span>
    </div>

    <!-- Controlled Form Inputs using Vue reactive() -->
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Student ID input -->
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
            Student ID <span class="text-red-500">*</span>
          </label>
          <input 
            v-model="form.student_id" 
            type="text" 
            required 
            placeholder="e.g. 2006-00123"
            :class="validationErrors?.student_id ? 'border-red-400 focus:ring-red-500 bg-red-50/20' : 'border-slate-300 focus:ring-indigo-500'"
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 text-sm transition" 
          />
          <p v-if="validationErrors?.student_id" class="text-xs text-red-600 mt-1 font-medium">
            {{ validationErrors.student_id[0] }}
          </p>
        </div>

        <!-- Course input -->
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
            Course <span class="text-red-500">*</span>
          </label>
          <input 
            v-model="form.course" 
            type="text" 
            required 
            placeholder="e.g. BSIT, BSCS, BSCpE"
            :class="validationErrors?.course ? 'border-red-400 focus:ring-red-500 bg-red-50/20' : 'border-slate-300 focus:ring-indigo-500'"
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 text-sm transition" 
          />
          <p v-if="validationErrors?.course" class="text-xs text-red-600 mt-1 font-medium">
            {{ validationErrors.course[0] }}
          </p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- First Name -->
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
            First Name <span class="text-red-500">*</span>
          </label>
          <input 
            v-model="form.first_name" 
            type="text" 
            required 
            placeholder="e.g. Juan"
            :class="validationErrors?.first_name ? 'border-red-400 focus:ring-red-500 bg-red-50/20' : 'border-slate-300 focus:ring-indigo-500'"
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 text-sm transition" 
          />
          <p v-if="validationErrors?.first_name" class="text-xs text-red-600 mt-1 font-medium">
            {{ validationErrors.first_name[0] }}
          </p>
        </div>

        <!-- Last Name -->
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
            Last Name <span class="text-red-500">*</span>
          </label>
          <input 
            v-model="form.last_name" 
            type="text" 
            required 
            placeholder="e.g. Dela Cruz"
            :class="validationErrors?.last_name ? 'border-red-400 focus:ring-red-500 bg-red-50/20' : 'border-slate-300 focus:ring-indigo-500'"
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 text-sm transition" 
          />
          <p v-if="validationErrors?.last_name" class="text-xs text-red-600 mt-1 font-medium">
            {{ validationErrors.last_name[0] }}
          </p>
        </div>

        <!-- Year Level -->
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
            Year Level (1-8) <span class="text-red-500">*</span>
          </label>
          <input 
            v-model.number="form.year_level" 
            type="number" 
            min="1" 
            max="8" 
            required 
            :class="validationErrors?.year_level ? 'border-red-400 focus:ring-red-500 bg-red-50/20' : 'border-slate-300 focus:ring-indigo-500'"
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 text-sm transition" 
          />
          <p v-if="validationErrors?.year_level" class="text-xs text-red-600 mt-1 font-medium">
            {{ validationErrors.year_level[0] }}
          </p>
        </div>
      </div>

      <!-- Action Buttons with Loading State Feedback -->
      <div class="flex items-center space-x-3 pt-2">
        <button 
          type="submit" 
          :disabled="submitting"
          :class="isEditing ? 'bg-amber-600 hover:bg-amber-700' : 'bg-indigo-600 hover:bg-indigo-700'"
          class="px-5 py-2.5 text-white rounded-lg transition font-medium text-sm disabled:opacity-50 flex items-center space-x-2 shadow-xs cursor-pointer"
        >
          <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
          <span>{{ submitting ? 'Saving Record...' : (isEditing ? 'Update Student Record' : 'Save Student Record') }}</span>
        </button>

        <button 
          v-if="isEditing" 
          type="button" 
          @click="$emit('cancel-edit')" 
          class="px-4 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg transition font-medium text-sm cursor-pointer"
        >
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, computed, watch } from 'vue';

const props = defineProps({
  selectedStudent: { type: Object, default: null },
  errorMessage: { type: String, default: '' },
  successMessage: { type: String, default: '' },
  validationErrors: { type: Object, default: () => ({}) },
  submitting: { type: Boolean, default: false }
});

const emit = defineEmits(['save-student', 'cancel-edit']);

// Manage form state using Vue 3 reactive()
const form = reactive({
  id: null,
  student_id: '',
  first_name: '',
  last_name: '',
  course: '',
  year_level: 1
});

// Computed property to determine edit state
const isEditing = computed(() => !!props.selectedStudent);

// Watch for changes in selectedStudent prop to populate form
watch(
  () => props.selectedStudent,
  (newVal) => {
    if (newVal) {
      form.id = newVal.id;
      form.student_id = newVal.student_id;
      form.first_name = newVal.first_name;
      form.last_name = newVal.last_name;
      form.course = newVal.course;
      form.year_level = newVal.year_level;
    } else {
      resetForm();
    }
  },
  { immediate: true }
);

const handleSubmit = () => {
  emit('save-student', { ...form });
};

const resetForm = () => {
  form.id = null;
  form.student_id = '';
  form.first_name = '';
  form.last_name = '';
  form.course = '';
  form.year_level = 1;
};
</script>
