<x-layouts.app title="Student Profile">
    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Student Information Card -->
        <div class="md:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Personal Information</h3>
                    <p class="text-xs text-slate-500">Official academic enrollment details</p>
                </div>
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-bold">
                    Active Student
                </span>
            </div>

            <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 mb-6">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-pink-500 to-violet-600 flex items-center justify-center font-black text-white text-xl shrink-0 shadow-md">
                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="text-base font-bold text-slate-900">{{ $student->user->name }}</h4>
                    <span class="text-xs font-mono font-bold text-indigo-600">{{ $student->student_id_number }}</span>
                </div>
            </div>

            <dl class="divide-y divide-slate-100 text-xs">
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-slate-500">Username:</dt>
                    <dd class="font-bold text-slate-800 font-mono">{{ $student->user->username }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-slate-500">Email Address:</dt>
                    <dd class="font-bold text-slate-800">{{ $student->user->email ?? 'Not specified' }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-slate-500">Program & Section:</dt>
                    <dd class="font-bold text-slate-800">{{ $student->course_section }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-slate-500">Year Level:</dt>
                    <dd class="font-bold text-slate-800">{{ $student->year_level }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-slate-500">Gender:</dt>
                    <dd class="font-bold text-slate-800">{{ $student->gender }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-slate-500">Contact Number:</dt>
                    <dd class="font-bold text-slate-800">{{ $student->contact_number ?? '—' }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-slate-500">Home Address:</dt>
                    <dd class="font-bold text-slate-800">{{ $student->address ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Security / Password Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs h-fit">
            <div class="pb-4 mb-6 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Security Settings</h3>
                <p class="text-xs text-slate-500">Update your portal password</p>
            </div>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('student.profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Current Password *</label>
                    <input type="password" name="current_password" required 
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">New Password *</label>
                    <input type="password" name="password" required minlength="6" 
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none" placeholder="Min. 6 characters">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Confirm New Password *</label>
                    <input type="password" name="password_confirmation" required minlength="6" 
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-indigo-600 outline-none">
                </div>

                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/30 transition">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
