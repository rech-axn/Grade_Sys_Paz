<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;

class ReportController extends Controller
{
    public function show(Student $student)
    {
        $student->load(['user', 'grades.subject']);
        $grades = $student->grades->sortBy('subject.subject_code');

        return view('teacher.students.report', compact('student', 'grades'));
    }
}
