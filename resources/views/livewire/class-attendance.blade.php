<div class="p-2">
    <flux:heading size="xl">Absensi Kelas: {{ $class->name ?? 'Anda bukan Wali Kelas' }}</flux:heading>

    {{-- STATISTIK MINI (Inline) --}}
    <div class="grid grid-cols-2 md:grid-cols-6 gap-2">
        <flux:card class="p-3 text-center">
            <div class="text-xs text-zinc-500 uppercase">Total</div>
            <div class="text-xl font-bold">{{ $totalSiswa }}</div>
        </flux:card>
        <flux:card class="p-3 text-center border-green-200 bg-green-50">
            <div class="text-xs text-green-700 uppercase">Hadir</div>
            <div class="text-xl font-bold text-green-600">{{ $stats['hadir'] + $stats['dispen'] }}</div>
        </flux:card>
        <flux:card class="p-3 text-center">
            <div class="text-xs text-yellow-700 uppercase">Izin</div>
            <div class="text-xl font-bold text-yellow-600">{{ $stats['izin'] }}</div>
        </flux:card>
        <flux:card class="p-3 text-center">
            <div class="text-xs text-blue-700 uppercase">Sakit</div>
            <div class="text-xl font-bold text-blue-600">{{ $stats['sakit'] }}</div>
        </flux:card>
        <flux:card class="p-3 text-center border-red-200 bg-red-50">
            <div class="text-xs text-red-700 uppercase">Alpa</div>
            <div class="text-xl font-bold text-red-600">{{ $stats['alpa'] }}</div>
        </flux:card>
        <flux:card class="p-3 flex flex-col justify-center items-center">
            <div class="text-xs text-zinc-500 uppercase">Persentase</div>
            <div class="text-lg font-bold">{{ number_format($persentase, 0) }}%</div>
            <div class="w-full mt-1">
                <flux:progress :value="$persentase" size="sm" />
            </div>
        </flux:card>
    </div>

    {{-- KONTROL FILTER & EKSPOR (Satu Baris Padat) --}}
    <div
        class="flex gap-2 items-center bg-zinc-50 dark:bg-zinc-800 p-2 rounded-lg border border-zinc-200 dark:border-zinc-700">
        <flux:input type="date" wire:model.live="selectedDate" class="max-w-[160px]" />

        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari siswa..." icon="magnifying-glass"
            class="max-w-[200px]" />

        <flux:select wire:model.live="statusFilter" class="max-w-[160px]" placeholder="Status...">
            <option value="">Semua Status</option>
            <option value="hadir">Hadir</option>
            <option value="alpa">Alpa</option>
            <option value="izin">Izin</option>
            <option value="sakit">Sakit</option>
        </flux:select>

        <flux:spacer />

        <div class="flex gap-2 items-center bg-zinc-50 dark:bg-zinc-800 p-2 rounded-lg ...">
            <flux:spacer />

            {{-- Tombol Bantuan --}}
            <flux:button icon="question-mark-circle" wire:click="$set('isGuideModalOpen', true)">Panduan</flux:button>
            
            <flux:dropdown align="end">
            <flux:button icon-trailing="chevron-down" variant="outline">Ekspor Data</flux:button>
            <flux:menu>
                <flux:menu.item icon="document-text" wire:click="exportPdf">Ekspor ke PDF</flux:menu.item>
                <flux:menu.item icon="table-cells" wire:click="exportExcel">Ekspor ke Excel</flux:menu.item>
            </flux:menu>
        </flux:dropdown>


        </div>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Nama Siswa</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Jam Masuk</flux:table.column>
            <flux:table.column>Catatan</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($students as $student)
                @php $attendance = $student->attendances->first(); @endphp
                <flux:table.row>
                    <flux:table.cell>{{ $student->name }}</flux:table.cell>
                    <flux:table.cell>
                        @php 
                            $isWeekend = \Carbon\Carbon::parse($selectedDate)->isWeekend();
                        @endphp
                        @if($attendance)
                            <flux:badge :color="$attendance->status === 'hadir' ? 'green' : ($attendance->status === 'alpa' ? 'red' : 'orange')">
                                {{ strtoupper($attendance->status) }}
                            </flux:badge>
                        @elseif($isHoliday)
                            <span class="text-xs text-zinc-400 italic">Libur</span>
                        @else
                            <flux:badge color="red">ALPA</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $attendance->time_in ?? '-' }}</flux:table.cell>
                    <flux:table.cell>{{ $attendance->note ?? '-' }}</flux:table.cell>
                <flux:table.cell>
                    @if($attendance)
                        <flux:button variant="ghost" size="sm" icon="pencil-square"
                        wire:click="openEditModal({{ $attendance->id }})" />
                    @else
                        <flux:button variant="ghost" size="sm" icon="plus"
                            wire:click="createAttendance({{ $student->id }})" />
                    @endif
                        </flux:table.cell>
                    </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    {{-- MODAL EDIT --}}
    <flux:modal wire:model="isEditModalOpen" class="md:w-[400px]">
        <form wire:submit="saveStatus" class="space-y-4">
            <flux:heading size="lg">Ubah Status Absensi</flux:heading>

            <flux:select label="Pilih Status" wire:model="newStatus">
                <option value="hadir">Hadir</option>
                <option value="terlambat">Terlambat</option>
                <option value="izin">Izin</option>
                <option value="sakit">Sakit</option>
                <option value="alpa">Alpa</option>
            </flux:select>

            <flux:input label="Catatan / Alasan" wire:model="newNote" placeholder="Masukkan alasan..." />

            <div class="flex gap-2 justify-end mt-4">
                <flux:button wire:click="$set('isEditModalOpen', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal wire:model="isGuideModalOpen" class="md:w-[600px]">
    <div class="space-y-6">
        <flux:heading size="lg">Panduan Absensi Kelas</flux:heading>
        
        <div class="space-y-4 text-sm text-zinc-600 dark:text-zinc-300">
            <p>Berikut adalah cara mengelola absensi siswa di kelas Anda:</p>
            
            <ul class="list-decimal list-inside space-y-2">
                <li><strong>Melihat Data:</strong> Gunakan filter tanggal dan status di bagian atas untuk memantau kehadiran harian.</li>
                <li><strong>Input Absensi:</strong> Jika siswa belum tercatat (tombol +), klik ikon tersebut untuk memberikan status kehadiran manual.</li>
                <li><strong>Mengubah Status:</strong> Klik ikon pensil untuk mengubah status (misalnya dari Hadir menjadi Izin/Sakit) jika ada perubahan data.</li>
                <li><strong>Catatan:</strong> Selalu tambahkan catatan atau alasan jika siswa berstatus Izin atau Sakit agar data lebih akurat.</li>
                <li><strong>Ekspor:</strong> Anda dapat mengunduh laporan bulanan atau harian melalui tombol <em>Ekspor Data</em> dalam format PDF atau Excel.</li>
            </ul>

            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                <flux:heading size="sm" class="text-blue-800 dark:text-blue-300">Tips Cepat:</flux:heading>
                <p class="mt-1 text-xs">Pastikan tanggal yang dipilih sudah sesuai sebelum melakukan perubahan status untuk menghindari kesalahan data di hari lain.</p>
            </div>
        </div>

        <div class="flex justify-end">
            <flux:button variant="primary" wire:click="$set('isGuideModalOpen', false)">Mengerti</flux:button>
        </div>
    </div>
</flux:modal>
</div>