<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarpras Digital - SMKN 7 Baleendah</title>
    @livewireStyles
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .logo-s7b {
            height: 45px !important;
            width: auto;
        }

        html {
            scroll-behavior: smooth;
        }
    </style>


</head>

<body class="bg-gray-50 antialiased">

    <header class="bg-white border-b-2 border-red-600 shadow-sm sticky top-0 z-50">
        <nav class="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8">

            <div class="flex lg:flex-1 items-center gap-3">
                <img src="{{ asset('images/Logo SMKN 7 BE.png') }}" alt="Logo S7B" class="logo-s7b" />
                <div class="hidden md:block">
                    <p class="font-black text-sm leading-none uppercase">Sarpras <span
                            class="text-red-600">Digital</span></p>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-1">SMKN 7 Baleendah</p>
                </div>
            </div>

            <div class="hidden lg:flex gap-x-8 items-center">
                <div class="relative group">
                    <button type="button"
                        class="flex items-center gap-x-1 text-sm font-bold text-gray-700 group-hover:text-red-600 transition-colors py-2">
                        Layanan Sarpras
                        <svg class="size-4 text-gray-400 group-hover:text-red-600 transition-transform group-hover:rotate-180"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" />
                        </svg>
                    </button>

                    <div
                        class="absolute left-0 top-full mt-0 w-56 origin-top-left rounded-xl bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none 
                invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-[60] overflow-hidden border border-gray-100">
                        <div class="py-2">
                            <a href="{{ route('user.request') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors border-b border-gray-50 group/item">
                                <div class="p-2 bg-gray-50 rounded-lg group-hover/item:bg-white transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-5 text-gray-500 group-hover/item:text-red-600">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold">Permintaan</span>
                                    <span class="text-[10px] text-gray-400 italic font-normal">Alat & Bahan</span>
                                </div>
                            </a>

                            <a href="#"
                                class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors border-b border-gray-50 group/item">
                                <div class="p-2 bg-gray-50 rounded-lg group-hover/item:bg-white transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-5 text-gray-500 group-hover/item:text-red-600">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold">Peminjaman</span>
                                    <span class="text-[10px] text-gray-400 italic font-normal">Ruang & Alat</span>
                                </div>
                            </a>

                            <a href="#"
                                class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors group/item">
                                <div class="p-2 bg-gray-50 rounded-lg group-hover/item:bg-white transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-5 text-gray-500 group-hover/item:text-red-600">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.83-5.83m0 0a2.93 2.93 0 1 1-4.144-4.144 2.93 2.93 0 0 1 4.144 4.144Zm-5.761 4.461L5.5 17.75a2.121 2.121 0 0 1-3-3l7.922-7.922m1.022 1.022L3.5 15.75a2.121 2.121 0 0 0 3 3l7.922-7.922m-1.022-1.022 3.99-3.99a2.93 2.93 0 1 1 4.144 4.144L15.4 12.194" />
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold">Pelaporan</span>
                                    <span class="text-[10px] text-gray-400 italic font-normal">Kerusakan</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('monitor.aktivitas') }}"
                    class="text-sm font-bold text-gray-700 hover:text-red-600">Monitor Aktivitas</a>
                <a href="#about" class="text-sm font-bold text-gray-700 hover:text-red-600">Tentang Kami</a>
                <a href="#" class="text-sm font-bold text-gray-700 hover:text-red-600">Tim Kami</a>
            </div>

            <div class="hidden lg:flex lg:flex-1 lg:justify-end items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-700">Log in</a>
                <a href="{{ route('kios.gateway') }}" {{-- UBAH INI --}}
                    class="bg-red-600 text-white px-5 py-2 rounded-lg font-bold text-sm hover:bg-red-700 shadow-md">
                    Kios RFID
                </a>
            </div>
        </nav>
    </header>

    <main class="max-w-7xl mx-auto px-6 lg:px-8 py-12 lg:py-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1 text-center lg:text-left">
                <!-- <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-50 border border-red-100 text-green-600 text-xs font-bold uppercase tracking-wider mb-6">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-600"></span>
                    </span>
                    Sistem Monitoring Real-time
                </div> -->

                <h1 class="text-4xl lg:text-6xl font-black text-zinc-900 leading-[1.1] tracking-tight">
                    Kelola Sarana Prasarana <br>
                    <span class="text-red-600">Lebih Terintegrasi.</span>
                </h1>

                <p class="mt-6 text-lg text-zinc-600 leading-relaxed max-w-xl">
                    Optimalkan pengelolaan aset, peminjaman ruang, hingga permintaan bahan praktik di SMKN 7 Baleendah
                    dengan sistem digital yang cepat dan akurat.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('user.request') }}"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-red-600 text-white font-bold rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 hover:-translate-y-1 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 2.25c-3.354 0-6.458 1.152-8.907 3.074a11.485 11.485 0 0 0-1.223 15.111l.131.187a1.125 1.125 0 0 0 1.583.245l3.888-2.917V17.25h1.125a1.125 1.125 0 0 1 1.125 1.125v1.5a3.375 3.375 0 0 0 5.59 2.51Z" />
                        </svg>
                        Mulai Sekarang
                    </a>

                    <flux:modal.trigger name="stock-modal">
                        <button type="button"
                            class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-zinc-900 font-bold rounded-xl border border-zinc-200 hover:bg-zinc-50 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="size-5 text-red-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            Lihat Stok Barang
                        </button>
                    </flux:modal.trigger>
                </div>

                <div class="mt-12 pt-8 border-t border-zinc-100 flex gap-8 justify-center lg:justify-start">
                    <div>
                        <span class="block text-2xl font-black text-zinc-900">100%</span>
                        <span class="text-xs text-zinc-500 uppercase font-bold tracking-wider">Digitalisasi</span>
                    </div>
                    <div class="border-l border-zinc-200"></div>
                    <div>
                        <span class="block text-2xl font-black text-zinc-900">Real-time</span>
                        <span class="text-xs text-zinc-500 uppercase font-bold tracking-wider">Data Aset</span>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2 relative">
                <div
                    class="absolute -top-4 -right-4 w-72 h-72 bg-red-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob">
                </div>
                <div
                    class="absolute -bottom-8 -left-8 w-72 h-72 bg-zinc-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000">
                </div>

                <div class="relative">
                    <img src="{{ asset('images/home.jpg') }}" alt="Sarpras SMKN 7"
                        class="rounded-3xl shadow-2xl w-full object-cover aspect-[4/3] lg:aspect-square border-8 border-white">

                    <div
                        class="absolute -bottom-6 -left-6 bg-white p-4 rounded-2xl shadow-xl border border-zinc-100 hidden sm:flex items-center gap-4 animate-bounce-slow">
                    </div>
                </div>
            </div>
        </div>
    </main>

    <section class="bg-zinc-50 py-24 border-t border-zinc-100">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-red-600 font-bold text-sm uppercase tracking-[0.2em] mb-3">Layanan Kami</h2>
                <p class="text-3xl lg:text-4xl font-black text-zinc-900 tracking-tight">
                    Segala urusan inventaris dalam satu pintu digital.
                </p>
                <p class="mt-4 text-zinc-600">
                    Pilih layanan yang Anda butuhkan untuk mendukung kegiatan belajar mengajar di SMKN 7 Baleendah.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">

                <div
                    class="group relative bg-white p-8 rounded-3xl border border-zinc-200 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="size-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-red-600 group-hover:text-white transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-zinc-900 mb-3">Permintaan Barang</h3>
                    <p class="text-zinc-600 text-sm leading-relaxed mb-6">
                        Ajukan kebutuhan ATK, bahan praktik kejujuran, atau barang habis pakai lainnya secara cepat
                        melalui sistem RFID.
                    </p>
                    <a href="{{ route('rfid.request') }}"
                        class="text-red-600 font-bold text-sm inline-flex items-center gap-2 hover:gap-3 transition-all">
                        Ajukan Sekarang
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
                <div
                    class="group relative bg-white p-8 rounded-3xl border border-zinc-200 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="size-14 bg-zinc-50 text-zinc-900 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-zinc-900 group-hover:text-white transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-zinc-900 mb-3">Peminjaman Aset</h3>
                    <p class="text-zinc-600 text-sm leading-relaxed mb-6">
                        Cek ketersediaan dan pinjam alat elektronik, peralatan bengkel, atau kunci laboratorium dengan
                        pencatatan otomatis.
                    </p>
                    <a href="#"
                        class="text-zinc-900 font-bold text-sm inline-flex items-center gap-2 hover:gap-3 transition-all">
                        Cek Jadwal
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>

                <div
                    class="group relative bg-white p-8 rounded-3xl border border-zinc-200 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="size-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.83-5.83m0 0a2.93 2.93 0 1 1-4.144-4.144 2.93 2.93 0 0 1 4.144 4.144Zm-5.761 4.461L5.5 17.75a2.121 2.121 0 0 1-3-3l7.922-7.922m1.022 1.022L3.5 15.75a2.121 2.121 0 0 0 3 3l7.922-7.922m-1.022-1.022 3.99-3.99a2.93 2.93 0 1 1 4.144 4.144L15.4 12.194" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-zinc-900 mb-3">Lapor Kerusakan</h3>
                    <p class="text-zinc-600 text-sm leading-relaxed mb-6">
                        Temukan fasilitas yang rusak? Ambil foto dan lapor di sini agar tim sarpras bisa segera
                        melakukan perbaikan.
                    </p>
                    <a href="#"
                        class="text-amber-600 font-bold text-sm inline-flex items-center gap-2 hover:gap-3 transition-all">
                        Buat Laporan
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-zinc-900 rounded-[3rem] p-8 lg:p-16 relative overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 -mt-20 -mr-20 size-80 bg-red-600/20 rounded-full blur-[100px]"></div>
                <div class="absolute bottom-0 left-0 -mb-20 -ml-20 size-80 bg-red-900/10 rounded-full blur-[100px]">
                </div>

                <div class="relative z-10 grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <h2 class="text-red-500 font-bold text-sm uppercase tracking-widest mb-4">Update Terkini</h2>
                        <p class="text-3xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                            Transparansi Data <br> Inventaris Sekolah.
                        </p>
                        <p class="mt-6 text-zinc-400 text-lg leading-relaxed">
                            Sistem kami mencatat setiap pergerakan barang secara otomatis. Pantau stok bahan praktik dan
                            ketersediaan alat secara langsung tanpa harus ke gudang.
                        </p>
                        <div class="mt-10">
                            <flux:modal.trigger name="stock-modal">
                                <button
                                    class="inline-flex items-center gap-2 text-white font-bold border-b-2 border-red-600 pb-1 hover:text-red-500 transition-colors">
                                    Buka Monitor Stok Lengkap
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="size-5">
                                        <path fill-rule="evenodd"
                                            d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </flux:modal.trigger>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-sm">
                            <span
                                class="block text-4xl lg:text-5xl font-black text-white mb-2">{{ number_format($totalItems) }}</span>
                            <span class="text-zinc-500 text-xs font-bold uppercase tracking-wider leading-tight">Total
                                Item <br> Tersedia</span>
                        </div>

                        <div class="bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-sm">
                            <span
                                class="block text-4xl lg:text-5xl font-black text-red-500 mb-2">{{ $todayRequests }}</span>
                            <span
                                class="text-zinc-500 text-xs font-bold uppercase tracking-wider leading-tight">Permintaan
                                <br> Hari Ini</span>
                        </div>

                        <div class="bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-sm">
                            <span class="block text-4xl lg:text-5xl font-black text-white mb-2">{{ $totalRooms }}</span>
                            <span class="text-zinc-500 text-xs font-bold uppercase tracking-wider leading-tight">Ruang
                                Lab <br> Terdata</span>
                        </div>

                        <div class="bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-sm">
                            <span
                                class="block text-4xl lg:text-5xl font-black text-green-500 mb-2">{{ $totalCategories }}</span>
                            <span
                                class="text-zinc-500 text-xs font-bold uppercase tracking-wider leading-tight">Kategori
                                <br> Barang</span>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-16 pt-8 border-t border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span
                            class="flex h-3 w-3 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.8)]"></span>
                        <p class="text-zinc-400 text-sm italic font-medium">Terakhir diperbarui: {{ $lastUpdate }}
                            melalui sistem</p>
                    </div>
                    <div class="text-zinc-500 text-xs font-bold tracking-widest uppercase">
                        SMKN 7 Baleendah • Sarpras Digital
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-zinc-50 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="relative bg-white rounded-[3rem] p-8 lg:p-20 shadow-sm border border-zinc-100">
                <div class="absolute top-10 left-10 text-zinc-100">
                    <svg class="size-32 fill-current" viewBox="0 0 32 32" aria-hidden="true">
                        <path
                            d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z" />
                    </svg>
                </div>

                <div class="relative grid lg:grid-cols-3 gap-12 items-center">
                    <div class="flex justify-center">
                        <div class="relative">
                            <div class="absolute -inset-4 bg-red-600/10 rounded-full blur-2xl"></div>
                            <div
                                class="relative size-64 lg:size-80 rounded-3xl overflow-hidden border-8 border-white shadow-2xl rotate-3 hover:rotate-0 transition-transform duration-500">
                                <img src="https://ui-avatars.com/api/?name=Gungun+Gunawan&background=dc2626&color=fff&size=512"
                                    alt="Pak Gungun Gunawan S.Pd" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 text-center lg:text-left">
                        <h2 class="text-red-600 font-bold text-sm uppercase tracking-widest mb-6">Sambutan Wakasek
                            Sarpras</h2>

                        <blockquote class="text-2xl lg:text-3xl font-medium text-zinc-800 leading-relaxed italic">
                            "Digitalisasi sarana dan prasarana bukan sekadar tren, melainkan kebutuhan untuk menciptakan
                            tata kelola sekolah yang transparan, akuntabel, dan efisien demi kenyamanan belajar seluruh
                            siswa."
                        </blockquote>

                        <div class="mt-10">
                            <p class="text-xl font-black text-zinc-900">Gungun Gunawan, S.Pd</p>
                            <p class="text-zinc-500 font-medium">Wakil Kepala Sekolah Bidang Sarana & Prasarana</p>
                        </div>

                        <div class="mt-8 flex flex-wrap gap-3 justify-center lg:justify-start">
                            <span
                                class="px-4 py-2 bg-zinc-100 rounded-full text-zinc-600 text-xs font-bold uppercase tracking-tighter">Pembina
                                Utama</span>
                            <span
                                class="px-4 py-2 bg-zinc-100 rounded-full text-zinc-600 text-xs font-bold uppercase tracking-tighter">Manajemen
                                Aset</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">

                <div>
                    <h2 class="text-red-600 font-bold text-sm uppercase tracking-widest mb-3">Tentang Kami</h2>
                    <h3 class="text-4xl lg:text-5xl font-black text-zinc-900 mb-6 leading-tight">
                        Transformasi Digital Tata Kelola Sarana Sekolah
                    </h3>
                    <p class="text-lg text-zinc-600 leading-relaxed mb-8">
                        Sistem Inventaris Sarpras SMKN 7 Baleendah adalah inovasi teknologi yang dirancang untuk
                        memodernisasi cara kami mengelola aset sekolah. Dengan integrasi teknologi
                        <strong>RFID</strong>, kami menghapus batasan birokrasi manual dan menggantinya dengan ekosistem
                        yang cepat, tepat, dan transparan.
                    </p>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div class="flex gap-4">
                            <div
                                class="flex-none size-10 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                                <flux:icon name="bolt" variant="mini" />
                            </div>
                            <div>
                                <h4 class="font-bold text-zinc-900">Proses Instan</h4>
                                <p class="text-sm text-zinc-500">Peminjaman dan pengembalian hanya dalam hitungan detik.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="flex-none size-10 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                                <flux:icon name="magnifying-glass" variant="mini" />
                            </div>
                            <div>
                                <h4 class="font-bold text-zinc-900">Transparansi Stok</h4>
                                <p class="text-sm text-zinc-500">Pantau ketersediaan barang secara real-time kapanpun.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="flex-none size-10 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                                <flux:icon name="shield-check" variant="mini" />
                            </div>
                            <div>
                                <h4 class="font-bold text-zinc-900">Data Akurat</h4>
                                <p class="text-sm text-zinc-500">Meminimalisir risiko kehilangan dan kesalahan input
                                    data.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="flex-none size-10 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                                <flux:icon name="chart-bar" variant="mini" />
                            </div>
                            <div>
                                <h4 class="font-bold text-zinc-900">Laporan Otomatis</h4>
                                <p class="text-sm text-zinc-500">Audit aset yang lebih mudah bagi manajemen sekolah.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -inset-4 bg-zinc-100 rounded-[3rem] -rotate-2"></div>
                    <div
                        class="relative bg-zinc-900 rounded-[2.5rem] overflow-hidden aspect-video shadow-2xl flex items-center justify-center p-12">
                        <div class="text-center">
                            <flux:icon name="identification" class="size-24 text-red-600 mb-4 mx-auto" />
                            <h4 class="text-white font-bold text-xl">RFID Integrated System</h4>
                            <p class="text-zinc-400 text-sm mt-2 font-mono italic">Powered by SMKN 7 Baleendah IT Team
                            </p>
                        </div>

                        <div
                            class="absolute bottom-6 right-6 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20">
                            <div class="flex items-center gap-3">
                                <div class="size-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-white text-xs font-bold uppercase tracking-widest">System
                                    Online</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-white border-t-2 border-red-600 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('images/Logo SMKN 7 BE.png') }}" alt="Logo S7B" class="h-12 w-auto" />
                        <div>
                            <p class="font-black text-sm leading-none uppercase text-zinc-900">Sarpras <span
                                    class="text-red-600">Digital</span></p>
                            <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mt-1">SMKN 7
                                Baleendah</p>
                        </div>
                    </div>
                    <p class="text-zinc-500 text-sm leading-relaxed mb-6">
                        Mewujudkan manajemen sarana prasarana sekolah yang modern, transparan, dan terintegrasi untuk
                        mendukung prestasi siswa.
                    </p>
                    <div class="flex gap-4">
                        <a href="#"
                            class="size-10 bg-zinc-100 rounded-full flex items-center justify-center text-zinc-600 hover:bg-red-600 hover:text-white transition-all">
                            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="size-10 bg-zinc-100 rounded-full flex items-center justify-center text-zinc-600 hover:bg-red-600 hover:text-white transition-all">
                            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.315 2c2.43 0 2.784.012 3.823.06 1.062.049 1.781.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.365.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.784-.465 2.43a4.908 4.908 0 01-1.153 1.772 4.908 4.908 0 01-1.772 1.153c-.636.247-1.363.417-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.784-.218-2.43-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.417-1.365-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.823.049-1.064.218-1.784.465-2.43a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.417 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-zinc-900 font-bold mb-6">Layanan Digital</h4>
                    <ul class="space-y-4 text-sm font-medium text-zinc-500">
                        <li><a href="{{ route('user.request') }}"
                                class="hover:text-red-600 transition-colors">Permintaan Barang</a></li>
                        <li><a href="#" class="hover:text-red-600 transition-colors">Peminjaman Ruang</a></li>
                        <li><a href="#" class="hover:text-red-600 transition-colors">Pelaporan Kerusakan</a></li>
                        <li><a href="#" class="hover:text-red-600 transition-colors">Status Inventaris</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-zinc-900 font-bold mb-6">Hubungi Kami</h4>
                    <ul class="space-y-4 text-sm text-zinc-500">
                        <li class="flex items-start gap-3">
                            <svg class="size-5 text-red-600 flex-none" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Jl. Siliwangi No.Km.15, Manggahang, Kec. Baleendah, Kabupaten Bandung, Jawa Barat 40375
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="size-5 text-red-600 flex-none" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            info@smkn7baleendah.sch.id
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-zinc-900 font-bold mb-6">Lokasi</h4>
                    <div
                        class="rounded-2xl overflow-hidden grayscale hover:grayscale-0 transition-all duration-500 shadow-md h-40">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.22603816782!2d107.63229677448888!3d-6.982635993018281!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e9064c9247d5%3A0xc39115c542c38!2sSMK%20Negeri%207%20Baleendah!5e0!3m2!1sid!2sid!4v1700000000000"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>

            <div
                class="pt-8 border-t border-zinc-100 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-bold text-zinc-400 uppercase tracking-widest">
                <p>&copy; 2026 Sarpras Digital SMKN 7 Baleendah. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-red-600 transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-red-600 transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    @livewire('stock-viewer')
    @livewireScripts
    @fluxScripts
</body>

</html>