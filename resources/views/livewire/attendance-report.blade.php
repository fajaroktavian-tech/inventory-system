<div class="p-6 lg:p-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <flux:heading size="xl" level="1">Rekap Absensi Kustom</flux:heading>
            <flux:subheading>Periode: <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ $summaryDate }}</span>
            </flux:subheading>
        </div>

        <div class="flex gap-3">
            <flux:button icon="question-mark-circle" wire:click="$set('isRecapGuideOpen', true)" variant="ghost">Panduan
            </flux:button>
            <flux:button wire:click="exportExcel" variant="filled" icon="document-text" color="zinc">
                Export Excel
            </flux:button>
        </div>
    </div>

    {{-- KARTU STATISTIK (OVERVIEW) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Rata-rata Kehadiran --}}
        <flux:card class="flex items-center gap-4 py-6">
            <div class="p-3 bg-emerald-100 dark:bg-emerald-500/20 rounded-2xl">
                <flux:icon.chart-bar class="text-emerald-600 dark:text-emerald-400" />
            </div>
            <div>
                <flux:text size="xs" class="uppercase font-bold tracking-wider text-zinc-500">Rata-rata Hadir
                </flux:text>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black">{{ $stats['rata_rata'] }}%</span>
                    <flux:text size="xs" class="text-zinc-400">Tepat Waktu</flux:text>
                </div>
            </div>
        </flux:card>

        {{-- Siswa Paling Rajin --}}
        <flux:card class="flex items-center gap-4 py-6 border-t-4 border-t-blue-500">
            <div class="p-3 bg-blue-100 dark:bg-blue-500/20 rounded-2xl">
                <flux:icon.trophy class="text-blue-600 dark:text-blue-400" />
            </div>
            <div class="overflow-hidden">
                <flux:text size="xs" class="uppercase font-bold tracking-wider text-zinc-500">Siswa Terajin (100%)
                </flux:text>
                <div class="text-lg font-bold text-zinc-800 dark:text-white truncate"
                    title="{{ $stats['siswa_rajin'] }}">
                    {{ $stats['siswa_rajin'] }}
                </div>
            </div>
        </flux:card>

        {{-- Siswa Paling Sering Terlambat --}}
        <flux:card class="flex items-center gap-4 py-6 border-t-4 border-t-rose-500">
            <div class="p-3 bg-rose-100 dark:bg-rose-500/20 rounded-2xl">
                <flux:icon.exclamation-triangle class="text-rose-600 dark:text-rose-400" />
            </div>
            <div class="overflow-hidden">
                <flux:text size="xs" class="uppercase font-bold tracking-wider text-zinc-500">Sering Terlambat
                </flux:text>
                <div class="text-lg font-bold text-rose-600 truncate" title="{{ $stats['siswa_terlambat'] }}">
                    {{ $stats['siswa_terlambat'] }}
                </div>
            </div>
        </flux:card>
    </div>

    <flux:card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            {{-- Input Tanggal Mulai --}}
            <flux:input type="date" wire:model.live="startDate" label="Dari Tanggal" />

            {{-- Input Tanggal Selesai --}}
            <flux:input type="date" wire:model.live="endDate" label="Sampai Tanggal" />

            {{-- Filter Kelas --}}
            <div class="flex flex-col gap-2">
                <flux:label>Filter Kelas</flux:label>
                <div class="relative">
                    <flux:input wire:model.live="class_name_search" list="class-list" placeholder="Ketik nama kelas..."
                        icon="academic-cap" />
                    <datalist id="class-list">
                        @foreach($classes as $class)
                            <option value="{{ $class->name }}">
                        @endforeach
                    </datalist>
                </div>
            </div>

            {{-- Pencarian --}}
            <flux:input wire:model.live.debounce.500ms="search" label="Cari Siswa" icon="magnifying-glass"
                placeholder="Nama atau NIS..." />
        </div>
    </flux:card>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Nama Siswa</flux:table.column>
                <flux:table.column>Kelas</flux:table.column>
                <flux:table.column class="text-center">Hadir</flux:table.column>
                <flux:table.column class="text-center">Terlambat</flux:table.column>
                <flux:table.column class="text-center">Izin/Sakit</flux:table.column>
                <flux:table.column class="text-center">Alpa</flux:table.column>
                <flux:table.column class="text-center">Efektivitas</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($recapData as $row)
                    @php
                        // $hariEfektif didapat dari variabel yang kita kirim dari render()
                        $totalMasuk = $row->total_hadir + $row->total_terlambat + $row->total_izin;

                        // Alpa = Hari Efektif dikurangi total kehadiran/izin
                        $alpa = max(0, $hariEfektif - $totalMasuk);

                        // Persentase Kehadiran
                        $persentase = $hariEfektif > 0 ? round(($totalMasuk / $hariEfektif) * 100) : 0;
                    @endphp
                    <flux:table.row :key="$row->id">
                        <flux:table.cell>
                            <div class="font-bold text-zinc-800 dark:text-white">{{ $row->name }}</div>
                            <div class="text-[10px] text-zinc-500 font-mono">{{ $row->nis }}</div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $row->class->name ?? '-' }}</flux:table.cell>

                        {{-- Data Statistik --}}
                        <flux:table.cell class="text-center font-bold text-emerald-600">{{ $row->total_hadir }}
                        </flux:table.cell>
                        <flux:table.cell class="text-center font-bold text-rose-600">{{ $row->total_terlambat }}
                        </flux:table.cell>
                        <flux:table.cell class="text-center font-bold text-blue-600">{{ $row->total_izin }}
                        </flux:table.cell>

                        {{-- Kolom Alpa sekarang lebih akurat --}}
                        <flux:table.cell class="text-center font-bold text-zinc-400">
                            {{ $alpa }}
                        </flux:table.cell>

                        <flux:table.cell class="text-center">
                            <flux:badge size="sm" :color="$persentase > 90 ? 'green' : ($persentase > 75 ? 'zinc' : 'red')"
                                inset="right">
                                {{ $persentase }}%
                            </flux:badge>
                            <flux:button variant="ghost" size="sm" icon="eye"
                                wire:click="showStudentDetail({{ $row->id }})" />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-12 text-zinc-500 italic">
                            Data tidak ditemukan.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-6">
            {{ $recapData->links() }}
        </div>
    </flux:card>

    <flux:modal wire:model="isRecapGuideOpen" class="md:w-[600px]">
        <div class="space-y-6">
            <flux:heading size="lg">Panduan Membaca Rekap Absensi</flux:heading>

            <div class="space-y-4 text-sm text-zinc-600 dark:text-zinc-300">
                <p>Halaman ini memberikan ringkasan kehadiran siswa dalam periode tertentu. Berikut cara membaca
                    informasinya:</p>

                <div class="grid gap-4">
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                        <flux:heading size="sm">Statistik (Atas)</flux:heading>
                        <ul class="list-disc list-inside mt-2 space-y-1 text-xs">
                            <li><strong>Rata-rata Hadir:</strong> Persentase kehadiran seluruh siswa di periode yang
                                dipilih.</li>
                            <li><strong>Siswa Terajin:</strong> Siswa dengan tingkat kehadiran 100% (Tanpa Alpa/Sakit).
                            </li>
                            <li><strong>Sering Terlambat:</strong> Siswa yang memiliki akumulasi keterlambatan paling
                                tinggi.</li>
                        </ul>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                        <flux:heading size="sm">Cara Hitung Efektivitas</flux:heading>
                        <p class="text-xs mt-2 italic">Efektivitas = (Total Kehadiran ÷ Jumlah Hari Efektif) × 100%</p>
                        <ul class="list-disc list-inside mt-2 text-xs space-y-1">
                            <li><strong>Alpa:</strong> Dihitung otomatis jika siswa tidak tercatat Hadir, Izin, atau
                                Sakit pada hari sekolah.</li>
                            <li><strong>Warna Badge:</strong> Hijau (>90%), Abu-abu (75-90%), Merah (<75%).< /li>
                        </ul>
                    </div>
                </div>

                <p class="text-xs text-zinc-500"><strong>Tips:</strong> Gunakan filter tanggal dan pencarian nama untuk
                    mendapatkan data yang spesifik. Klik "Export Excel" untuk mengunduh laporan ke perangkat Anda.</p>
            </div>

            <div class="flex justify-end">
                <flux:button variant="primary" wire:click="$set('isRecapGuideOpen', false)">Mengerti</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal wire:model="isDetailModalOpen" class="md:w-[600px]">
    @if($selectedStudentDetail)
        <div class="space-y-4">
            <flux:heading size="lg">Riwayat Kehadiran: {{ $selectedStudentDetail->name }}</flux:heading>
            
            {{-- Tambahkan pembungkus dengan overflow-y-auto dan max-height --}}
            <div class="overflow-y-auto max-h-[400px] border border-zinc-200 dark:border-zinc-700 rounded-lg">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sticky>Tanggal</flux:table.column>
                        <flux:table.column>Waktu</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach($selectedStudentDetail->attendances as $att)
                            <flux:table.row>
                                <flux:table.cell>{{ $att->created_at->format('d M Y') }}</flux:table.cell>
                                <flux:table.cell>{{ $att->time_in ?? '-' }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="{{ $att->status === 'hadir' ? 'green' : 'red' }}">
                                        {{ strtoupper($att->status) }}
                                    </flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>

            <div class="flex justify-end pt-2">
                <flux:button wire:click="$set('isDetailModalOpen', false)">Tutup</flux:button>
            </div>
        </div>
    @endif
</flux:modal>
</div>