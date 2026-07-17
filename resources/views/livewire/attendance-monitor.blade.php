<div class="p-6 lg:p-8" wire:poll.60s> {{-- Refresh otomatis setiap 5 detik --}}
<div class="mb-4">
    @if(\App\Models\SchoolCalendar::where('date', now()->format('Y-m-d'))->where('is_holiday', true)->exists())
        <flux:badge color="amber" icon="sun">Hari ini sekolah libur ({{ \App\Models\SchoolCalendar::where('date', now()->format('Y-m-d'))->first()->description }})</flux:badge>
    @endif
</div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <flux:heading size="xl" level="1">Monitoring Kehadiran Real-time</flux:heading>
            <flux:subheading>Pantauan aktivitas absensi hari ini, {{ now()->translatedFormat('d F Y') }}
            </flux:subheading>
        </div>
        <flux:badge color="green" icon="rss" class="animate-pulse">Live Monitoring</flux:badge>
    </div>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <flux:card class="flex flex-col justify-center py-6">
            <flux:text class="text-zinc-500 uppercase text-xs font-bold tracking-wider">Total Hadir</flux:text>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold">{{ $stats['hadir'] }}</span>
                <flux:text size="sm" class="text-green-500">Siswa</flux:text>
            </div>
        </flux:card>

        <flux:card class="flex flex-col justify-center py-6 border-l-4 border-red-500">
            <flux:text class="text-zinc-500 uppercase text-xs font-bold tracking-wider">Terlambat</flux:text>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-red-500">{{ $stats['terlambat'] }}</span>
                <flux:text size="sm">Hari ini</flux:text>
            </div>
        </flux:card>

        <flux:card class="flex flex-col justify-center py-6">
            <flux:text class="text-zinc-500 uppercase text-xs font-bold tracking-wider">Izin / Sakit</flux:text>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-blue-500">{{ $stats['izin_sakit'] }}</span>
                <flux:text size="sm">Siswa</flux:text>
            </div>
        </flux:card>

        <flux:card class="flex flex-col justify-center py-6 border-l-4 border-zinc-500">
            <flux:text class="text-zinc-500 uppercase text-xs font-bold tracking-wider">Belum Absen (Alpa)</flux:text>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-zinc-400">{{ $stats['tidak_hadir'] }}</span>
                <flux:text size="sm">Siswa</flux:text>
            </div>
        </flux:card>
    </div>

    {{-- Filter & Tabel --}}
    <flux:card>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex flex-1 gap-2">
                <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari nama siswa..."
                    class="max-w-xs" />
                <flux:select wire:model.live="filterStatus" class="max-w-[150px]">
                    <option value="">Semua Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                </flux:select>
                <flux:button wire:click="export" icon="arrow-down-tray" variant="primary">
        Export Excel
    </flux:button>
            </div>
            
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Siswa</flux:table.column>
                <flux:table.column>Kelas</flux:table.column>
                {{-- Kolom Waktu dipisah menjadi dua --}}
                <flux:table.column>Masuk</flux:table.column>
                <flux:table.column>Pulang</flux:table.column>
                <flux:table.column>Status</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($latestLogs as $log)
                    <flux:table.row :key="$log->id">
                        {{-- Nama Siswa --}}
                        <flux:table.cell>
                            <div class="flex items-center gap-4 py-1">
                                <flux:avatar initials="{{ $log->student->initials() }}" size="xl"
                                    src="{{ $log->student->avatar ? asset('storage/' . $log->student->avatar) : null }}" />
                                <div>
                                    <div class="font-medium text-zinc-800 dark:text-zinc-200">{{ $log->student->name }}
                                    </div>
                                    <div class="text-[10px] text-zinc-500 font-mono">{{ $log->student->nis }}</div>
                                </div>
                            </div>
                        </flux:table.cell>

                        {{-- Kelas --}}
                        <flux:table.cell>{{ $log->student->class->name ?? '-' }}</flux:table.cell>

                        {{-- Tap Masuk --}}
                        <flux:table.cell class="font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                            {{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('H:i:s') : '--:--:--' }}
                        </flux:table.cell>

                        {{-- Tap Pulang --}}
                        <flux:table.cell class="font-mono text-orange-600 dark:text-orange-400 font-bold">
                            {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('H:i:s') : '--:--:--' }}
                        </flux:table.cell>

                        {{-- Status --}}
                        <flux:table.cell>
                            @php
                                $color = match ($log->status) {
                                    'hadir' => 'green',
                                    'terlambat' => 'red',
                                    'izin', 'sakit', 'dispen' => 'blue',
                                    default => 'zinc'
                                };
                            @endphp
                            <flux:badge :color="$color" size="sm" inset="right">{{ strtoupper($log->status) }}</flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-10 text-zinc-500 italic">
                            Belum ada aktivitas absensi hari ini.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $latestLogs->links() }}
        </div>
    </flux:card>
</div>