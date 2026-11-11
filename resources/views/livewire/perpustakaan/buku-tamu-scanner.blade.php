<div class="space-y-6">
    <input wire:model.live="searchRfid" type="text" autofocus onblur="this.focus()"
        class="absolute opacity-0 pointer-events-none">

    {{-- HEADER MODUL --}}
    <header class="bg-white border border-zinc-200 rounded-3xl p-6 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 bg-red-50 rounded-2xl border border-red-100 flex items-center justify-center p-3">
                {{-- Gunakan ikon perpustakaan --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-9 text-red-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-950 uppercase tracking-tighter leading-none">Buku Tamu Digital
                </h1>
                <p class="text-zinc-500 font-bold text-xs mt-1 uppercase tracking-widest">Ruang Perpustakaan S7B</p>
            </div>
        </div>

        {{-- Jam & Tanggal Kios Digital Sederhana --}}
        <div class="bg-zinc-50 border border-zinc-200 rounded-2xl px-6 py-3 text-right">
            <p id="kios-clock" class="text-gray-900 font-black text-lg font-mono tracking-tighter">Memuat Waktu...</p>
        </div>
    </header>

    {{-- KONTEN UTAMA: 2 KOLOM --}}
    <main class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: SCANNER AREA (STATUS & PREVIEW) --}}
        <section class="md:col-span-1 flex flex-col gap-6">
            {{-- STATUS CARD --}}
            <div class="flex-1 bg-white border border-zinc-200 rounded-[2rem] p-8 flex flex-col items-center justify-center relative overflow-hidden shadow-sm">
                
                @if($lastTap)
                    {{-- FOTO & INFO SISWA (PREVIEW TERBARU) --}}
                    
                    {{-- Foto Siswa (Tampilan Terang) --}}
                    {{-- PERUBAHAN DISINI: Logika bingkai foto diubah untuk menangkap status 'info' (kuning/amber) dan 'error' (merah) --}}
                    <div class="w-56 h-72 rounded-[2rem] border-4 {{ $status == 'error' ? 'border-red-500' : ($status == 'info' ? 'border-amber-500' : 'border-emerald-500') }} overflow-hidden shadow-xl mb-6 bg-zinc-50 transition-all duration-500 relative">
                        @if($lastTap->user->avatar)
                            <img src="{{ asset('storage/' . $lastTap->user->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-6xl font-black text-zinc-300 bg-zinc-50">
                                {{ $lastTap->user->initials() }}
                            </div>
                        @endif
                        
                        {{-- Badge Tujuan Kunjungan --}}
                        <div class="absolute bottom-3 left-3 right-3 bg-white/80 backdrop-blur-sm border border-zinc-200 rounded-lg py-2 text-center text-[10px] font-black uppercase tracking-widest text-zinc-700">
                            Tujuan: {{ ucfirst($lastTap->purpose) }}
                        </div>
                    </div>

                    {{-- Info Siswa --}}
                    <div class="text-center">
                        <h2 class="text-2xl font-black text-gray-950 uppercase leading-tight px-4">
                            {{ $lastTap->user->name }}
                        </h2>
                        <p class="text-zinc-500 font-bold mt-1 uppercase tracking-widest text-xs">
                            {{ $lastTap->user->class->name ?? 'STAFF/GURU' }}
                        </p>
                    </div>

                    {{-- Pesan Status Tap --}}
                    <div class="mt-6 w-full">
                        {{-- PERUBAHAN DISINI: Warna peringatan (status 'info') diubah menjadi kuning (amber) --}}
                        <div
                            class="py-3 px-4 rounded-xl text-center font-black uppercase tracking-widest text-xs border-2 transition-all {{ $status == 'error' ? 'bg-red-50 border-red-500 text-red-600' : ($status == 'info' ? 'bg-amber-50 border-amber-500 text-amber-700' : 'bg-emerald-50 border-emerald-500 text-emerald-600') }}">
                            {{ $message }}
                        </div>
                    </div>
                @else
                    {{-- STANDBY MODE (BELUM ADA TAP) --}}
                    <div class="flex flex-col items-center opacity-40 text-zinc-350">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-28 mb-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a.75.75 0 0 1 .694.721v6.726a1.5 1.5 0 0 0 .332.946l4.285 5.51a.75.75 0 1 1-1.18.924l-4.285-5.51a2.999 2.999 0 0 1-.664-1.892V5.296a.75.75 0 0 1 .818-.721ZM3 6.75A.75.75 0 0 1 3.75 6h.75a.75.75 0 0 1 .75.75v.75a.75.75 0 0 1-.75.75h-.75A.75.75 0 0 1 3 6.75Zm0 4.5A.75.75 0 0 1 3.75 10h.75a.75.75 0 0 1 .75.75v.75a.75.75 0 0 1-.75.75h-.75A.75.75 0 0 1 3 11.25ZM3 15.75a.75.75 0 0 1 .75-.75h.75a.75.75 0 0 1 .75.75v.75a.75.75 0 0 1-.75.75h-.75A.75.75 0 0 1 3 15.75Z" />
                        </svg>
                        <p class="text-center font-black uppercase tracking-[0.4em] text-sm leading-relaxed text-zinc-600">SILAKAN<br>TEMPELKAN KARTU RFID</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- KOLOM KANAN: TABEL RIWAYAT KUNJUNGAN HARI INI --}}
        <section class="md:col-span-2 bg-white border border-zinc-200 rounded-[2rem] flex flex-col overflow-hidden shadow-sm">
            <div class="p-7 pb-5 flex items-center justify-between border-b border-zinc-200">
                <h3 class="text-sm font-black text-gray-950 uppercase tracking-[0.3em] flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-red-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    Kunjungan Hari Ini
                </h3>
                <div class="flex gap-2">
                    {{-- Badge Total Pengunjung --}}
                    <div class="px-4 py-1.5 bg-red-50 border border-red-100 rounded-lg text-xs font-black text-red-600 uppercase tracking-widest">
                        Total Pengunjung: {{ $totalVisitors }} Siswa
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-x-auto overflow-y-auto p-7 pt-4 h-[450px]">
                <table class="w-full text-sm text-left border-collapse min-w-[600px]">
                    <thead class="bg-white border-b border-zinc-200">
                        <tr class="text-zinc-600 text-[11px] font-black uppercase tracking-widest">
                            <th class="px-5 py-4 w-16 text-center">Waktu</th>
                            <th class="px-5 py-4">Informasi Pengunjung</th>
                            <th class="px-5 py-4 w-40">Tujuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentVisits as $visit)
                            <tr class="border-b border-zinc-100 last:border-0 hover:bg-zinc-50 transition">
                                {{-- Waktu (Filled Ikon Sederhana) --}}
                                <td class="px-5 py-4 text-center">
                                    <div class="bg-gray-100 border border-gray-200 rounded-lg py-2.5 text-base font-bold text-gray-950 font-mono tracking-tighter">
                                        {{ \Carbon\Carbon::parse($visit->visit_time)->format('H:i') }}
                                    </div>
                                </td>

                                {{-- Nama & Kelas --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        {{-- Avatar Kecil --}}
                                        @if($visit->user->avatar)
                                            <img src="{{ asset('storage/' . $visit->user->avatar) }}" class="w-10 h-10 rounded-lg object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-gray-100 text-gray-400 font-bold text-xs border">
                                                {{ substr($visit->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-gray-950 text-sm">
                                                {{ $visit->user->name }}
                                            </div>
                                            <div class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider">
                                                {{ $visit->user->class->name ?? 'STAFF/GURU' }} | {{ $visit->user->nis ?? ($visit->user->nip ?? 'N/A') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Tujuan Badge (Filled Ikon Sesuai Request) --}}
                                <td class="px-5 py-4">
                                    @if($visit->purpose == 'belajar')
                                        {{-- Ikon filled oranye (pensil) --}}
                                        <flux:button variant="filled" size="sm" icon="pencil-square"
                                            class="w-full bg-amber-600 text-white" disabled>
                                            <span class="text-xs uppercase font-black tracking-wider">Belajar</span>
                                        </flux:button>
                                    @else
                                        {{-- Ikon filled oranye (buku) --}}
                                        <flux:button variant="filled" size="sm" icon="book-open"
                                            class="w-full bg-orange-600 text-white" disabled>
                                            <span class="text-xs uppercase font-black tracking-wider">Membaca</span>
                                        </flux:button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-16 text-center text-zinc-300 uppercase font-black tracking-widest text-xs opacity-50">
                                    Belum ada kunjungan hari ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    {{-- Script Jam (Sederhana dari absensi) --}}
    <script>
        function updateKiosTime() {
            const now = new Date();
            const time = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
            const date = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).toUpperCase();
            document.getElementById('kios-clock').innerHTML = `${time} <span class="ml-2 text-[10px] text-zinc-500 font-bold">${date}</span>`;
        }
        setInterval(updateKiosTime, 1000);
        updateKiosTime();
    </script>
</div>