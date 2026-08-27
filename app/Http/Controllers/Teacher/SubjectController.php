<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount('grades')
            ->with(['grades' => function ($q) {
                $q->whereNotNull('final_grade');
            }])
            ->get()
            ->sortBy('subject_code');

        return view('teacher.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_code' => ['required', 'string', 'max:20', 'unique:subjects,subject_code'],
            'subject_title' => ['required', 'string', 'max:255'],
            'units' => ['required', 'integer', 'min:1', 'max:12'],
            'semester' => ['required', 'string', 'max:50'],
            'academic_year' => ['required', 'string', 'max:50'],
        ]);

        Subject::create($validated);

        return redirect()->route('teacher.subjects.index')->with('success', "Subject {$validated['subject_code']} created successfully.");
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'subject_code' => ['required', 'string', 'max:20', 'unique:subjects,subject_code,' . $subject->id],
            'subject_title' => ['required', 'string', 'max:255'],
            'units' => ['required', 'integer', 'min:1', 'max:12'],
            'semester' => ['required', 'string', 'max:50'],
            'academic_year' => ['required', 'string', 'max:50'],
        ]);

        $subject->update($validated);

        return redirect()->route('teacher.subjects.index')->with('success', "Subject {$subject->subject_code} updated successfully.");
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('teacher.subjects.index')->with('success', 'Subject removed.');
    }
}
