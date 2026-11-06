<template>
  <div class="mt-4">
    <h3 class="font-bold text-base mb-2">Student List</h3>

    <div v-if="loading" class="text-sm text-slate-500 py-2">
      Loading students...
    </div>

    <div v-else-if="students.length === 0" class="text-sm text-slate-500 py-2">
      No student records found.
    </div>

    <table v-else class="w-full text-left border-collapse border border-slate-300 text-sm">
      <thead>
        <tr class="bg-slate-100 border-b border-slate-300">
          <th class="p-2 border border-slate-300">Student ID</th>
          <th class="p-2 border border-slate-300">Name</th>
          <th class="p-2 border border-slate-300">Course</th>
          <th class="p-2 border border-slate-300">Year Level</th>
          <th v-if="canManage" class="p-2 border border-slate-300">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="student in students" :key="student.id" class="border-b border-slate-300">
          <td class="p-2 border border-slate-300">{{ student.student_id }}</td>
          <td class="p-2 border border-slate-300">{{ student.first_name }} {{ student.last_name }}</td>
          <td class="p-2 border border-slate-300">{{ student.course }}</td>
          <td class="p-2 border border-slate-300">{{ student.year_level }}</td>
          <td v-if="canManage" class="p-2 border border-slate-300 space-x-2">
            <button 
              @click="emit('edit-student', student)" 
              class="text-blue-600 hover:underline cursor-pointer"
            >
              Edit
            </button>
            <button 
              @click="emit('delete-student', student.id)" 
              :disabled="deletingId === student.id"
              class="text-red-600 hover:underline cursor-pointer disabled:opacity-50"
            >
              {{ deletingId === student.id ? 'Deleting...' : 'Delete' }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
const props = defineProps({
  students: { type: Array, required: true },
  loading: { type: Boolean, default: false },
  canManage: { type: Boolean, default: false },
  deletingId: { type: [Number, String, null], default: null }
});

const emit = defineEmits(['edit-student', 'delete-student']);
</script>
