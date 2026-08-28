<x-layouts.app title="Official Term Grade Slip">
    <div class="mb-6 no-print flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-slate-900 tracking-tight">My Academic Performance</h3>
            <p class="text-xs text-slate-500 mt-0.5">First Semester &bull; Academic Year 2025-2026</p>
        </div>
        <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Grade Slip
        </button>
    </div>

    <!-- Official Grade Slip Container -->
    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/80 shadow-xs max-w-4xl mx-auto print-container">
        <!-- School Header -->
        <div class="text-center pb-6 mb-8 border-b border-slate-200">
            <div class="w-12 h-12 mx-auto mb-2 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center font-extrabold text-white text-lg shadow-md shadow-indigo-500/30">
                GS
            </div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">GRADE SYSTEM ACADEMY</h2>
            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">Student Portal &bull; Official Term Grade Slip</p>
        </div>

        <!-- Student Meta Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Student Name</div>
                <div class="text-base font-bold text-slate-900 mt-0.5">{{ $student->user->name }}</div>
                
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-3">Student ID Number</div>
                <div class="text-sm font-mono font-bold text-indigo-600">{{ $student->student_id_number }}</div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Program & Year</div>
                <div class="text-base font-bold text-slate-900 mt-0.5">{{ $student->course_section }} ({{ $student->year_level }})</div>

                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-3">Term & Year</div>
                <div class="text-sm font-semibold text-slate-700">1st Semester, 2025-2026</div>
            </div>
        </div>

        <!-- Grades Table -->
        <div class="border border-slate-200 rounded-2xl overflow-hidden mb-8">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50 border-b border-slate-200">
                        <th class="py-3 px-4">Subject Code</th>
                        <th class="py-3 px-4">Course Description</th>
                        <th class="py-3 px-3 text-center">Units</th>
                        <th class="py-3 px-3 text-center">Prelim (30%)</th>
                        <th class="py-3 px-3 text-center">Midterm (30%)</th>
                        <th class="py-3 px-3 text-center">Finals (40%)</th>
                        <th class="py-3 px-3 text-center">Final Grade</th>
                        <th class="py-3 px-4 text-center">Remarks</th>
                        <th class="py-3 px-4">Instructor Feedback</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($grades as $g)
                        <tr>
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">{{ $g->subject->subject_code }}</td>
                            <td class="py-3.5 px-4 font-medium text-slate-900">{{ $g->subject->subject_title }}</td>
                            <td class="py-3.5 px-3 text-center text-slate-600 font-semibold">{{ $g->subject->units }}</td>
                            <td class="py-3.5 px-3 text-center text-slate-600">{{ $g->prelim !== null ? number_format($g->prelim, 2) : '—' }}</td>
                            <td class="py-3.5 px-3 text-center text-slate-600">{{ $g->midterm !== null ? number_format($g->midterm, 2) : '—' }}</td>
                            <td class="py-3.5 px-3 text-center text-slate-600">{{ $g->finals !== null ? number_format($g->finals, 2) : '—' }}</td>
                            <td class="py-3.5 px-3 text-center font-extrabold text-slate-900">
                                {{ $g->final_grade !== null ? number_format($g->final_grade, 2) : '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $g->remarks === 'Passed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($g->remarks === 'Failed' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600') }}">
                                    {{ $g->remarks }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-500">
                                {{ $g->notes ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-6 text-center text-slate-400">No grades registered for this term.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Summary Calculation Card -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Passed Units</div>
                <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $student->passed_units }} of {{ $student->total_units }} Credits Earned</div>
            </div>
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">General Weighted Average</div>
                <div class="text-2xl font-black text-indigo-600">{{ $student->gwa > 0 ? $student->gwa . '%' : 'N/A' }}</div>
            </div>
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Academic Standing</div>
                <div class="text-sm font-bold {{ $student->gwa >= 75.0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $student->academic_status }}
                </div>
            </div>
        </div>

        <!-- Grading Legend -->
        <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-100 text-xs text-slate-500">
            <div class="font-bold text-slate-700 uppercase mb-1.5 text-[11px]">Grading Scale Guide:</div>
            <div class="flex flex-wrap gap-4 text-[11px]">
                <span><strong class="text-slate-800">95.00 - 100.00:</strong> 1.00 (Excellent)</span>
                <span><strong class="text-slate-800">90.00 - 94.99:</strong> 1.25 - 1.50 (Superior)</span>
                <span><strong class="text-slate-800">85.00 - 89.99:</strong> 1.75 - 2.00 (Good)</span>
                <span><strong class="text-slate-800">75.00 - 84.99:</strong> 2.25 - 3.00 (Passed)</span>
                <span><strong class="text-rose-600">Below 75.00:</strong> 5.00 (Failed)</span>
            </div>
        </div>

        <div class="text-center text-[11px] text-slate-400 pt-8 mt-8 border-t border-slate-200">
            Generated electronically via GradeSys Portal on {{ date('F j, Y, g:i a') }}.
        </div>
    </div>
</x-layouts.app>
