<x-layouts.app title="Student Dashboard">
    <!-- Welcome Header Banner -->
    <div class="bg-gradient-to-r from-sky-500 via-indigo-600 to-indigo-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-indigo-600/20 mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-md mb-2">
                {{ $student->student_id_number }}
            </span>
            <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Hello, {{ $student->user->name }}!</h3>
            <p class="text-sky-100 text-sm mt-1">{{ $student->course_section }} &bull; {{ $student->year_level }}</p>
        </div>
        <div>
            <a href="{{ route('student.grades.index') }}" class="px-5 py-3 bg-white text-indigo-700 font-bold text-sm rounded-xl shadow-md hover:bg-indigo-50 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                View Official Grade Slip
            </a>
        </div>
    </div>

    <!-- Student Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <!-- GWA -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">General Weighted Average</div>
                <div class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $student->gwa > 0 ? $student->gwa . '%' : 'N/A' }}</div>
                <div class="text-xs text-slate-500 mt-1">Weighted by credit units</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>

        <!-- Academic Standing -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Academic Standing</div>
                <div class="text-xl font-extrabold {{ $student->gwa >= 75.0 ? 'text-emerald-600' : 'text-rose-600' }} mt-1">
                    {{ $student->academic_status }}
                </div>
                <div class="text-xs text-slate-500 mt-1">Evaluation status</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Units Passed -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Credits Earned / Enrolled</div>
                <div class="text-3xl font-extrabold text-slate-900 mt-1">
                    {{ $student->passed_units }} <span class="text-sm font-semibold text-slate-400">/ {{ $student->total_units }} Units</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">{{ $grades->count() }} enrolled courses</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>
    </div>

    <!-- Registered Courses Summary -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
            <div>
                <h4 class="font-bold text-slate-900 text-base">Current Enrolled Courses</h4>
                <p class="text-xs text-slate-500">First Semester, A.Y. 2025-2026</p>
            </div>
            <a href="{{ route('student.grades.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Detailed Report Card &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80 border-b border-slate-100">
                        <th class="py-3 px-4">Subject Code</th>
                        <th class="py-3 px-4">Descriptive Title</th>
                        <th class="py-3 px-3 text-center">Units</th>
                        <th class="py-3 px-3 text-center">Prelim</th>
                        <th class="py-3 px-3 text-center">Midterm</th>
                        <th class="py-3 px-3 text-center">Finals</th>
                        <th class="py-3 px-3 text-center">Final Grade</th>
                        <th class="py-3 px-4 text-center">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($grades as $g)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-indigo-600">{{ $g->subject->subject_code }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $g->subject->subject_title }}</td>
                            <td class="py-3.5 px-3 text-center font-semibold text-slate-600">{{ $g->subject->units }}</td>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">You are not registered in any courses.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
