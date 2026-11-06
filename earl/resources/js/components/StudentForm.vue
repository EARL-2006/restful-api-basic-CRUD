<template>
  <div class="border border-slate-300 p-4 mb-4 bg-white">
    <h3 class="font-bold text-base mb-3">
      {{ isEditing ? 'Edit Student' : 'Add Student' }}
    </h3>

    <form @submit.prevent="handleSubmit" class="space-y-3">
      <div>
        <label class="block text-xs font-medium mb-1">Student ID:</label>
        <input 
          v-model="form.student_id" 
          type="text" 
          required 
          placeholder="Student ID"
          class="border border-slate-300 px-2 py-1 w-full text-sm" 
        />
      </div>

      <div>
        <label class="block text-xs font-medium mb-1">First Name:</label>
        <input 
          v-model="form.first_name" 
          type="text" 
          required 
          placeholder="First Name"
          class="border border-slate-300 px-2 py-1 w-full text-sm" 
        />
      </div>

      <div>
        <label class="block text-xs font-medium mb-1">Last Name:</label>
        <input 
          v-model="form.last_name" 
          type="text" 
          required 
          placeholder="Last Name"
          class="border border-slate-300 px-2 py-1 w-full text-sm" 
        />
      </div>

      <div>
        <label class="block text-xs font-medium mb-1">Course:</label>
        <input 
          v-model="form.course" 
          type="text" 
          required 
          placeholder="Course"
          class="border border-slate-300 px-2 py-1 w-full text-sm" 
        />
      </div>

      <div>
        <label class="block text-xs font-medium mb-1">Year Level:</label>
        <input 
          v-model.number="form.year_level" 
          type="number" 
          min="1" 
          max="8" 
          required 
          placeholder="Year Level"
          class="border border-slate-300 px-2 py-1 w-full text-sm" 
        />
      </div>

      <div class="flex gap-2 pt-1">
        <button 
          type="submit" 
          :disabled="submitting"
          class="border border-slate-400 bg-slate-100 hover:bg-slate-200 px-4 py-1 text-sm font-medium cursor-pointer disabled:opacity-50"
        >
          {{ submitting ? 'Saving...' : (isEditing ? 'Update Student' : 'Add Student') }}
        </button>

        <button 
          v-if="isEditing" 
          type="button" 
          @click="$emit('cancel-edit')" 
          class="border border-slate-300 px-4 py-1 text-sm cursor-pointer"
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
  submitting: { type: Boolean, default: false }
});

const emit = defineEmits(['save-student', 'cancel-edit']);

const form = reactive({
  id: null,
  student_id: '',
  first_name: '',
  last_name: '',
  course: '',
  year_level: 1
});

const isEditing = computed(() => !!props.selectedStudent);

function resetForm() {
  form.id = null;
  form.student_id = '';
  form.first_name = '';
  form.last_name = '';
  form.course = '';
  form.year_level = 1;
}

function handleSubmit() {
  emit('save-student', { ...form });
}

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
</script>
