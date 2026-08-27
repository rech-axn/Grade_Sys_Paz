<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user()->student;

        if (!$student) {
            return view('student.no-profile');
        }

        $student->load(['grades.subject']);
        $grades = $student->grades->sortBy('subject.subject_code');

        return view('student.dashboard', compact('student', 'grades'));
    }
}
