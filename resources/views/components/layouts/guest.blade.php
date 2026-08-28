<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Login' }} - {{ config('app.name', 'GradeSys') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts and Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 flex items-center justify-center p-4 relative overflow-hidden font-sans text-slate-100">
    <!-- Glow Orb -->
    <div class="absolute w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-3xl pointer-events-none -top-20 -left-20"></div>
    <div class="absolute w-[400px] h-[400px] bg-violet-600/20 rounded-full blur-3xl pointer-events-none -bottom-20 -right-20"></div>

    <div class="w-full max-w-md relative z-10">
        {{ $slot }}
    </div>
</body>
</html>
