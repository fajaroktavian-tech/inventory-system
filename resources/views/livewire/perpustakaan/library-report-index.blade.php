<div class="p-6">
    {{-- Judul Halaman --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Laporan & Buku Tamu Perpustakaan</flux:heading>
            <flux:subheading>Rekapitulasi data pengunjung dan statistik perpustakaan.</flux:subheading>
        </div>
    </div>

    {{-- KARTU STATISTIK CEPAT --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <flux:card class="flex items-center gap-4">
            <div class="p-3 bg-red-100 dark:bg-red-900 rounded-xl">
                <flux:icon.book-open class="size-7 text-red-600 dark:text-red-300" />
            </div>
            <div>
                <flux:heading size="sm" class="text-zinc-500">Total Pengunjung Hari Ini</flux:heading>
                <flux:heading size="xl" class="font-black text-gray-950">{{ $totalToday }} Siswa</flux:heading>
            </div>
        </flux:card>

        <flux:card
            class="flex items-center gap-4 border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-950">
            <div class="p-3 bg-emerald-100 dark:bg-emerald-900 rounded-xl">
                <flux:icon.users class="size-7 text-emerald-600 dark:text-emerald-300" />
            </div>
            <div>
                <flux:heading size="sm" class="text-emerald-700 dark:text-emerald-400">Total Berdasarkan Filter
                </flux:heading>
                <flux:heading size="xl" class="font-black text-emerald-950">{{ $totalFiltered }} Siswa</flux:heading>
            </div>
        </flux:card>
    </div>

    {{-- AREA FILTER & TABEL --}}
    <flux:card class="border border-zinc-200">
    <div class="flex flex-col md:flex-row md:items-end gap-x-4 gap-y-4 mb-5 border-b border-zinc-100 pb-5">
            
            {{-- Grup Input Tanggal (Start - End) --}}
            <div class="flex flex-col gap-1 flex-grow md:flex-grow-0">
                {{-- Label Grup --}}
                <flux:label>Rentang Tanggal Kunjungan</flux:label>
                
                {{-- Container Input Tanggal dan Separator --}}
                <div class="flex items-center gap-2">
                    {{-- Tanggal Mulai (Tanpa label karena label di atas) --}}
                    <flux:input wire:model.live="startDate" type="date" class="w-full md:w-auto" />

                    {{-- Separator s/d --}}
                    <flux:subheading class="shrink-0">s/d</flux:subheading>

                    {{-- Tanggal Selesai (Tanpa label) --}}
                    <flux:input wire:model.live="endDate" type="date" class="w-full md:w-auto" />
                </div>
            </div>

            {{-- Elemen Pencarian --}}
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                placeholder="Cari Nama, NIS, atau NIP..."
                icon="magnifying-glass" 
                label="Pencarian" 
                class="flex-grow md:max-w-xs" 
            />

            {{-- Spacer untuk mendorong tombol ke kanan di layar besar --}}
            <flux:spacer class="hidden md:block" />

            {{-- Tombol Cetak --}}
            <flux:button 
                icon="printer" 
                variant="subtle" 
                class="shrink-0 w-full md:w-auto md:self-end"
            >
                Cetak Laporan
            </flux:button>
        </div>

        {{-- TABEL REKAP KUNJUNGAN --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column w-12 text-center>No</flux:table.column>
                <flux:table.column>Waktu & Tanggal</flux:table.column>
                <flux:table.column>Informasi Pengunjung</flux:table.column>
                <flux:table.column>NIS / NIP</flux:table.column>
                <flux:table.column w-40>Tujuan</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($visits as $index => $visit)
                    <flux:table.row :key="$visit->id">
                        <flux:table.cell text-center text-zinc-500>
                            {{ $visits->firstItem() + $index }}
                        </flux:table.cell>

                        {{-- Waktu Tap --}}
                        <flux:table.cell>
                            <div class="font-bold text-gray-950 font-mono text-base">
                                {{ \Carbon\Carbon::parse($visit->visit_time)->format('H:i') }}
                            </div>
                            <span class="block text-xs text-zinc-500 font-medium uppercase tracking-wider mt-0.5">
                                {{ \Carbon\Carbon::parse($visit->visit_date)->translatedFormat('d M Y') }}
                            </span>
                        </flux:table.cell>

                        {{-- Info Pengunjung (Nama & Kelas) --}}
                        <flux:table.cell font="medium">
                            {{ $visit->user->name }}
                            <span class="block text-xs text-zinc-500 font-normal uppercase tracking-wider mt-0.5">
                                {{ $visit->user->class->name ?? ($visit->user->position ?? 'STAFF/GURU') }}
                            </span>
                        </flux:table.cell>

                        {{-- NIS / NIP --}}
                        <flux:table.cell text-zinc-600>
                            {{ $visit->user->nis ?? ($visit->user->nip ?? 'N/A') }}
                        </flux:table.cell>

                        {{-- Tujuan (Dengan Ikon Filled Sesuai Kios) --}}
                        <flux:table.cell>
                            @if($visit->purpose == 'belajar')
                                {{-- Ikon filled amber (pensil) --}}
                                <flux:button variant="filled" size="sm" icon="pencil-square"
                                    class="w-full bg-amber-600 text-white" disabled>
                                    <span class="text-xs uppercase font-black tracking-wider">Belajar</span>
                                </flux:button>
                            @else
                                {{-- Ikon filled orange (buku) --}}
                                <flux:button variant="filled" size="sm" icon="book-open" class="w-full bg-orange-600 text-white"
                                    disabled>
                                    <span class="text-xs uppercase font-black tracking-wider">Membaca</span>
                                </flux:button>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5"
                            class="py-12 text-center text-zinc-500 uppercase font-black tracking-widest text-xs opacity-60">
                            Tidak ada data kunjungan untuk tanggal ini.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $visits->links() }}
        </div>
    </flux:card>
</div>