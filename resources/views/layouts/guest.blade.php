<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Monitoring Aktivitas</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
    </head>
    <body class="bg-zinc-100 font-sans antialiased">
        {{-- Container utama agar konten tidak melebar ke pinggir --}}
        <div class="min-h-screen flex items-center justify-center p-6">
            <div class="w-full max-w-lg"> {{-- Batasi lebar maksimal --}}
                {{ $slot }}
            </div>
        </div>
        @fluxScripts
    </body>
</html>