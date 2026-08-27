<x-layouts.guest title="Sign In">
    <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-8 sm:p-10 border border-white/20"
         x-data="{ username: '{{ old('username', $prefillUser ?? '') }}', password: '{{ $prefillUser ? 'password123' : '' }}' }">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center font-black text-white text-2xl shadow-xl shadow-indigo-500/30">
                GS
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">GradeSys Paz</h2>
            <p class="text-xs text-slate-500 mt-1 font-medium">Faculty & Student Grading Portal (Laravel 12)</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Username</label>
                <input type="text" id="username" name="username" x-model="username" required autofocus
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 text-slate-800 text-sm transition outline-none"
                       placeholder="Enter your username">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Password</label>
                <input type="password" id="password" name="password" x-model="password" required
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 text-slate-800 text-sm transition outline-none"
                       placeholder="Enter your password">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-600/30 transition duration-150 transform active:scale-[0.99] mt-2">
                Sign In to Portal
            </button>
        </form>

        <!-- 1-Click Demo Accounts -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center mb-3">1-Click Demo Accounts:</span>
            
            <div class="space-y-2">
                <button type="button" @click="username = 'teacher'; password = 'password123'"
                        class="w-full flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-indigo-50/70 border border-slate-200/80 hover:border-indigo-200 text-left transition group">
                    <div class="flex items-center gap-2.5">
                        <span class="text-base">🧑‍🏫</span>
                        <div>
                            <div class="text-xs font-bold text-slate-800 group-hover:text-indigo-700">Teacher: Prof. Maria Santos</div>
                            <div class="text-[10px] text-slate-400 font-mono">user: teacher</div>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-indigo-600 bg-white px-2 py-0.5 rounded-md border border-slate-200 shadow-2xs">Fill</span>
                </button>

                <button type="button" @click="username = 'student1'; password = 'password123'"
                        class="w-full flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-indigo-50/70 border border-slate-200/80 hover:border-indigo-200 text-left transition group">
                    <div class="flex items-center gap-2.5">
                        <span class="text-base">🎓</span>
                        <div>
                            <div class="text-xs font-bold text-slate-800 group-hover:text-indigo-700">Student: Juan Dela Cruz</div>
                            <div class="text-[10px] text-slate-400 font-mono">user: student1</div>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-indigo-600 bg-white px-2 py-0.5 rounded-md border border-slate-200 shadow-2xs">Fill</span>
                </button>

                <button type="button" @click="username = 'student2'; password = 'password123'"
                        class="w-full flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-indigo-50/70 border border-slate-200/80 hover:border-indigo-200 text-left transition group">
                    <div class="flex items-center gap-2.5">
                        <span class="text-base">🎓</span>
                        <div>
                            <div class="text-xs font-bold text-slate-800 group-hover:text-indigo-700">Student: Alyssa Reyes (Dean's List)</div>
                            <div class="text-[10px] text-slate-400 font-mono">user: student2</div>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-indigo-600 bg-white px-2 py-0.5 rounded-md border border-slate-200 shadow-2xs">Fill</span>
                </button>
            </div>
        </div>
    </div>
</x-layouts.guest>
