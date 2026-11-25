<div class="py-6 max-w-7xl mx-auto px-4 lg:px-8 space-y-6">
    <!-- Header Halaman -->
    <div
        class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700 pb-6">
        <div>
            <flux:heading size="xl">Detail Kehadiran Kelas: {{ $class->name }}</flux:heading>
            <flux:subheading>Wali Kelas: {{ $class->waliKelas->name ?? '-' }} | Prodi: {{ $class->prodi->name ?? '-' }}
            </flux:subheading>
            <flux:subheading class="mt-1 font-medium text-indigo-600 dark:text-indigo-400">Tanggal:
                {{ Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
            </flux:subheading>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama siswa..." icon="magnifying-glass"
                class="flex-grow md:w-72" />
            <flux:button wire:click="export" variant="primary" icon="arrow-down-tray"
                class="bg-emerald-600 hover:bg-emerald-700 text-white">
                Export Excel
            </flux:button>
        </div>
    </div>
    <!-- Tabel Detail Siswa -->
    <flux:card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                        <th class="py-3 px-6">No</th>
                        <th class="py-3 px-6">Nama Siswa</th>
                        <th class="py-3 px-6 text-center">Jam Masuk</th>
                        <th class="py-3 px-6 text-center">Jam Pulang</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6">Catatan</th>
                        <th class="py-3 px-6 w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 text-sm text-zinc-800 dark:text-zinc-200">
                    {{-- Ganti $student dengan $item agar tidak rancu --}}
                    @forelse($studentsWithAttendance as $index => $item)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                            <td class="py-4 px-6 text-zinc-500">{{ $index + 1 }}</td>

                            {{-- Akses nama: $item['name'] --}}
                            <td class="py-4 px-6 font-medium">{{ $item['name'] }}</td>

                            <td class="py-4 px-6 text-center font-mono text-emerald-600 dark:text-emerald-400">
                                {{-- Akses time_in: $item['time_in'] --}}
                                {{ $item['time_in'] ? \Carbon\Carbon::parse($item['time_in'])->format('H:i') : '-' }}
                            </td>
                            <td class="py-4 px-6 text-center font-mono text-amber-600 dark:text-amber-400">
                                {{-- Akses time_out: $item['time_out'] --}}
                                {{ $item['time_out'] ? \Carbon\Carbon::parse($item['time_out'])->format('H:i') : '-' }}
                            </td>

                            <td class="py-4 px-6 text-center">
                                @php
                                    // Akses status: $item['attendance_status']
                                    $statusConfig = match ($item['attendance_status']) {
                                        'hadir' => ['hadir', 'emerald'],
                                        'terlambat' => ['terlambat', 'amber'],
                                        'sakit' => ['sakit', 'blue'],
                                        'izin' => ['izin', 'indigo'],
                                        'dispen' => ['dispen', 'purple'],
                                        'alpa' => ['alpa', 'rose'],
                                        default => ['alpa', 'rose'],
                                    };
                                    [$label, $color] = $statusConfig;
                                @endphp
                                <flux:badge size="sm" :color="$color" class="uppercase tracking-wide font-bold">
                                    {{ $label }}
                                </flux:badge>
                            </td>

                            <td class="py-4 px-6 text-zinc-600 dark:text-zinc-400 italic text-xs">
                                {{-- --- AKSES CATATAN DENGAN BENAR DI SINI --- --}}
                                {{ $item['note'] ?: '-' }}
                            </td>

                            @if(in_array(auth()->user()->role, ['admin', 'kesiswaan']))
                                <td class="py-4 px-6 text-right">
                                    <flux:button {{-- Akses id: $item['id'] --}} wire:click="openEditModal({{ $item['id'] }})"
                                        variant="subtle" size="sm" icon="pencil">
                                        Edit
                                    </flux:button>
                                </td>
                            @else
                                <td class="py-4 px-6"></td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-zinc-500">
                                Tidak ada data siswa yang ditemukan di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>
    <flux:modal name="attendance-edit-modal" class="md:w-xl space-y-6" wire:model="isEditModalOpen">
        <div>
            <flux:heading size="lg">Ubah Status Absensi</flux:heading>
            <flux:subheading>Siswa: <span
                    class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $selectedStudentName }}</span>
            </flux:subheading>
            <flux:subheading>Tanggal: <span
                    class="font-medium text-indigo-600 dark:text-indigo-400">{{ Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</span>
            </flux:subheading>
        </div>

        {{-- FORM DI DALAM MODAL --}}
        <div class="space-y-4 py-2">
            <flux:field>
                <flux:label>Status Kehadiran</flux:label>
                <flux:select wire:model="currentStatus" placeholder="Pilih status...">
                    <option value="hadir">Hadir</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="sakit">Sakit</option>
                    <option value="izin">Izin</option>
                    <option value="dispen">Dispen</option>
                    <option value="alpa">Alpa</option>
                </flux:select>
                <flux:error name="currentStatus" />
            </flux:field>

            {{-- Tambahkan catatan/keterangan opsional jika diperlukan (perlu tambah kolom di model Attendance) --}}
            <flux:field>
                <flux:label>Catatan (Opsional)</flux:label>
                <flux:textarea wire:model="note" rows="3" placeholder="Masukkan keterangan izin/sakit..." />
            </flux:field>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
            <flux:modal.close>
                <flux:button variant="subtle">Batal</flux:button>
            </flux:modal.close>

            <flux:button wire:click="updateAttendance" variant="primary"
                class="bg-emerald-600 hover:bg-emerald-700 text-white" wire:loading.attr="disabled">
                Simpan Perubahan
            </flux:button>
        </div>
    </flux:modal>
</div>