<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user()->student;
        $student->load(['grades.subject']);
        $grades = $student->grades->sortBy('subject.subject_code');

        return view('student.grades', compact('student', 'grades'));
    }
}
