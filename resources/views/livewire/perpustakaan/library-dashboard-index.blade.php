<div class="p-6">
    {{-- Judul Halaman Dashboard (Tema Terang) --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl" class="font-black text-gray-950 uppercase tracking-tighter leading-none">Dashboard
                Perpustakaan S7B</flux:heading>
            <flux:subheading class="text-zinc-500 font-bold text-sm mt-1 uppercase tracking-widest">Selamat Datang,
                {{ auth()->user()->name }}</flux:subheading>
        </div>
    </div>

    {{-- GRUP KARTU STATISTIK (DUMMY & REAL MIXED) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Kartu DUMMY 1: Total Buku Katalog --}}
        <flux:card class="flex items-center gap-4 border border-zinc-200">
            <div
                class="p-3 bg-red-50 dark:bg-red-950 rounded-2xl border border-red-100 dark:border-red-800 flex items-center justify-center">
                <flux:icon.book-open class="size-7 text-red-600 dark:text-red-300" />
            </div>
            <div>
                <flux:heading size="sm" class="text-zinc-500">Total Buku Katalog</flux:heading>
                <flux:heading size="xl" class="font-black text-gray-950">{{ $statisticsDummy['total_books'] }} Unit
                </flux:heading>
                <flux:subheading class="text-[10px] font-bold mt-0.5 text-zinc-400 uppercase tracking-widest">(Dummy)
                </flux:subheading>
            </div>
        </flux:card>

        {{-- Kartu DUMMY 2: Buku Terpinjam (Sirkulasi) --}}
        <flux:card
            class="flex items-center gap-4 border border-zinc-200 relative overflow-hidden bg-white dark:bg-zinc-900 shadow-none">
            <flux:icon.key class="absolute top-3 right-3 h-14 text-zinc-100 dark:text-zinc-800" />
            <div
                class="p-3 bg-orange-50 dark:bg-orange-950 rounded-2xl border border-orange-100 dark:border-orange-800 flex items-center justify-center p-3">
                <flux:icon.rectangle-stack class="size-7 text-orange-600 dark:text-orange-300" />
            </div>
            <div>
                <flux:heading size="sm" class="text-zinc-500">Buku Sedang Terpinjam</flux:heading>
                <flux:heading size="xl" class="font-black text-gray-950">{{ $statisticsDummy['total_borrowed'] }} Unit
                </flux:heading>
                <flux:subheading
                    class="text-[10px] font-bold mt-0.5 text-orange-600 dark:text-orange-400 uppercase tracking-widest">
                    (Dummy Sirkulasi)</flux:subheading>
            </div>
        </flux:card>

        {{-- Kartu REAL 1: Pengunjung Hari Ini (Sinkron dengan Kios) --}}
        <flux:card
            class="flex items-center gap-4 border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950 shadow-sm relative overflow-hidden">
            <div class="p-3 bg-emerald-100 dark:bg-emerald-900 rounded-2xl">
                <flux:icon.users class="size-7 text-emerald-600 dark:text-emerald-300" />
            </div>
            <div>
                <flux:heading size="sm" class="text-emerald-700 dark:text-emerald-400">Pengunjung Hari Ini
                </flux:heading>
                <flux:heading size="xl" class="font-black text-emerald-950">{{ $totalToday }} Siswa</flux:heading>
            </div>
            {{-- Pulsing Badge Indikator "Realtime" --}}
            <span class="absolute top-3 right-3 flex h-3 w-3">
                <span
                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span
                    class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 border-2 border-white dark:border-emerald-950"></span>
            </span>
        </flux:card>

        {{-- --- MODIFIKASI KARTU REAL 2: TOTAL PENGUNJUNG MINGGU INI --- --}}
        <flux:card
            class="flex items-center gap-4 border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950 shadow-sm relative overflow-hidden">
            <div class="p-3 bg-amber-100 dark:bg-amber-900 rounded-2xl">
                {{-- Ganti Ikon Trophy menjadi User Group/Calendar --}}
                <flux:icon.user-group class="size-7 text-amber-600 dark:text-amber-300" />
            </div>
            <div>
                {{-- Ganti Label --}}
                <flux:heading size="sm" class="text-amber-700 dark:text-amber-400">Total Pengunjung Minggu Ini
                </flux:heading>
                {{-- Ganti Variabel Data --}}
                <flux:heading size="xl" class="font-black text-amber-950 leading-tight">{{ $totalThisWeek }} Siswa
                </flux:heading>
                {{-- Subheading Bulan Sekarang sebagai Analis --}}
                <flux:heading size="sm"
                    class="text-amber-600 dark:text-amber-400 font-bold mt-1 uppercase tracking-widest text-[11px]">
                    Rekap Bulan: {{ Carbon\Carbon::now()->format('F Y') }}</flux:heading>
            </div>
        </flux:card>
    </div>

    {{-- GRUP GRAFIK ANALISIS (GRID 2 KOLOM) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        {{-- --- MODIFIKASI JUDUL GRAFIK 1: TREN KUNJUNGAN BULAN INI --- --}}
        <flux:card class="border border-zinc-200 relative">
            {{-- Ganti Judul --}}
            <flux:heading size="lg" class="font-black text-gray-950">Tren Kunjungan Bulan Ini (Per Hari)</flux:heading>
            {{-- Wadah ApexCharts (Id tetap sama, logic kustom di script bawah) --}}
            <div id="visitTrendsChart" class="h-64 mt-4 -ml-4 -mb-2"></div>
            {{-- Pulsing Badge Indikator "Realtime" --}}
            <span class="absolute top-5 right-5 flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500 border border-white"></span>
            </span>
        </flux:card>

        {{-- GRAFIK 2: Analisis Tujuan & Kategori (TAB) - Tetap Sama --}}
        <flux:card class="border border-zinc-200">
            <flux:heading size="lg"
                class="font-black text-gray-950 mb-4 pb-4 border-b border-zinc-100 dark:border-zinc-800">Analisis Tujuan
                Kunjungan (Bulan Ini)</flux:heading>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 items-center">
                <div id="visitPurposeChart" class="h-56 sm:col-span-1 -ml-4 -mb-2"></div> {{-- Wadah Pie ApexCharts --}}
                {{-- Info Detail Tujuan --}}
                <div class="sm:col-span-2 space-y-4">
                    <div
                        class="bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 rounded-xl p-5 flex items-center justify-between">
                        <flux:heading size="sm"
                            class="text-amber-700 dark:text-amber-400 font-bold uppercase tracking-widest text-xs flex items-center gap-2">
                            <flux:icon.pencil-square class="size-4" /> Belajar</flux:heading>
                        <flux:heading size="xl" class="font-black text-amber-950">{{ $totalPurposeBelajarMonth }}
                        </flux:heading>
                    </div>
                    <div
                        class="bg-orange-50 dark:bg-orange-950 border border-orange-200 dark:border-orange-800 rounded-xl p-5 flex items-center justify-between">
                        <flux:heading size="sm"
                            class="text-orange-700 dark:text-orange-400 font-bold uppercase tracking-widest text-xs flex items-center gap-2">
                            <flux:icon.book-open class="size-4" /> Membaca</flux:heading>
                        <flux:heading size="xl" class="font-black text-orange-950">{{ $totalPurposeMembacaMonth }}
                        </flux:heading>
                    </div>
                </div>
            </div>
        </flux:card>
    </div>

    {{-- GRUP DETAIL PREVIEW (PREVIEW TABEL HARI INI) - Tetap Sama --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KOLOM KIRI (PREVIEW LAPORAN HARI INI) - 2 Kolom Lebar --}}
        <div class="lg:col-span-2">
            <flux:card class="border border-zinc-200 h-full">
                {{-- Judul & Toolbar Laporan --}}
                <div
                    class="flex items-center justify-between gap-4 mb-5 pb-5 border-b border-zinc-100 dark:border-zinc-800">
                    <flux:heading size="lg" class="font-black text-gray-950">Preview Laporan Pengunjung Hari Ini
                    </flux:heading>
                    {{-- Pencarian --}}
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari Nama, NIS, atau NIP..."
                        icon="magnifying-glass" size="sm" class="max-w-xs flex-grow" />
                </div>

                {{-- TABEL REKAP KUNJUNGAN (REAL) --}}
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column w-12 text-center>No</flux:table.column>
                        <flux:table.column>Waktu</flux:table.column>
                        <flux:table.column>Informasi Pengunjung</flux:table.column>
                        <flux:table.column w-40>Tujuan</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse($todayVisitsPagination as $index => $visit)
                            <flux:table.row :key="$visit->id">
                                {{-- No Urut --}}
                                <flux:table.cell text-center text-zinc-500>
                                    {{ $todayVisitsPagination->firstItem() + $index }}
                                </flux:table.cell>

                                {{-- Waktu Tap --}}
                                <flux:table.cell>
                                    <div class="font-bold text-gray-950 font-mono text-base">
                                        {{ \Carbon\Carbon::parse($visit->visit_time)->format('H:i') }}
                                    </div>
                                </flux:table.cell>

                                {{-- Info Pengunjung (Nama & Kelas) --}}
                                <flux:table.cell font="medium">
                                    {{ $visit->user->name }}
                                    <span class="block text-xs text-zinc-500 font-normal uppercase tracking-wider mt-0.5">
                                        {{ $visit->user->class->name ?? ($visit->user->position ?? 'STAFF/GURU') }}
                                    </span>
                                </flux:table.cell>

                                {{-- Tujuan (Badge Sesuai Kios) --}}
                                <flux:table.cell>
                                    @if($visit->purpose == 'belajar')
                                        <flux:badge color="amber" size="sm"
                                            class="w-full text-center text-[10px] font-black uppercase tracking-wider">Belajar
                                        </flux:badge>
                                    @else
                                        <flux:badge color="orange" size="sm"
                                            class="w-full text-center text-[10px] font-black uppercase tracking-wider">Membaca
                                        </flux:badge>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="py-12 text-center text-zinc-500 uppercase font-black tracking-widest text-xs opacity-60">
                                    Belum ada kunjungan hari ini.</td>
                            </tr>
                        @endforelse
                    </flux:table.rows>
                </flux:table>

                {{-- PAGINATION & LINK KE LAPORAN --}}
                <div class="mt-4 flex items-center justify-between">
                    {{ $todayVisitsPagination->links() }}
                    <flux:button :href="route('library.report.index')" variant="subtle" size="sm" icon="chart-pie"
                        wire:navigate>Buka Laporan Lengkap</flux:button>
                </div>
            </flux:card>
        </div>

        {{-- KOLOM KANAN (DUMMY: BUKU POPULER) - 1 Kolom Sempit - Tetap Sama --}}
        <div class="lg:col-span-1">
            <flux:card class="border border-zinc-200 h-full relative overflow-hidden bg-white/40 dark:bg-black/20">
                <flux:icon.key class="absolute top-5 right-5 h-20 text-zinc-200 dark:text-zinc-800" />
                <flux:heading size="lg" class="font-black text-gray-950 mb-5">5 Buku Terpopuler Bulan Ini</flux:heading>

                <div class="space-y-4 relative z-10">
                    {{-- Pastikan ada simbol $ di depan topBooksDummy --}}
                    @foreach($topBooksDummy as $book)
                        <div
                            class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 flex items-center justify-between opacity-60">
                            <div>
                                <flux:heading size="sm" class="font-black text-gray-950 leading-tight">{{ $book['title'] }}
                                </flux:heading>
                                <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest mt-1">
                                    {{ $book['author'] }}</p>
                            </div>
                            <flux:badge color="zinc" size="sm"
                                class="text-[10px] font-black uppercase tracking-wider shrink-0">{{ $book['borrow_count'] }}
                                Pinjaman</flux:badge>
                        </div>
                    @endforeach
                </div>
                <flux:subheading class="mt-6 font-black uppercase tracking-[0.4em] text-sm text-zinc-500 text-center">
                    (FITUR DUMMY SIRKULASI)</flux:subheading>
            </flux:card>
        </div>
    </div>
</div>


{{-- ============================================= --}}
{{-- --- SCRIPTS: INIALISASI APEXCHARTS --- --}}
{{-- ============================================= --}}
{{-- --- KITA MENYESUAIKAN TOOLTIP AGAR PAS DENGAN DATA BULANAN --- --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        // Ambil data JSON dari properti Livewire
        const visitData = JSON.parse(@this.visitChartData);
        const purposeData = JSON.parse(@this.purposeChartData);

        // -- KONFIGURASI GRAFIK 1: TREN KUNJUNGAN BULAN INI (LINE CHART) --
        const visitTrendsChart = new ApexCharts(document.getElementById('visitTrendsChart'), {
            series: [{
                name: 'Pengunjung',
                data: visitData.series // Data Angka
            }],
            chart: {
                type: 'line',
                height: 250,
                sparkline: { enabled: true }, // Gunakan sparkline untuk tampilan bersih/kaku
                zoom: { enabled: false },
                toolbar: { show: false }
            },
            colors: ['#ef4444'], // Red-500 (Tema Terang S7B)
            stroke: { curve: 'smooth', width: 4 },
            labels: visitData.labels, // Data Tanggal (Sekarang format: j M)
            grid: { show: false },
            xaxis: {
                labels: { show: false },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { show: false }
            },
            tooltip: {
                theme: 'light',
                x: { show: true } 
            }
        });
        visitTrendsChart.render();

        // -- KONFIGURASI GRAFIK 2: TUJUAN KUNJUNGAN (PIE CHART) - Tetap Sama --
        const visitPurposeChart = new ApexCharts(document.getElementById('visitPurposeChart'), {
            series: purposeData.series, 
            labels: purposeData.labels, 
            chart: {
                type: 'pie',
                height: 250,
                toolbar: { show: false }
            },
            colors: ['#f59e0b', '#f97316'], 
            legend: { show: false }, 
            dataLabels: { enabled: false },
            tooltip: {
                theme: 'light',
                y: { formatter: (value) => `${value} Siswa` }
            }
        });
        visitPurposeChart.render();
    });
</script>