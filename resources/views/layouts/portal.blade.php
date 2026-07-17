<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal S7B - Terpadu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900 font-sans">
    
    <header class="bg-white border-b shadow-sm">
        <nav class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/LogoSMKN7BE.png') }}" class="h-10" alt="Logo">
                <span class="font-bold text-lg uppercase tracking-wider">Portal <span class="text-red-600">S7B</span></span>
            </div>
            <div>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold">Login</a>
                @endauth
            </div>
        </nav>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-12">
    @yield('content') </main>

    @livewireScripts
</body>
</html>