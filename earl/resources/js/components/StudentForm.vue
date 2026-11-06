<template>
  <div class="bg-white p-6 rounded-lg shadow-md mb-6 border border-slate-200">
    <h3 class="text-lg font-semibold text-slate-800 mb-4">{{ isEditing ? 'Edit Student' : 'Add New Student' }}</h3>
    
    <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 text-red-700 rounded-md text-sm">
      {{ errorMessage }}
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Student ID:</label>
        <input v-model="form.student_id" type="text" required class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">First Name:</label>
        <input v-model="form.first_name" type="text" required class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Last Name:</label>
        <input v-model="form.last_name" type="text" required class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Course:</label>
        <input v-model="form.course" type="text" required class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Year Level:</label>
        <input v-model.number="form.year_level" type="number" min="1" max="8" required class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>

      <div class="flex space-x-3 pt-2">
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition font-medium">
          {{ isEditing ? 'Update Student' : 'Add Student' }}
        </button>
        <button v-if="isEditing" type="button" @click="$emit('cancel-edit')" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300 transition font-medium">
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  props: {
    selectedStudent: { type: Object, default: null },
    errorMessage: { type: String, default: '' }
  },
  emits: ['save-student', 'cancel-edit'],
  data() {
    return {
      form: { id: null, student_id: '', first_name: '', last_name: '', course: '', year_level: 1 }
    };
  },
  computed: {
    isEditing() {
      return !!this.selectedStudent;
    }
  },
  watch: {
    selectedStudent: {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          this.form = { ...newVal };
        } else {
          this.resetForm();
        }
      }
    }
  },
  methods: {
    handleSubmit() {
      this.$emit('save-student', { ...this.form });
    },
    resetForm() {
      this.form = { id: null, student_id: '', first_name: '', last_name: '', course: '', year_level: 1 };
    }
  }
};
</script>