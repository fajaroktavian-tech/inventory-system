<div class="p-6">
    <flux:heading size="xl">Rekap Absensi Kelas</flux:heading>

    <div class="flex gap-4 my-6 items-end">
        <flux:input type="date" label="Dari" wire:model.live="startDate" />
        <flux:input type="date" label="Sampai" wire:model.live="endDate" />
        <flux:button wire:click="exportExcel" icon="arrow-down-tray" variant="filled">
            Export Excel
        </flux:button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <p class="text-sm text-zinc-500">Total Siswa</p>
            <p class="text-2xl font-bold">{{ $totalSiswa }}</p>
        </div>
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <p class="text-sm text-zinc-500">Persentase Kehadiran</p>
            <p class="text-2xl font-bold text-green-600">{{ $persentase }}%</p>
        </div>
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <p class="text-sm text-zinc-500">Rentang Laporan</p>
            <p class="text-sm font-semibold">{{ $startDate }} s/d {{ $endDate }}</p>
        </div>
    </div>

    <flux:input icon="magnifying-glass" placeholder="Cari siswa..." wire:model.live.debounce.300ms="search" />

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Nama Siswa</flux:table.column>
            <flux:table.column>Hadir</flux:table.column>
            <flux:table.column>Sakit</flux:table.column>
            <flux:table.column>Izin</flux:table.column>
            <flux:table.column>Alpha</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach($students as $student)
                <flux:table.row>
                    <flux:table.cell>{{ $student->name }}</flux:table.cell>
                    <flux:table.cell>{{ $student->hadir_count }}</flux:table.cell>
                    <flux:table.cell>{{ $student->attendances->where('status', 'sakit')->count() }}</flux:table.cell>
                    <flux:table.cell>{{ $student->attendances->where('status', 'izin')->count() }}</flux:table.cell>
                    <flux:table.cell
                        class="font-bold {{ $student->alpa_count > 3 ? 'text-red-600 bg-red-50' : 'text-zinc-900' }}">
                        {{ $student->alpa_count }}
                        @if($student->alpa_count > 3)
                            <flux:badge color="red" size="sm" class="ml-2">Perhatian</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                <flux:button size="sm" variant="ghost" icon="eye" wire:click="showDetail({{ $student->id }})">
                    Detail
                </flux:button>
            </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
    <flux:modal wire:model="isDetailModalOpen" class="md:w-[600px]">
    @if($selectedStudent)
        <div class="space-y-4">
            <flux:heading size="lg">Riwayat Absensi: {{ $selectedStudent->name }}</flux:heading>
            <flux:button wire:click="exportDetailPdf({{ $selectedStudent->id }})" 
                             icon="document-arrow-down" 
                             variant="outline" 
                             size="sm">
                    PDF
                </flux:button>
            
            <div class="max-h-[400px] overflow-y-auto border rounded-lg">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Tanggal</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>Jam</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($selectedStudent->attendances as $att)
                            <flux:table.row>
                                <flux:table.cell>{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="{{ $att->status == 'hadir' ? 'green' : ($att->status == 'alpa' ? 'red' : 'orange') }}">
                                        {{ strtoupper($att->status) }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>{{ $att->time_in ?? '-' }}</flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="3" class="text-center text-zinc-500">Tidak ada data kehadiran.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
            
            <div class="flex justify-end">
                <flux:button wire:click="$set('isDetailModalOpen', false)">Tutup</flux:button>
            </div>
        </div>
    @endif
</flux:modal>
</div>