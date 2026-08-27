<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $selectedSubjectId = $request->query('subject_id');
        $selectedStudentId = $request->query('student_id');

        $subjects = Subject::orderBy('subject_code')->get();
        $students = Student::with('user')->get()->sortBy('user.name');

        $query = Grade::with(['student.user', 'subject']);

        if ($selectedSubjectId) {
            $query->where('subject_id', $selectedSubjectId);
        }

        if ($selectedStudentId) {
            $query->where('student_id', $selectedStudentId);
        }

        $grades = $query->get()->sortBy(['subject.subject_code', 'student.user.name']);

        $gradedScores = $grades->pluck('final_grade')->filter(fn($v) => $v !== null);
        $stats = [
            'count' => $grades->count(),
            'avg' => $gradedScores->isNotEmpty() ? round($gradedScores->avg(), 2) : 0,
            'max' => $gradedScores->isNotEmpty() ? $gradedScores->max() : 0,
            'min' => $gradedScores->isNotEmpty() ? $gradedScores->min() : 0,
        ];

        return view('teacher.grades.index', compact('grades', 'subjects', 'students', 'selectedSubjectId', 'selectedStudentId', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'prelim' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'midterm' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'finals' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        Grade::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'subject_id' => $validated['subject_id'],
            ],
            [
                'prelim' => $validated['prelim'] !== null ? (float)$validated['prelim'] : null,
                'midterm' => $validated['midterm'] !== null ? (float)$validated['midterm'] : null,
                'finals' => $validated['finals'] !== null ? (float)$validated['finals'] : null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->route('teacher.grades.index', [
            'subject_id' => $validated['subject_id']
        ])->with('success', 'Grade saved and calculated successfully.');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        return back()->with('success', 'Grade record deleted.');
    }
}
