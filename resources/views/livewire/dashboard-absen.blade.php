<div class="p-6 space-y-6">
    <flux:heading size="xl">Dashboard Absensi</flux:heading>
    @if($isHoliday)
    <div class="p-4 bg-blue-100 text-blue-800 rounded-lg font-bold">
        Hari ini adalah hari libur: {{ $holidayName ?? 'Libur Nasional/Sekolah' }}
    </div>
@endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Siswa --}}
        <flux:card class="p-4 border-l-4 border-orange-500">
            <flux:subheading>Total Siswa</flux:subheading>
            <div class="text-3xl font-black mt-2 text-orange-600">{{ $totalSiswa }}</div>
        </flux:card>
        
        {{-- Total Hadir --}}
        <flux:card class="p-4 border-l-4 border-emerald-500">
            <flux:subheading>Total Hadir</flux:subheading>
            <div class="text-3xl font-black mt-2">{{ $stats['total_hadir'] }}</div>
        </flux:card>

        {{-- Persentase --}}
        <flux:card class="p-4 border-l-4 border-blue-500">
            <flux:subheading>Persentase</flux:subheading>
            <div class="text-3xl font-black mt-2">{{ $stats['persentase'] }}%</div>
        </flux:card>

        {{-- Terlambat --}}
        <flux:card class="p-4 border-l-4 border-orange-500">
            <flux:subheading>Terlambat</flux:subheading>
            <div class="text-3xl font-black mt-2 text-orange-600">{{ $stats['terlambat'] }}</div>
        </flux:card>

        {{-- Sakit/Izin --}}
        <flux:card class="p-4 border-l-4 border-orange-500">
            <flux:subheading>Sakit/Izin</flux:subheading>
            <div class="text-3xl font-black mt-2 text-orange-600">{{ $stats['sakit_izin'] }}</div>
        </flux:card>

        {{-- Alpa --}}
        <flux:card class="p-4 border-l-4 border-rose-500">
            <flux:subheading>Belum Absen (Alpa)</flux:subheading>
            <div class="text-3xl font-black mt-2 text-rose-600">{{ $stats['alpa'] }}</div>
        </flux:card>
    </div>

    <div x-data="{}" x-init="
            // Pastikan DOM sudah siap
            new ApexCharts(document.querySelector('#lineChart'), {
                chart: { type: 'line', height: 250 },
                series: [{ name: 'Siswa Hadir', data: {{ $lineChartData }} }],
                xaxis: { categories: {!! $lineCategories !!} },
                stroke: { curve: 'smooth' }
            }).render();

            new ApexCharts(document.querySelector('#barChart'), {
                chart: { type: 'bar', height: 250 },
                series: [{ name: 'Siswa Hadir', data: {{ $classCounts }} }],
                xaxis: { categories: {!! $classNames !!} }
            }).render();
        ">
        <div id="lineChart"></div>
        <div id="barChart"></div>

        <script>
            document.addEventListener('livewire:navigated', () => {
                // Inisialisasi Chart di sini
                new ApexCharts(document.querySelector("#lineChart"), {
                    chart: { type: 'line', height: 250 },
                    series: [{ name: 'Siswa Hadir', data: {!! $lineChartData !!} }],
                    xaxis: { categories: {!! $lineCategories !!} }
                }).render();

                new ApexCharts(document.querySelector("#barChart"), {
                    chart: { type: 'bar', height: 250 },
                    series: [{ name: 'Siswa Hadir', data: {!! $classCounts !!} }],
                    xaxis: { categories: {!! $classNames !!} }
                }).render();
            });
        </script>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        {{-- Tabel Live Taps (5 Terakhir) --}}
        <flux:card class="p-4">
            <flux:heading size="sm" class="mb-4">Log Absensi Terbaru</flux:heading>
            <div class="space-y-3">
                @foreach($recentTaps as $tap)
                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                        <div>
                            <div class="font-bold text-sm">{{ $tap->student->name }}</div>
                            <div class="text-xs text-zinc-500">{{ $tap->student->class->name ?? '-' }}</div>
                        </div>
                        <div class="text-xs font-mono font-bold text-emerald-600">
                            {{ \Carbon\Carbon::parse($tap->time_in)->format('H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </flux:card>

        {{-- Tabel Siswa Terlambat --}}
        <flux:card class="p-4 border-l-4 border-rose-500">
            <flux:heading size="sm" class="mb-4 text-rose-600">Siswa Terlambat Hari Ini</flux:heading>
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-zinc-500 text-xs">
                        <th class="pb-2">Nama</th>
                        <th class="pb-2 text-center">Kelas</th>
                        <th class="pb-2 text-right">Jam Masuk</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($lateStudents as $late)
                        <tr class="border-t border-zinc-100 dark:border-zinc-700">
                            <td class="py-2 font-medium">{{ $late->student->name }}</td>
                            <td class="py-2 text-center text-zinc-500">{{ $late->student->class->name ?? '-' }}</td>
                            <td class="py-2 text-right font-mono text-rose-600 font-bold">
                                {{ \Carbon\Carbon::parse($late->time_in)->format('H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-zinc-400 italic text-xs">Tidak ada siswa terlambat
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </flux:card>
        <flux:card class="p-4 border-l-4 border-zinc-500 mt-6">
            <div class="flex justify-between items-center mb-4">
                <flux:heading size="sm" class="text-zinc-600">Siswa Belum Absen ({{ $absentStudents->count() }})
                </flux:heading>
                <flux:button variant="primary" color="danger" size="sm" icon="document-arrow-down"
                    href="{{ route('attendance.export-alpa') }}" target="_blank">
                    Eksport PDF
                </flux:button>
            </div>

            <div class="max-h-60 overflow-y-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 bg-white dark:bg-zinc-900">
                        <tr class="text-zinc-500 text-xs">
                            <th class="pb-2">Nama Siswa</th>
                            <th class="pb-2 text-center">Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absentStudents as $student)
                            <tr class="border-t border-zinc-100 dark:border-zinc-700">
                                <td class="py-2 font-medium">{{ $student->name }}</td>
                                <td class="py-2 text-center text-zinc-500">{{ $student->class->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-4 text-center text-emerald-600 font-bold">Semua siswa sudah absen!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>
    </div>
</div>