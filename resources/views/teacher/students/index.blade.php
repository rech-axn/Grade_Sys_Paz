<x-layouts.app title="Students Directory">
    <div x-data="{ 
        search: '', 
        addModalOpen: false, 
        editModalOpen: false,
        editData: { id: '', name: '', email: '', student_id_number: '', course_section: '', year_level: '', gender: 'Male', contact_number: '', address: '' },
        openEdit(student) {
            this.editData = {
                id: student.id,
                name: student.user.name,
                email: student.user.email || '',
                student_id_number: student.student_id_number,
                course_section: student.course_section,
                year_level: student.year_level,
                gender: student.gender,
                contact_number: student.contact_number || '',
                address: student.address || ''
            };
            this.editModalOpen = true;
        }
    }">

        <!-- Action Header -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Enrolled Students</h3>
                <p class="text-xs text-slate-500 mt-0.5">Manage student academic records and portal credentials</p>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <input type="text" x-model="search" placeholder="Search students..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 rounded-xl border border-slate-200 text-xs focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <button @click="addModalOpen = true" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Enroll Student
                </button>
            </div>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80 border-b border-slate-100">
                            <th class="py-3.5 px-6">Student ID</th>
                            <th class="py-3.5 px-4">Full Name</th>
                            <th class="py-3.5 px-4">Username</th>
                            <th class="py-3.5 px-4">Course & Section</th>
                            <th class="py-3.5 px-4">Year Level</th>
                            <th class="py-3.5 px-4 text-center">GWA</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $stu)
                            <tr class="hover:bg-slate-50/60 transition"
                                x-show="!search || '{{ strtolower($stu->user->name . ' ' . $stu->student_id_number . ' ' . $stu->course_section . ' ' . $stu->user->username) }}'.includes(search.toLowerCase())">
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-mono font-bold">
                                        {{ $stu->student_id_number }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900">{{ $stu->user->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $stu->user->email ?? 'No email' }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-xs font-mono font-semibold">
                                        {{ $stu->user->username }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-slate-600 font-medium">{{ $stu->course_section }}</td>
                                <td class="py-4 px-4 text-slate-600">{{ $stu->year_level }}</td>
                                <td class="py-4 px-4 text-center">
                                    @if($stu->gwa > 0)
                                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg text-xs font-bold">
                                            {{ $stu->gwa }}%
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">No grades</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('teacher.students.report', $stu) }}" 
                                           class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold transition" title="View Grade Report">
                                            Report
                                        </a>

                                        <button @click="openEdit({{ $stu->toJson() }})"
                                                class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                                            Edit
                                        </button>

                                        <form method="POST" action="{{ route('teacher.students.destroy', $stu) }}" 
                                              onsubmit="return confirm('Delete student record for {{ addslashes($stu->user->name) }}? All grades will be removed.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Delete Student">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">No students enrolled yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Student Modal -->
        <div x-show="addModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95" style="display: none;">
            <div @click.away="addModalOpen = false" class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <h4 class="text-lg font-bold text-slate-900">Enroll New Student</h4>
                    <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <form method="POST" action="{{ route('teacher.students.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Full Name *</label>
                            <input type="text" name="name" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none" placeholder="e.g. Maria Clara">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Student ID *</label>
                            <input type="text" name="student_id_number" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none" placeholder="e.g. 2026-00105">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Login Username *</label>
                            <input type="text" name="username" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none" placeholder="e.g. student5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Login Password *</label>
                            <input type="password" name="password" required value="password123" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Course & Section</label>
                            <input type="text" name="course_section" value="BSIT 1-A" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Year Level</label>
                            <select name="year_level" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Gender</label>
                            <select name="gender" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Email</label>
                            <input type="email" name="email" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none" placeholder="student@gradesys.edu">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Contact Number</label>
                        <input type="text" name="contact_number" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none" placeholder="+63 912 345 6789">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Address</label>
                        <input type="text" name="address" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none" placeholder="City, Country">
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="addModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/30 transition">Save Student</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Student Modal -->
        <div x-show="editModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95" style="display: none;">
            <div @click.away="editModalOpen = false" class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <h4 class="text-lg font-bold text-slate-900">Edit Student Record</h4>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <form method="POST" :action="'{{ url('teacher/students') }}/' + editData.id" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Full Name *</label>
                            <input type="text" name="name" x-model="editData.name" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Student ID *</label>
                            <input type="text" name="student_id_number" x-model="editData.student_id_number" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Course & Section</label>
                            <input type="text" name="course_section" x-model="editData.course_section" required class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Year Level</label>
                            <select name="year_level" x-model="editData.year_level" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Gender</label>
                            <select name="gender" x-model="editData.gender" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Email</label>
                            <input type="email" name="email" x-model="editData.email" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Contact Number</label>
                        <input type="text" name="contact_number" x-model="editData.contact_number" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Address</label>
                        <input type="text" name="address" x-model="editData.address" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Change Password (Leave blank to keep current)</label>
                        <input type="password" name="password" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none" placeholder="New Password">
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/30 transition">Update Record</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
