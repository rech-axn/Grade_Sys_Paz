<x-layouts.app title="Course Subjects">
    <div x-data="{ 
        search: '', 
        addModalOpen: false, 
        editModalOpen: false,
        editData: { id: '', subject_code: '', subject_title: '', units: 3, semester: '1st Semester', academic_year: '2025-2026' },
        openEdit(subject) {
            this.editData = {
                id: subject.id,
                subject_code: subject.subject_code,
                subject_title: subject.subject_title,
                units: subject.units,
                semester: subject.semester,
                academic_year: subject.academic_year
            };
            this.editModalOpen = true;
        }
    }">

        <!-- Action Header -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Academic Curriculum Subjects</h3>
                <p class="text-xs text-slate-500 mt-0.5">Manage course descriptions, credit units, and semester terms</p>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <input type="text" x-model="search" placeholder="Search subjects..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 rounded-xl border border-slate-200 text-xs focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <button @click="addModalOpen = true" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Add Subject
                </button>
            </div>
        </div>

        <!-- Subjects Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80 border-b border-slate-100">
                            <th class="py-3.5 px-6">Subject Code</th>
                            <th class="py-3.5 px-4">Descriptive Title</th>
                            <th class="py-3.5 px-4 text-center">Units</th>
                            <th class="py-3.5 px-4">Semester</th>
                            <th class="py-3.5 px-4">Academic Year</th>
                            <th class="py-3.5 px-4 text-center">Average Grade</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($subjects as $sub)
                            <tr class="hover:bg-slate-50/60 transition"
                                x-show="!search || '{{ strtolower($sub->subject_code . ' ' . $sub->subject_title . ' ' . $sub->semester) }}'.includes(search.toLowerCase())">
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-mono font-bold">
                                        {{ $sub->subject_code }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $sub->subject_title }}</td>
                                <td class="py-4 px-4 text-center font-semibold text-slate-700">{{ $sub->units }} Credits</td>
                                <td class="py-4 px-4 text-slate-600">{{ $sub->semester }}</td>
                                <td class="py-4 px-4 text-slate-600">{{ $sub->academic_year }}</td>
                                <td class="py-4 px-4 text-center">
                                    @if($sub->average_grade)
                                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-xs font-bold">
                                            {{ $sub->average_grade }}%
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">No grades</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('teacher.grades.index', ['subject_id' => $sub->id]) }}" 
                                           class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold transition">
                                            Grades
                                        </a>

                                        <button @click="openEdit({{ $sub->toJson() }})"
                                                class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                                            Edit
                                        </button>

                                        <form method="POST" action="{{ route('teacher.subjects.destroy', $sub) }}" 
                                              onsubmit="return confirm('Delete subject {{ addslashes($sub->subject_code) }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Delete Subject">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">No subjects registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Subject Modal -->
        <div x-show="addModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95" style="display: none;">
            <div @click.away="addModalOpen = false" class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <h4 class="text-lg font-bold text-slate-900">Add New Subject</h4>
                    <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <form method="POST" action="{{ route('teacher.subjects.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Subject Code *</label>
                            <input type="text" name="subject_code" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none uppercase" placeholder="e.g. CS201">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Credit Units *</label>
                            <input type="number" name="units" min="1" max="12" value="3" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Descriptive Title *</label>
                        <input type="text" name="subject_title" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none" placeholder="e.g. Data Structures and Algorithms">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Semester</label>
                            <select name="semester" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Academic Year</label>
                            <input type="text" name="academic_year" value="2025-2026" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="addModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/30 transition">Save Subject</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Subject Modal -->
        <div x-show="editModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95" style="display: none;">
            <div @click.away="editModalOpen = false" class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <h4 class="text-lg font-bold text-slate-900">Edit Subject</h4>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <form method="POST" :action="'{{ url('teacher/subjects') }}/' + editData.id" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Subject Code *</label>
                            <input type="text" name="subject_code" x-model="editData.subject_code" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none uppercase">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Credit Units *</label>
                            <input type="number" name="units" x-model="editData.units" min="1" max="12" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Descriptive Title *</label>
                        <input type="text" name="subject_title" x-model="editData.subject_title" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Semester</label>
                            <select name="semester" x-model="editData.semester" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Academic Year</label>
                            <input type="text" name="academic_year" x-model="editData.academic_year" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/30 transition">Update Subject</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
