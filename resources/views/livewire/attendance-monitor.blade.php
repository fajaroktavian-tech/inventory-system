<div class="min-h-screen bg-zinc-950 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Notifikasi Hari Libur -->
        @if(\App\Models\SchoolCalendar::where('date', now()->format('Y-m-d'))->where('is_holiday', true)->exists())
            <div class="flex items-center gap-3 p-4 bg-amber-950/40 border border-amber-900/60 rounded-xl text-amber-200 shadow-sm">
                <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span class="text-sm font-medium">
                    Hari ini sekolah libur ({{ \App\Models\SchoolCalendar::where('date', now()->format('Y-m-d'))->first()->description }})
                </span>
            </div>
        @endif

        <!-- Header Halaman -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-800 pb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-100">Monitoring Kehadiran Real-time</h1>
                <p class="text-sm text-zinc-400 mt-1">Pantauan aktivitas absensi siswa secara langsung hari ini, {{ now()->translatedFormat('d F Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-900 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Monitoring
                </span>
                <a href="{{ route('home') }}" class="px-4 py-2 text-sm font-medium text-zinc-300 bg-zinc-900 border border-zinc-800 rounded-lg hover:bg-zinc-800 transition shadow-sm">
                    Kembali ke Beranda
                </a>
            </div>
        </div>

        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Hadir -->
            <div class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Total Hadir</span>
                <div class="flex items-baseline gap-2 mt-4">
                    <span class="text-4xl font-extrabold text-zinc-100">{{ $stats['hadir'] }}</span>
                    <span class="text-sm font-medium text-emerald-400">Siswa</span>
                </div>
            </div>

            <!-- Terlambat -->
            <div class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800 border-l-4 border-l-rose-500 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Terlambat</span>
                <div class="flex items-baseline gap-2 mt-4">
                    <span class="text-4xl font-extrabold text-rose-400">{{ $stats['terlambat'] }}</span>
                    <span class="text-sm font-medium text-zinc-400">Hari ini</span>
                </div>
            </div>

            <!-- Izin / Sakit -->
            <div class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Izin / Sakit / Dispen</span>
                <div class="flex items-baseline gap-2 mt-4">
                    <span class="text-4xl font-extrabold text-sky-400">{{ $stats['izin_sakit'] }}</span>
                    <span class="text-sm font-medium text-zinc-400">Siswa</span>
                </div>
            </div>

            <!-- Belum Absen / Alpa -->
            <div class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800 border-l-4 border-l-zinc-600 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Belum Absen (Alpa)</span>
                <div class="flex items-baseline gap-2 mt-4">
                    <span class="text-4xl font-extrabold text-zinc-300">{{ $stats['tidak_hadir'] }}</span>
                    <span class="text-sm font-medium text-zinc-400">Siswa</span>
                </div>
            </div>
        </div>

        <!-- Filter & Tabel Aktivitas -->
        <div class="bg-zinc-900 rounded-2xl border border-zinc-800 shadow-sm overflow-hidden" wire:poll.5s>
            
            <!-- Filter Bar -->
            <div class="p-5 border-b border-zinc-800 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3 flex-1">
                    <div class="w-full sm:max-w-xs">
                        <input type="text" wire:model.live="search" placeholder="Cari nama siswa..." class="w-full px-3 py-2 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <select wire:model.live="filterStatus" class="w-full sm:w-40 px-3 py-2 bg-zinc-950 border border-zinc-800 rounded-lg text-sm text-zinc-100 focus:outline-none focus:border-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="hadir">Hadir</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>
                </div>
                <div>
                    <button wire:click="export" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm rounded-lg shadow-sm transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Export Excel
                    </button>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-800 bg-zinc-800/50 text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Siswa</th>
                            <th class="py-3.5 px-6">Kelas</th>
                            <th class="py-3.5 px-6">Masuk</th>
                            <th class="py-3.5 px-6">Pulang</th>
                            <th class="py-3.5 px-6">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800 text-sm">
                        @forelse($latestLogs as $log)
                            <tr class="hover:bg-zinc-800/30 transition">
                                <!-- Kolom Siswa -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="shrink-0">
                                            @if($log->student->avatar)
                                                <img class="w-10 h-10 rounded-full object-cover border border-zinc-700" src="{{ asset('storage/' . $log->student->avatar) }}" alt="{{ $log->student->name }}">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-indigo-950 text-indigo-400 font-semibold flex items-center justify-center text-xs border border-indigo-900">
                                                    {{ $log->student->initials() }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-semibold text-zinc-100">{{ $log->student->name }}</div>
                                            <div class="text-xs text-zinc-500 font-mono">{{ $log->student->nis ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Kelas -->
                                <td class="py-4 px-6 font-medium text-zinc-300">
                                    {{ $log->student->class->name ?? '-' }}
                                </td>

                                <!-- Jam Masuk -->
                                <td class="py-4 px-6 font-mono text-emerald-400 font-semibold">
                                    {{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('H:i:s') : '--:--:--' }}
                                </td>

                                <!-- Jam Pulang -->
                                <td class="py-4 px-6 font-mono text-amber-400 font-semibold">
                                    {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('H:i:s') : '--:--:--' }}
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-6">
                                    @php
                                        $badgeColor = match ($log->status) {
                                            'hadir' => 'bg-emerald-950/50 text-emerald-400 border-emerald-900',
                                            'terlambat' => 'bg-rose-950/50 text-rose-400 border-rose-900',
                                            'izin', 'sakit', 'dispen' => 'bg-sky-950/50 text-sky-400 border-sky-900',
                                            default => 'bg-zinc-800 text-zinc-400 border-zinc-700'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wide border {{ $badgeColor }}">
                                        {{ $log->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-zinc-500 italic">
                                    Belum ada aktivitas absensi yang tercatat hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-zinc-800">
                {{ $latestLogs->links() }}
            </div>

        </div>
    </div>
</div>