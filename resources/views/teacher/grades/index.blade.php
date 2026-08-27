<x-layouts.app title="Master Grade Matrix">
    <div x-data="{ 
        encodeModalOpen: false, 
        modalTitle: 'Encode Student Grade',
        selectedStudent: '{{ $selectedStudentId ?? '' }}',
        selectedSubject: '{{ $selectedSubjectId ?? '' }}',
        prelim: '',
        midterm: '',
        finals: '',
        notes: '',

        get computedGrade() {
            const p = parseFloat(this.prelim);
            const m = parseFloat(this.midterm);
            const f = parseFloat(this.finals);
            if (!isNaN(p) && !isNaN(m) && !isNaN(f)) {
                return ((p * 0.3) + (m * 0.3) + (f * 0.4)).toFixed(2);
            }
            return null;
        },

        get status() {
            if (this.computedGrade !== null) {
                return parseFloat(this.computedGrade) >= 75.0 ? 'Passed' : 'Failed';
            }
            return 'Pending';
        },

        openEncode(grade = null) {
            if (grade) {
                this.modalTitle = 'Edit Grade for ' + (grade.student.user.name || 'Student');
                this.selectedStudent = grade.student_id;
                this.selectedSubject = grade.subject_id;
                this.prelim = grade.prelim ?? '';
                this.midterm = grade.midterm ?? '';
                this.finals = grade.finals ?? '';
                this.notes = grade.notes ?? '';
            } else {
                this.modalTitle = 'Encode Student Grade';
                this.selectedStudent = '{{ $selectedStudentId ?? '' }}';
                this.selectedSubject = '{{ $selectedSubjectId ?? '' }}';
                this.prelim = '';
                this.midterm = '';
                this.finals = '';
                this.notes = '';
            }
            this.encodeModalOpen = true;
        }
    }">

        <!-- Filter & Header Box -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs mb-6 no-print">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 tracking-tight">Master Grade Matrix</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Formula: (Prelim × 30%) + (Midterm × 30%) + (Finals × 40%) = Final Grade</p>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="openEncode()" 
                            class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Encode Grade
                    </button>
                    <button onclick="window.print()" 
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Sheet
                    </button>
                </div>
            </div>

            <!-- Filter Controls -->
            <form method="GET" action="{{ route('teacher.grades.index') }}" class="mt-6 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <select name="subject_id" onchange="this.form.submit()" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none bg-slate-50">
                        <option value="">-- All Subjects --</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}" {{ $selectedSubjectId == $sub->id ? 'selected' : '' }}>
                                {{ $sub->subject_code }} - {{ $sub->subject_title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="student_id" onchange="this.form.submit()" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none bg-slate-50">
                        <option value="">-- All Students --</option>
                        @foreach($students as $stu)
                            <option value="{{ $stu->id }}" {{ $selectedStudentId == $stu->id ? 'selected' : '' }}>
                                {{ $stu->user->name }} ({{ $stu->student_id_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    @if($selectedSubjectId || $selectedStudentId)
                        <a href="{{ route('teacher.grades.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                            Reset Filters
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Mini Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6 no-print">
            <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                <div class="text-[11px] font-bold uppercase text-slate-400">Total Entries</div>
                <div class="text-xl font-bold text-slate-800 mt-0.5">{{ $stats['count'] }}</div>
            </div>
            <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                <div class="text-[11px] font-bold uppercase text-slate-400">Average Grade</div>
                <div class="text-xl font-bold text-indigo-600 mt-0.5">{{ $stats['avg'] }}%</div>
            </div>
            <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                <div class="text-[11px] font-bold uppercase text-slate-400">Highest Score</div>
                <div class="text-xl font-bold text-emerald-600 mt-0.5">{{ $stats['max'] }}%</div>
            </div>
            <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                <div class="text-[11px] font-bold uppercase text-slate-400">Lowest Score</div>
                <div class="text-xl font-bold text-rose-600 mt-0.5">{{ $stats['min'] }}%</div>
            </div>
        </div>

        <!-- Matrix Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80 border-b border-slate-100">
                            <th class="py-3.5 px-6">Student Name & ID</th>
                            <th class="py-3.5 px-4">Subject</th>
                            <th class="py-3.5 px-3 text-center">Prelim (30%)</th>
                            <th class="py-3.5 px-3 text-center">Midterm (30%)</th>
                            <th class="py-3.5 px-3 text-center">Finals (40%)</th>
                            <th class="py-3.5 px-3 text-center">Final Grade</th>
                            <th class="py-3.5 px-4 text-center">Remarks</th>
                            <th class="py-3.5 px-4">Feedback / Notes</th>
                            <th class="py-3.5 px-6 text-right no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($grades as $g)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900">{{ $g->student->user->name ?? 'Student' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $g->student->student_id_number }} &bull; {{ $g->student->course_section }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded-md text-xs font-bold font-mono">
                                        {{ $g->subject->subject_code }}
                                    </span>
                                    <div class="text-[11px] text-slate-500 truncate max-w-[140px]">{{ $g->subject->subject_title }}</div>
                                </td>
                                <td class="py-4 px-3 text-center text-slate-600 font-medium">{{ $g->prelim !== null ? number_format($g->prelim, 2) : '-' }}</td>
                                <td class="py-4 px-3 text-center text-slate-600 font-medium">{{ $g->midterm !== null ? number_format($g->midterm, 2) : '-' }}</td>
                                <td class="py-4 px-3 text-center text-slate-600 font-medium">{{ $g->finals !== null ? number_format($g->finals, 2) : '-' }}</td>
                                <td class="py-4 px-3 text-center font-extrabold text-base {{ $g->final_grade >= 75.0 ? 'text-slate-900' : 'text-rose-600' }}">
                                    {{ $g->final_grade !== null ? number_format($g->final_grade, 2) : '-' }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $g->remarks === 'Passed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($g->remarks === 'Failed' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600') }}">
                                        {{ $g->remarks }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-xs text-slate-500 max-w-[150px] truncate">
                                    {{ $g->notes ?: '—' }}
                                </td>
                                <td class="py-4 px-6 text-right no-print">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEncode({{ $g->toJson() }})"
                                                class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition" title="Edit Grade">
                                            Edit
                                        </button>

                                        <form method="POST" action="{{ route('teacher.grades.destroy', $g) }}" 
                                              onsubmit="return confirm('Remove grade entry?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Delete Entry">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">No grade records match current filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Encode Grade Modal with Live Alpine.js Calculator -->
        <div x-show="encodeModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95" style="display: none;">
            <div @click.away="encodeModalOpen = false" class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <h4 class="text-lg font-bold text-slate-900" x-text="modalTitle"></h4>
                    <button @click="encodeModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <form method="POST" action="{{ route('teacher.grades.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Select Student *</label>
                        <select name="student_id" x-model="selectedStudent" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                            <option value="">-- Choose Student --</option>
                            @foreach($students as $stu)
                                <option value="{{ $stu->id }}">{{ $stu->user->name }} ({{ $stu->student_id_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Select Subject *</label>
                        <select name="subject_id" x-model="selectedSubject" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                            <option value="">-- Choose Subject --</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->subject_code }} - {{ $sub->subject_title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Prelim (30%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="prelim" x-model="prelim" placeholder="0.00" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none font-semibold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Midterm (30%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="midterm" x-model="midterm" placeholder="0.00" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none font-semibold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Finals (40%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="finals" x-model="finals" placeholder="0.00" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none font-semibold">
                        </div>
                    </div>

                    <!-- Real-time Live Calculation Widget -->
                    <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-700 block">Computed Final Grade</span>
                            <span class="text-xl font-black text-indigo-900" x-text="computedGrade ? computedGrade + '%' : '—'"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-700 block">Status</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold inline-block"
                                  :class="status === 'Passed' ? 'bg-emerald-100 text-emerald-800' : (status === 'Failed' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-600')"
                                  x-text="status"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Instructor Feedback / Notes</label>
                        <input type="text" name="notes" x-model="notes" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none" placeholder="e.g. Excellent active participation in lab sessions">
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="encodeModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/30 transition">Save & Calculate</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
