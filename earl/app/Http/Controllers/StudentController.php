<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        return response()->json(Student::all(), 200);
    }

    public function show(Student $student)
    {
        return response()->json($student, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|max:50|unique:students,student_id',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:students,email',
            'phone'      => 'required|string|max:11', 
            'address'    => 'required|string|max:255',
        ]);

        $student = Student::create($validated);

        return response()->json([
            'message' => 'Student created successfully',
            'student' => $student,
        ], 201);
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'student_id')->ignore($student->id),
            ],
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => [
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student->id),
            ],
            'phone'      => 'required|string|max:11',
            'address'    => 'required|string|max:255',
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully',
            'student' => $student,
        ], 200);
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully',
        ], 200);
    }
}