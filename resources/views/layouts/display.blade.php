<!DOCTYPE html>
<html lang="id"> {{-- Hapus class="dark" agar kembali ke mode cerah --}}
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitoring Sarpras - SMKN 7 Baleendah</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>

    @livewireStyles
    @fluxStyles
</head>
<body class="bg-white text-zinc-900 antialiased"> {{-- Putih bersih seperti dashboard login --}}
    <div class="min-h-screen p-8">
        {{ $slot }}
    </div>

    @livewireScripts
    @fluxScripts
</body>
</html>