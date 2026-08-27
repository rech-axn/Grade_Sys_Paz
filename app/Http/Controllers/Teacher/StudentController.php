<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['user', 'grades.subject'])
            ->get()
            ->sortBy('user.name');

        return view('teacher.students.index', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'student_id_number' => ['required', 'string', 'max:50', 'unique:students,student_id_number'],
            'course_section' => ['required', 'string', 'max:100'],
            'year_level' => ['required', 'string', 'max:50'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'role' => 'student',
                'password' => Hash::make($validated['password']),
            ]);

            Student::create([
                'user_id' => $user->id,
                'student_id_number' => $validated['student_id_number'],
                'course_section' => $validated['course_section'],
                'year_level' => $validated['year_level'],
                'gender' => $validated['gender'],
                'contact_number' => $validated['contact_number'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);
        });

        return redirect()->route('teacher.students.index')->with('success', 'Student account and profile created successfully.');
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $student->user_id],
            'student_id_number' => ['required', 'string', 'max:50', 'unique:students,student_id_number,' . $student->id],
            'course_section' => ['required', 'string', 'max:100'],
            'year_level' => ['required', 'string', 'max:50'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($student, $validated) {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $student->user->update($userData);

            $student->update([
                'student_id_number' => $validated['student_id_number'],
                'course_section' => $validated['course_section'],
                'year_level' => $validated['year_level'],
                'gender' => $validated['gender'],
                'contact_number' => $validated['contact_number'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);
        });

        return redirect()->route('teacher.students.index')->with('success', 'Student record updated successfully.');
    }

    public function destroy(Student $student)
    {
        // Deleting the user cascades and removes student & grades
        $student->user->delete();
        return redirect()->route('teacher.students.index')->with('success', 'Student record removed.');
    }
}
