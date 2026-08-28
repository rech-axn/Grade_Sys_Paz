<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\StudentController as TeacherStudentController;
use App\Http\Controllers\Teacher\SubjectController as TeacherSubjectController;
use App\Http\Controllers\Teacher\GradeController as TeacherGradeController;
use App\Http\Controllers\Teacher\ReportController as TeacherReportController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\GradeController as StudentGradeController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;

// Public / Auth Routes
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isTeacher() 
            ? redirect()->route('teacher.dashboard') 
            : redirect()->route('student.dashboard');
    }
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Teacher Portal Routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->as('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    
    // Students
    Route::get('/students', [TeacherStudentController::class, 'index'])->name('students.index');
    Route::post('/students', [TeacherStudentController::class, 'store'])->name('students.store');
    Route::put('/students/{student}', [TeacherStudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [TeacherStudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/students/{student}/report', [TeacherReportController::class, 'show'])->name('students.report');

    // Subjects
    Route::get('/subjects', [TeacherSubjectController::class, 'index'])->name('subjects.index');
    Route::post('/subjects', [TeacherSubjectController::class, 'store'])->name('subjects.store');
    Route::put('/subjects/{subject}', [TeacherSubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [TeacherSubjectController::class, 'destroy'])->name('subjects.destroy');

    // Grades
    Route::get('/grades', [TeacherGradeController::class, 'index'])->name('grades.index');
    Route::post('/grades', [TeacherGradeController::class, 'store'])->name('grades.store');
    Route::delete('/grades/{grade}', [TeacherGradeController::class, 'destroy'])->name('grades.destroy');
});

// Student Portal Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->as('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-grades', [StudentGradeController::class, 'index'])->name('grades.index');
    Route::get('/profile', [StudentProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/password', [StudentProfileController::class, 'updatePassword'])->name('profile.password');
});
