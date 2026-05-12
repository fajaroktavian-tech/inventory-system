<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Monitor Sarpras') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxStyles

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #09090b; /* Warna Zinc 950 */
        }
        /* Sembunyikan scrollbar untuk tampilan monitor TV jika diperlukan */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="antialiased no-scrollbar">
    <div class="min-h-screen">
        {{ $slot }}
    </div>

    @persist('toast')
        <flux:toast.group />
    @endpersist

    @fluxScripts
</body>
</html>