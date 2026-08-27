<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Grade;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();

        $gradedCount = Grade::whereNotNull('final_grade')->count();
        $avgGrade = $gradedCount > 0 ? round(Grade::whereNotNull('final_grade')->avg('final_grade'), 2) : 0;
        $passedCount = Grade::where('remarks', 'Passed')->count();
        $passingRate = $gradedCount > 0 ? round(($passedCount / $gradedCount) * 100, 1) : 0;

        $recentGrades = Grade::with(['student.user', 'subject'])
            ->orderBy('updated_at', 'desc')
            ->limit(6)
            ->get();

        $students = Student::with('user')->get();
        $topStudents = $students->sortByDesc('gwa')->take(5);

        return view('teacher.dashboard', compact(
            'totalStudents',
            'totalSubjects',
            'avgGrade',
            'passingRate',
            'passedCount',
            'gradedCount',
            'recentGrades',
            'topStudents'
        ));
    }
}
