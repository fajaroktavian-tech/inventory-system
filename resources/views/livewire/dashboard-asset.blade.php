<div class="p-6 space-y-6">
    @if(request()->routeIs('public.display'))
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-black">MONITORING LOGISTIK SARPRAS</h1>
            <p class="text-zinc-400">SMKN 7 BALEENDAH</p>
        </div>
    @endif

    <div class="flex flex-col">
        <flux:heading size="xl">Dashboard Inventaris Aset</flux:heading>
        <flux:subheading>Selamat datang, {{ auth()->user()->name }}. Berikut adalah ikhtisar aset fisik sekolah saat ini.</flux:subheading>
    </div>

    {{-- 1. CEK ROLE: JIKA ADMIN/OWNER/PETUGAS, TAMPILKAN STATISTIK UTAMA --}}
    @if(in_array(auth()->user()->role, ['admin', 'owner', 'petugas']))
        
        {{-- Indikator Terakhir Diperbarui --}}
        <div class="flex items-center gap-2 bg-white dark:bg-zinc-900 px-3 py-1.5 rounded-full border border-zinc-200 dark:border-zinc-700 shadow-sm w-fit">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <flux:text size="sm" class="text-zinc-500 font-medium">
                Terakhir diperbarui: <span class="text-zinc-800 dark:text-zinc-200 font-mono">{{ $lastUpdated }}</span>
            </flux:text>
        </div>

        {{-- Grid Card Statistik Utama --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- Card Total Nilai Aset --}}
            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/50 rounded-lg text-emerald-600 dark:text-emerald-400">
                    <flux:icon name="banknotes" />
                </div>
                <div>
                    <flux:text size="xs" class="text-zinc-500 uppercase tracking-wider font-semibold">Kekayaan Aset</flux:text>
                    <flux:heading size="lg" class="text-emerald-600 dark:text-emerald-400 font-bold">
                        Rp {{ number_format($totalValue, 0, ',', '.') }}
                    </flux:heading>
                </div>
            </div>

            {{-- Card Total Unit --}}
            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-950/50 rounded-lg text-blue-600 dark:text-blue-400">
                    <flux:icon name="rectangle-stack" />
                </div>
                <div>
                    <flux:text size="xs" class="text-zinc-500 uppercase tracking-wider font-semibold">Total Unit Fisik</flux:text>
                    <flux:heading size="xl">{{ $totalUnit }} <span class="text-xs font-normal text-zinc-400">Unit</span></flux:heading>
                </div>
            </div>

            {{-- Card Lokasi Ketersediaan / Sirkulasi --}}
            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-center text-xs text-zinc-500 font-medium">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500"></span>Tersedia: <b>{{ $totalAvailable }}</b></span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span>Dipinjam: <b>{{ $totalLoaned }}</b></span>
                </div>
                <div class="mt-3 h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden flex">
                    @php 
                        $availablePct = $totalUnit > 0 ? ($totalAvailable / $totalUnit) * 100 : 0;
                        $loanedPct = $totalUnit > 0 ? ($totalLoaned / $totalUnit) * 100 : 0;
                    @endphp
                    <div style="width: {{ $availablePct }}%" class="bg-green-500 h-full"></div>
                    <div style="width: {{ $loanedPct }}%" class="bg-blue-500 h-full"></div>
                </div>
            </div>

            {{-- Card Warning Rusak Berat --}}
            <div class="p-4 bg-red-50/60 dark:bg-red-950/20 border border-red-200 dark:border-red-900/40 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-red-100 dark:bg-red-900/50 rounded-lg text-red-600 dark:text-red-400">
                    <flux:icon name="exclamation-triangle" />
                </div>
                <div>
                    <flux:text size="xs" class="text-red-700 dark:text-red-400 font-semibold uppercase tracking-wider">Rusak Berat</flux:text>
                    <flux:heading size="xl" class="text-red-600 dark:text-red-400 font-bold">{{ $totalBroken }} <span class="text-xs font-normal">Unit</span></flux:heading>
                </div>
            </div>

        </div>

        {{-- SEKTOR NOTIFIKASI JATUH TEMPO --}}
    <div class="mt-6">
        <flux:card>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <flux:icon name="clock" class="text-red-500" />
                    <flux:heading size="lg">Peminjaman Jatuh Tempo (Overdue)</flux:heading>
                </div>
                @if($overdueLoans->count() > 0)
                    <flux:badge color="red" size="sm" inset="right">{{ $overdueLoans->count() }} Peringatan</flux:badge>
                @endif
            </div>

            <flux:subheading class="-mt-2 mb-4">Daftar aset yang telah melewati batas tanggal pengembalian wajib.</flux:subheading>

            <div class="space-y-3">
            @forelse($overdueLoans as $loan)
    @php
        $dueDate = \Carbon\Carbon::parse($loan->due_date);
        $daysOverdue = $dueDate->diffInDays(\Carbon\Carbon::today());
        $borrowerName = $loan->user->name ?? 'User';
        // Ambil info nama barang bersarang dari relasi Asset -> ItemInfo
        $assetName = $loan->asset->itemInfo->name ?? 'Unit Aset';
    @endphp

    <div class="p-4 bg-red-50/40 dark:bg-red-950/10 border border-red-100 dark:border-red-900/30 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 flex items-center justify-center font-bold flex-shrink-0 text-sm">
                {{ substr($borrowerName, 0, 1) }}
            </div>
            <div>
                <h4 class="text-sm font-semibold text-zinc-900 dark:text-white leading-tight">
                    {{ $borrowerName }}
                </h4>
                <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">
                    Meminjam: <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $assetName }}</span> 
                    @if(isset($loan->asset->serial_number))
                        <span class="font-mono text-[11px] bg-zinc-200 dark:bg-zinc-800 px-1 rounded">[{{ $loan->asset->serial_number }}]</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center justify-between sm:justify-end gap-4 border-t sm:border-t-0 pt-2 sm:pt-0 border-red-100 dark:border-zinc-800">
            <div class="text-left sm:text-right">
                <p class="text-xs text-zinc-500">Batas Kembali:</p>
                <p class="text-xs font-bold text-red-600 dark:text-red-400">
                    {{ $dueDate->translatedFormat('d M Y') }} <span class="text-[10px] font-normal bg-red-600 text-white px-1.5 py-0.5 rounded-full ml-1">Terlambat {{ $daysOverdue }} Hari</span>
                </p>
            </div>
            
            <flux:button size="sm" variant="ghost" icon="phone" href="{{ route('asset-loans.index', ['search' => $borrowerName]) }}" wire:navigate>
                Tagih
            </flux:button>
        </div>
    </div>
@empty
    <div class="text-center py-8 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/30 dark:bg-zinc-900/10">
        <flux:icon name="check-circle" class="mx-auto size-8 text-green-500 mb-2" />
        <p class="text-sm text-zinc-500 dark:text-zinc-400 font-medium">Luar biasa! Tidak ada peminjaman aset yang terlambat.</p>
    </div>
@endforelse
            </div>
        </flux:card>
    </div>

    {{-- SEKTOR AKTIVITAS TERBARU (TIMELINE STYLE) --}}
    <div class="mt-6">
        <flux:card>
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="clock" class="text-zinc-500" />
                <flux:heading size="lg">Aktivitas Terbaru</flux:heading>
            </div>
            <flux:subheading class="-mt-2 mb-6">Log rekaman otomatis pendaftaran fisik dan sirkulasi peminjaman aset.</flux:subheading>

            <div class="relative">

    {{-- Garis Vertikal Latar Belakang --}}
    {{-- Kita posisikan garis ini tepat di tengah-tengah ikon (ikon lebar 7 = center di 3.5/14px) --}}
    <div class="absolute left-[14px] top-2 bottom-2 w-0.5 bg-zinc-100 dark:bg-zinc-800"></div>

    <div class="space-y-6">
        @forelse($activities as $activity)
            <div class="relative flex items-start gap-4">
                
                {{-- Lingkaran Icon Status --}}
                {{-- Kita gunakan flex-shrink-0 agar lingkaran tidak gepeng dan z-10 agar di atas garis --}}
                <div class="relative z-10 w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 ring-4 ring-white dark:ring-zinc-900 
                    @if($activity['color'] === 'blue') bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400
                    @elseif($activity['color'] === 'green') bg-green-50 text-green-600 dark:bg-green-950/40 dark:text-green-400
                    @else bg-purple-50 text-purple-600 dark:bg-purple-950/40 dark:text-purple-400 @endif">
                    <flux:icon name="{{ $activity['icon'] }}" variant="mini" class="size-4" />
                </div>

                {{-- Konten Detail Aktivitas --}}
                <div class="flex-1 border-b border-zinc-100 dark:border-zinc-800/60 pb-4 last:border-0 last:pb-0">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex flex-col">
                            <h4 class="text-sm font-semibold text-zinc-950 dark:text-white">
                                {{ $activity['title'] }}
                            </h4>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">
                                {{ $activity['description'] }}
                            </p>
                        </div>
                        <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono whitespace-nowrap mt-1">
                            {{ $activity['date']->diffForHumans() }}
                        </span>
                    </div>
                </div>

            </div>
        @empty
            <div class="text-center py-6 text-sm text-zinc-400">
                Belum ada aktivitas sirkulasi atau registrasi barang minggu ini.
            </div>
        @endforelse
    </div>
</div>
            
            <!-- Tombol Navigasi Pintas -->
            <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end">
                <flux:button variant="ghost" size="sm" icon="arrow-right" icon-trailing href="{{ route('asset-registration.index') }}" wire:navigate>
                    Lihat Asset Master
                </flux:button>
            </div>
        </flux:card>
    </div>

    {{-- SEKTOR SEBARAN ASET PER RUANGAN --}}
    <div class="mt-6">
        <flux:card>
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="building-office-2" class="text-zinc-500" />
                <flux:heading size="lg">Sebaran Aset per Ruangan</flux:heading>
            </div>
            <flux:subheading class="-mt-2 mb-6">Perbandingan jumlah unit aset fisik yang tersebar di berbagai lokasi ruangan.</flux:subheading>

            <div class="relative w-full" style="min-height: 300px;">
                <canvas id="roomDistributionChart"></canvas>
            </div>
        </flux:card>
    </div>

        {{-- SEKTOR GRAFIK DISTRIBUSI KONDISI ASET --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Card Grafik (Mengambil 2 Kolom pada Layar Lebar) --}}
    <flux:card class="lg:col-span-2 p-6 flex flex-col justify-between">
        <div class="mb-4">
            <flux:heading size="lg" icon="chart-pie">Visualisasi Distribusi Kondisi Aset</flux:heading>
            <flux:subheading>Rasio perbandingan kondisi fisik seluruh unit aset sekolah.</flux:subheading>
        </div>
        
        {{-- Canvas Chart.js --}}
        <div class="relative w-full mx-auto flex justify-center items-center" style="max-height: 280px;">
            <canvas id="assetConditionChart"></canvas>
        </div>
    </flux:card>

    {{-- Card Analisis/Rekomendasi Pimpinan (Mengambil 1 Kolom) --}}
    <flux:card class="p-6 flex flex-col justify-between bg-zinc-50/50 dark:bg-zinc-900/30">
        <div>
            <flux:heading size="md" icon="document-text">Rekomendasi Sistem</flux:heading>
            <flux:subheading class="mt-1">Poin tindakan manajemen aset terdeteksi:</flux:subheading>
            
            <div class="mt-4 space-y-3">
                @if($conditionStats['rusak_berat'] > 0)
                    <div class="p-3 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/40 rounded-xl text-xs text-red-800 dark:text-red-300">
                        <strong class="block mb-0.5">⚠️ Butuh Pengadaan / Penghapusan</strong>
                        Ditemukan {{ $conditionStats['rusak_berat'] }} unit rusak berat. Direkomendasikan melakukan proses *write-off* (penghapusan) pengarsipan atau pengajuan unit baru.
                    </div>
                @endif

                @if($conditionStats['rusak_ringan'] > 0)
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40 rounded-xl text-xs text-amber-800 dark:text-amber-300">
                        <strong class="block mb-0.5">🔧 Jadwalkan Maintenance</strong>
                        Terdapat {{ $conditionStats['rusak_ringan'] }} unit berstatus rusak ringan. Lakukan pengecekan rutin atau servis berkala untuk mencegah kerusakan permanen.
                    </div>
                @endif

                @if($conditionStats['rusak_berat'] == 0 && $conditionStats['rusak_ringan'] == 0)
                    <div class="p-3 bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-900/40 rounded-xl text-xs text-green-800 dark:text-green-300">
                        <strong class="block mb-0.5">Kondisi Prima</strong>
                        Seluruh aset terdata dalam kondisi baik. Pertahankan manajemen perawatan lingkungan ruangan.
                    </div>
                @endif
            </div>
        </div>

        <flux:button size="sm" variant="subtle" class="w-full mt-4" href="{{ route('asset-report') }}" wire:navigate>
            Lihat Rekap Model Barang
        </flux:button>
    </flux:card>
</div>

{{-- SCRIPT SINKRONISASI CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:navigated', () => {
        
        // --- 1. INISIALISASI ASSET CONDITION CHART (DOUGHNUT) ---
        const ctxCondition = document.getElementById('assetConditionChart');
        if (ctxCondition) {
            const existingConditionChart = Chart.getChart(ctxCondition);
            if (existingConditionChart) {
                existingConditionChart.destroy();
            }

            new Chart(ctxCondition, {
                type: 'doughnut',
                data: {
                    labels: ['Baik', 'Rusak Ringan', 'Rusak Berat'],
                    datasets: [{
                        data: [
                            {{ $conditionStats['baik'] }},
                            {{ $conditionStats['rusak_ringan'] }},
                            {{ $conditionStats['rusak_berat'] }}
                        ],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 2,
                        borderColor: document.documentElement.classList.contains('dark') ? '#18181b' : '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#27272a',
                                font: { family: 'ui-sans-serif, system-ui', size: 12 },
                                padding: 20
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // --- 2. INISIALISASI ROOM DISTRIBUTION CHART (BAR HORIZONTAL) ---
        const ctxRoom = document.getElementById('roomDistributionChart');
        if (ctxRoom) {
            const existingRoomChart = Chart.getChart(ctxRoom);
            if (existingRoomChart) {
                existingRoomChart.destroy();
            }

            new Chart(ctxRoom, {
                type: 'bar',
                data: {
                    labels: @json($roomLabels),
                    datasets: [{
                        label: 'Jumlah Unit',
                        data: @json($roomData),
                        backgroundColor: '#3b82f6', // Blue-500
                        borderRadius: 6,
                        barThickness: 20,
                    }]
                },
                options: {
                    indexAxis: 'y', // Membuat grafik jadi horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                stepSize: 1,
                                color: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#71717a'
                            }
                        },
                        y: {
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                color: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#71717a',
                                font: { weight: '500' }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

    {{-- 2. JIKA YANG LOGIN ADALAH SISWA/GURU --}}
    @else
        <div class="bg-emerald-600 rounded-3xl p-8 text-white shadow-lg overflow-hidden relative">
            <div class="relative z-10">
                <h1 class="text-2xl font-bold">Peminjaman Fasilitas & Aset</h1>
                <p class="mt-2 text-emerald-100 max-w-md">Memerlukan proyektor, kunci ruangan laboratorium, atau perangkat penunjang belajar lainnya? Daftarkan peminjaman barang Anda.</p>
            </div>
            <flux:icon name="rectangle-stack" class="absolute -right-10 -bottom-10 size-64 opacity-10 rotate-12" />
        </div>

        <div class="mt-8">
            <flux:heading class="mb-4" icon="clock">Status Peminjaman Aset Anda</flux:heading>
            {{-- Tempatkan Komponen status request aset khusus user jika ada --}}
            <p class="text-sm text-zinc-400 bg-zinc-50 dark:bg-zinc-900 p-4 rounded-xl border border-dashed">Fitur pelacakan mandiri unit dipinjam aktif.</p>
        </div>
    @endif
</div>