@extends('layouts.portal')

@section('content')
    <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">Layanan Terpadu SMKN 7 Baleendah</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">Selamat datang di sistem manajemen sekolah. Silakan pilih modul layanan
            yang Anda butuhkan di bawah ini.</p>
    </div>

    <div class="grid md:grid-cols-3 gap-8">

        <div class="bg-white p-8 rounded-2xl border border-red-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-red-100 text-red-600 rounded-xl flex items-center justify-center mb-6">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M20 7h-4V4c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v3H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zM10 4h4v3h-4V4zm10 16H4V9h16v11z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-2">Sarpras Digital</h3>
            <p class="text-gray-500 mb-6">Kelola inventaris, data barang, peminjaman aset, dan registrasi unit fisik
                sekolah.</p>
            <a href="{{ route('sarpras.landing') }}" class="text-red-600 font-semibold inline-flex items-center">
                Masuk Modul &rarr;
            </a>
        </div>

        <div class="bg-white p-8 rounded-2xl border border-blue-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-6">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-2">Sistem Absensi</h3>
            <p class="text-gray-500 mb-6">Monitoring kehadiran siswa, data piket, dan laporan kedisiplinan secara real-time.
            </p>
            <a href="{{ route('dashboard.absen') }}" class="text-blue-600 font-semibold inline-flex items-center">
                Masuk Modul &rarr;
            </a>
        </div>

        <div class="bg-gray-100 p-8 rounded-2xl border border-gray-200 opacity-70">
            <div class="w-12 h-12 bg-gray-200 text-gray-500 rounded-xl flex items-center justify-center mb-6">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-2">Perpustakaan</h3>
            <p class="text-gray-500 mb-6">Modul katalog buku dan peminjaman perpustakaan akan segera hadir.</p>
            <span class="text-gray-400 font-semibold">Segera Hadir</span>
        </div>

    </div>
@endsection