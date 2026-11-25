<div class="py-6 max-w-7xl mx-auto px-4 lg:px-8 space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Rekapitulasi Kehadiran Per Kelas</flux:heading>
            <flux:subheading>Monitor kehadiran siswa (Hadir, Terlambat, Sakit, Izin, Alpa) secara real-time.
            </flux:subheading>
        </div>

        <!-- Filter Tanggal & Pencarian -->
        <div class="flex flex-wrap items-center gap-3">
            <div>
                <input type="date" wire:model.live="selectedDate"
                    class="px-3 py-2 text-sm bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-zinc-800 dark:text-zinc-200">
            </div>
            <div class="w-64">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                    placeholder="Cari nama kelas..." />
            </div>
            <div>
                <flux:button wire:click="export" variant="primary" icon="arrow-down-tray"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white">
                    Export Excel
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Tabel Rekapitulasi -->
    <flux:card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Wali Kelas</th>
                        <th class="py-3 px-4 text-center">Total Siswa</th>
                        <th class="py-3 px-4 text-center text-emerald-600 dark:text-emerald-400">Hadir</th>
                        <th class="py-3 px-4 text-center text-amber-600 dark:text-amber-400">Terlambat</th>
                        <th class="py-3 px-4 text-center text-blue-600 dark:text-blue-400">Sakit</th>
                        <th class="py-3 px-4 text-center text-indigo-600 dark:text-indigo-400">Izin</th>
                        <th class="py-3 px-4 text-center text-purple-600 dark:text-purple-400">Dispen</th>
                        <th class="py-3 px-4 text-center text-rose-600 dark:text-rose-400">Alpa</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 text-sm text-zinc-800 dark:text-zinc-200">
                    @forelse($classes as $class)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                            <td class="py-3.5 px-4 font-semibold">
                                {{ $class->name }}
                                <span class="block text-xs font-normal text-zinc-500">{{ $class->prodi->name ?? '' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-zinc-600 dark:text-zinc-400">
                                {{ $class->waliKelas->name ?? 'Belum ditentukan' }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-medium">
                                <flux:badge size="sm" color="zinc">{{ $class->students_count }}</flux:badge>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $class->rekap['hadir'] }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-semibold text-amber-600 dark:text-amber-400">
                                {{ $class->rekap['terlambat'] }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-semibold text-blue-600 dark:text-blue-400">
                                {{ $class->rekap['sakit'] }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-semibold text-indigo-600 dark:text-indigo-400">
                                {{ $class->rekap['izin'] }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-semibold text-purple-600 dark:text-purple-400">
                                {{ $class->rekap['dispen'] }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-semibold text-rose-600 dark:text-rose-400">
                                @if($class->rekap['alpa'] > 0)
                                    {{ $class->rekap['alpa'] }}
                                @else
                                    <span class="text-zinc-400 font-normal">0</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <flux:button
                                    :href="route('attendance.attendance.detail.class', ['classId' => $class->id, 'date' => $selectedDate])"
                                    variant="subtle" size="sm" icon="magnifying-glass" wire:navigate>
                                    Detail
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-zinc-500">
                                Tidak ada data kelas yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $classes->links() }}
        </div>
    </flux:card>
</div>