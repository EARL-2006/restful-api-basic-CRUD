<template>
  <div class="bg-white p-6 rounded-lg shadow-md border border-slate-200">
    <h3 class="text-lg font-semibold text-slate-800 mb-4">Student Records</h3>

    <div v-if="loading" class="text-slate-500 py-4 text-center">Loading students...</div>
    <div v-else-if="students.length === 0" class="text-slate-500 py-4 text-center">No student records found.</div>

    <div v-else class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-sm">
            <th class="p-3">Student ID</th>
            <th class="p-3">Name</th>
            <th class="p-3">Course</th>
            <th class="p-3">Year Level</th>
            <th v-if="canManage" class="p-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 text-sm">
          <tr v-for="student in students" :key="student.id" class="hover:bg-slate-50 transition">
            <td class="p-3 text-slate-600">{{ student.student_id }}</td>
            <td class="p-3 font-medium text-slate-800">{{ student.first_name }} {{ student.last_name }}</td>
            <td class="p-3 text-slate-600">{{ student.course }}</td>
            <td class="p-3 text-slate-600">{{ student.year_level }}</td>
            <td v-if="canManage" class="p-3 text-right space-x-2">
              <button @click="$emit('edit-student', student)" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</button>
              <button @click="$emit('delete-student', student.id)" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    students: { type: Array, required: true },
    loading: { type: Boolean, default: false },
    canManage: { type: Boolean, default: true }
  },
  emits: ['edit-student', 'delete-student']
};
</script>