<x-layouts.app title="Faculty Dashboard">
    <!-- Stat Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Total Students -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Students</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalStudents) }}</div>
                <div class="text-xs text-slate-500 mt-1">Enrolled in department</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <!-- Active Subjects -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Subjects</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalSubjects) }}</div>
                <div class="text-xs text-slate-500 mt-1">Curriculum courses</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>

        <!-- Class Average -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Class Average</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $avgGrade }}%</div>
                <div class="text-xs text-slate-500 mt-1">Across final scores</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>

        <!-- Passing Rate -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Passing Rate</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">{{ $passingRate }}%</div>
                <div class="text-xs text-slate-500 mt-1">{{ $passedCount }} of {{ $gradedCount }} passing</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Banner -->
    <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-violet-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-indigo-600/20 mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-md mb-2">
                Faculty Portal
            </span>
            <h3 class="text-xl sm:text-2xl font-extrabold tracking-tight">Welcome, {{ auth()->user()->name }}!</h3>
            <p class="text-indigo-100 text-sm mt-1 max-w-xl">Manage your students, courses, and encode term grades seamlessly with automated calculation formulas.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.grades.index') }}" class="px-5 py-3 bg-white text-indigo-700 font-bold text-sm rounded-xl shadow-md hover:bg-indigo-50 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Encode Grades
            </a>
            <a href="{{ route('teacher.students.index') }}" class="px-4 py-3 bg-indigo-800/60 hover:bg-indigo-800 text-white font-semibold text-sm rounded-xl border border-white/20 transition">
                + Add Student
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Grades Feed -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <div>
                    <h4 class="font-bold text-slate-900 text-base">Recent Grade Records</h4>
                    <p class="text-xs text-slate-500">Latest updated student submissions</p>
                </div>
                <a href="{{ route('teacher.grades.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">View All &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80 rounded-lg">
                            <th class="py-3 px-4 rounded-l-lg">Student</th>
                            <th class="py-3 px-3">Subject</th>
                            <th class="py-3 px-3 text-center">Prelim</th>
                            <th class="py-3 px-3 text-center">Midterm</th>
                            <th class="py-3 px-3 text-center">Finals</th>
                            <th class="py-3 px-3 text-center">Final Grade</th>
                            <th class="py-3 px-4 rounded-r-lg text-center">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentGrades as $rg)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $rg->student->user->name ?? 'Student' }}
                                    <div class="text-[11px] text-slate-400 font-mono font-normal">{{ $rg->student->student_id_number }}</div>
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-md text-xs font-bold font-mono">
                                        {{ $rg->subject->subject_code }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center text-slate-600">{{ $rg->prelim !== null ? number_format($rg->prelim, 2) : '-' }}</td>
                                <td class="py-3.5 px-3 text-center text-slate-600">{{ $rg->midterm !== null ? number_format($rg->midterm, 2) : '-' }}</td>
                                <td class="py-3.5 px-3 text-center text-slate-600">{{ $rg->finals !== null ? number_format($rg->finals, 2) : '-' }}</td>
                                <td class="py-3.5 px-3 text-center font-bold text-slate-900">
                                    {{ $rg->final_grade !== null ? number_format($rg->final_grade, 2) : '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $rg->remarks === 'Passed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($rg->remarks === 'Failed' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600') }}">
                                        {{ $rg->remarks }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-400">No grades recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Students Leaderboard -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs flex flex-col">
            <div class="pb-4 mb-4 border-b border-slate-100">
                <h4 class="font-bold text-slate-900 text-base">Top Academic Standings</h4>
                <p class="text-xs text-slate-500">Highest General Weighted Averages (GWA)</p>
            </div>

            <div class="space-y-3 flex-1">
                @forelse($topStudents as $rank => $s)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-indigo-200 transition">
                        <div class="flex items-center gap-3">
                            <span class="w-6 text-center font-black text-sm {{ $loop->first ? 'text-indigo-600' : 'text-slate-400' }}">
                                #{{ $loop->iteration }}
                            </span>
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ $s->user->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $s->course_section }}</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg text-xs font-bold">
                            {{ $s->gwa }}%
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">No graded students yet.</p>
                @endforelse
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('teacher.students.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                    Open Student Directory &rarr;
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
