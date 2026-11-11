<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Buku Tamu Digital - Perpustakaan S7B</title>

    {{-- Fonts (Gunakan font Inter yang sama) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    {{-- Scripts (Tailwind & Vite) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* Slate 50 (Tema Terang) */
            color: #0f172a; /* Slate 900 (Tema Terang) */
        }

        /* Utilitas No-Scrollbar dari layout kios lama */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="antialiased no-scrollbar overflow-hidden">

<main class="relative z-10 max-w-7xl mx-auto px-6 py-3 md:px-10 md:py-4">
        {{ $slot }}
    </main>

    @fluxScripts
</body>

</html>