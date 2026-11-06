<template>
  <div class="bg-white p-6 rounded-xl shadow-md border border-slate-200">
    <!-- Header with count and live filter -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5 pb-3 border-b border-slate-100">
      <div>
        <h3 class="text-lg font-bold text-slate-800">Student Directory</h3>
        <p class="text-xs text-slate-500">
          Total records: <span class="font-semibold text-slate-700">{{ students.length }}</span>
          <span v-if="searchQuery"> (Filtered: {{ filteredStudents.length }})</span>
        </p>
      </div>

      <!-- Controlled search filter using ref() -->
      <div class="w-full sm:w-64">
        <div class="relative">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search by ID, name, course..."
            class="w-full pl-9 pr-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Loading State Feedback -->
    <div v-if="loading" class="py-12 flex flex-col items-center justify-center text-slate-500 space-y-3">
      <svg class="animate-spin h-7 w-7 text-indigo-600" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
      </svg>
      <span class="text-sm font-medium">Loading student records via API...</span>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredStudents.length === 0" class="py-10 text-center text-slate-500">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <p class="text-sm font-medium">{{ searchQuery ? 'No matching students found.' : 'No student records in the database.' }}</p>
    </div>

    <!-- Student Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
            <th class="p-3 font-semibold">Student ID</th>
            <th class="p-3 font-semibold">Full Name</th>
            <th class="p-3 font-semibold">Course</th>
            <th class="p-3 font-semibold">Year Level</th>
            <th v-if="canManage" class="p-3 font-semibold text-right">Actions (Admin)</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
          <tr 
            v-for="student in filteredStudents" 
            :key="student.id" 
            class="hover:bg-slate-50/70 transition"
          >
            <td class="p-3 font-mono text-xs font-semibold text-indigo-700">{{ student.student_id }}</td>
            <td class="p-3 font-medium text-slate-800">{{ student.first_name }} {{ student.last_name }}</td>
            <td class="p-3 text-slate-600">
              <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs font-medium">
                {{ student.course }}
              </span>
            </td>
            <td class="p-3 text-slate-600">
              <span class="text-xs font-medium text-slate-700">Year {{ student.year_level }}</span>
            </td>

            <!-- Restricted Action Buttons: Only rendered for Admin role -->
            <td v-if="canManage" class="p-3 text-right space-x-2">
              <button 
                @click="emit('edit-student', student)" 
                class="px-2.5 py-1 text-xs font-semibold text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 rounded transition cursor-pointer"
              >
                Edit
              </button>
              <button 
                @click="emit('delete-student', student.id)" 
                :disabled="deletingId === student.id"
                class="px-2.5 py-1 text-xs font-semibold text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 rounded transition disabled:opacity-50 cursor-pointer"
              >
                {{ deletingId === student.id ? 'Deleting...' : 'Delete' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  students: { type: Array, required: true },
  loading: { type: Boolean, default: false },
  canManage: { type: Boolean, default: false },
  deletingId: { type: [Number, String, null], default: null }
});

const emit = defineEmits(['edit-student', 'delete-student']);

// Search filter state managed via Composition API ref()
const searchQuery = ref('');

// Filtered student list computed from searchQuery and students props
const filteredStudents = computed(() => {
  if (!searchQuery.value.trim()) {
    return props.students;
  }
  const query = searchQuery.value.toLowerCase();
  return props.students.filter(student => 
    student.student_id?.toLowerCase().includes(query) ||
    student.first_name?.toLowerCase().includes(query) ||
    student.last_name?.toLowerCase().includes(query) ||
    student.course?.toLowerCase().includes(query)
  );
});
</script>
