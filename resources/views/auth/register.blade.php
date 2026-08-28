<x-layouts.guest title="Register">
    <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-8 sm:p-10 border border-white/20"
         x-data="{ role: '{{ old('role', 'student') }}' }">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center font-black text-white text-2xl shadow-xl shadow-indigo-500/30">
                GS
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Create an Account</h2>
            <p class="text-xs text-slate-500 mt-1 font-medium">Join the Faculty & Student Grading Portal</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Please correct the following errors:</span>
                </div>
                <ul class="list-disc pl-5 space-y-1 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Role Selection -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">I am a</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-center gap-2 p-3 rounded-xl border cursor-pointer transition-all duration-200"
                           :class="role === 'student' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-bold' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'">
                        <input type="radio" name="role" value="student" x-model="role" class="sr-only">
                        <span>Student</span>
                    </label>
                    <label class="flex items-center justify-center gap-2 p-3 rounded-xl border cursor-pointer transition-all duration-200"
                           :class="role === 'teacher' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-bold' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'">
                        <input type="radio" name="role" value="teacher" x-model="role" class="sr-only">
                        <span>Teacher</span>
                    </label>
                </div>
            </div>

            <!-- Full Name -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 text-slate-800 text-sm transition outline-none"
                       placeholder="Enter your full name">
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 text-slate-800 text-sm transition outline-none"
                       placeholder="Enter your email">
            </div>

            <!-- Student ID Field (Alpine toggle) -->
            <div x-show="role === 'student'" x-collapse>
                <label for="student_id_number" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Student ID Number</label>
                <input type="text" id="student_id_number" name="student_id_number" value="{{ old('student_id_number') }}"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 text-slate-800 text-sm transition outline-none"
                       placeholder="e.g. 2026-1001">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 text-slate-800 text-sm transition outline-none"
                           placeholder="Create password">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 text-slate-800 text-sm transition outline-none"
                           placeholder="Confirm password">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-600/30 transition duration-150 transform active:scale-[0.99] mt-4">
                Create Account
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            <p class="text-slate-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-500 transition">Sign in instead</a>
            </p>
        </div>
    </div>
</x-layouts.guest>
