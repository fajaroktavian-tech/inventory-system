<div class="p-6 max-w-4xl mx-auto space-y-6">

    @if($isHoliday)
        <div class="bg-amber-100 border-l-4 border-amber-500 text-amber-700 p-4 mb-6" role="alert">
            <p class="font-bold">Hari Libur Terdeteksi</p>
            <p>Hari ini ditandai sebagai hari libur di sistem. Fitur input absensi dinonaktifkan.</p>
        </div>
    @else
    

    <div class="flex justify-between items-center">
        <flux:heading size="xl">Piket & Input Manual</flux:heading>
        <flux:button icon="question-mark-circle" wire:click="$set('isPiketGuideOpen', true)" variant="ghost">Panduan
        </flux:button>
    </div>

    {{-- KONTROL INPUT CEPAT --}}
    <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-200">
        <flux:subheading>Cari Siswa untuk Input Absen</flux:subheading>
        <div class="flex gap-2 mt-2">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NIS siswa..."
                icon="magnifying-glass" autofocus />
        </div>
    </div>

    {{-- HASIL PENCARIAN (Bisa dijadikan modal atau list di bawah) --}}
    @if(!empty($search) && $foundStudents->isNotEmpty())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Nama Siswa</flux:table.column>
                <flux:table.column>Kelas</flux:table.column>
                <flux:table.column>Aksi</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach($foundStudents as $student)
                    <flux:table.row>
                        <flux:table.cell>{{ $student->name }}</flux:table.cell>
                        <flux:table.cell>{{ $student->class->name }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button variant="primary" size="sm" wire:click="markAsPresent({{ $student->id }}, 'hadir')">
                                Hadir</flux:button>
                            <flux:button size="sm" wire:click="openPiketModal({{ $student->id }})">Lainnya...</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @elseif(!empty($search))
        <div class="p-4 text-center text-zinc-500">Siswa tidak ditemukan.</div>
    @endif

    @if($latestAttendances->isNotEmpty())
        <div class="mt-8">
            <flux:heading size="md" class="mb-4">Riwayat Input Terakhir (Hari Ini)</flux:heading>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Nama Siswa</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Waktu Input</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($latestAttendances as $att)
                        <flux:table.row>
                            <flux:table.cell>{{ $att->student->name }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ $att->status === 'hadir' ? 'green' : 'red' }}">
                                    {{ strtoupper($att->status) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $att->updated_at->format('H:i:s') }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:button variant="ghost" size="sm" icon="trash" wire:click="undoAttendance({{ $att->id }})"
                                    wire:confirm="Yakin ingin menghapus data ini?" class="text-red-500" />
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    @endif

    <flux:modal wire:model="isPiketModalOpen" class="md:w-[400px]">
        <div class="space-y-4">
            <flux:heading size="lg">Pilih Status Absensi</flux:heading>

            <div class="grid grid-cols-2 gap-2">
                <flux:button wire:click="savePiketStatus('izin')" variant="outline">Izin</flux:button>
                <flux:button wire:click="savePiketStatus('sakit')" variant="outline">Sakit</flux:button>
                <flux:button wire:click="savePiketStatus('alpa')" variant="outline">Alpa</flux:button>
                <flux:button wire:click="savePiketStatus('dispen')" variant="outline">Dispen</flux:button>
            </div>

            <flux:button wire:click="$set('isPiketModalOpen', false)" class="w-full">Batal</flux:button>
        </div>
    </flux:modal>

    {{-- DAFTAR SISWA BELUM ABSEN --}}
    <div class="mt-8 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden" x-data="{ open: false }">

        {{-- Header Accordion --}}
        <button @click="open = !open"
            class="w-full bg-zinc-50 dark:bg-zinc-800 p-4 font-semibold text-rose-600 flex justify-between items-center hover:bg-zinc-100 dark:hover:bg-zinc-700 transition">
            <span>Siswa Belum Absen ({{ $absentStudents->count() }} orang)</span>
            <span x-text="open ? '▼' : '▲'" class="text-xs"></span>
        </button>

        {{-- Konten --}}
        <div x-show="open" class="border-t border-zinc-200 dark:border-zinc-700">
            <flux:table>
                <flux:table.rows>
                    @foreach($absentStudents as $student)
                        <flux:table.row>
                            <flux:table.cell>{{ $student->name }}</flux:table.cell>
                            <flux:table.cell>{{ $student->class->name }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-1">
                                    {{-- Tombol Hadir Langsung --}}
                                    <flux:button size="xs" variant="primary"
                                        wire:click="markAsPresent({{ $student->id }}, 'hadir')">
                                        Hadir
                                    </flux:button>

                                    {{-- Tombol Opsi Lainnya --}}
                                    <flux:button size="xs" variant="ghost" wire:click="openPiketModal({{ $student->id }})">
                                        Opsi...
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
    <flux:modal wire:model="isPiketGuideOpen" class="md:w-[600px]">
        <div class="space-y-6">
            <flux:heading size="lg">Panduan Petugas Piket</flux:heading>

            <div class="space-y-4 text-sm text-zinc-600 dark:text-zinc-300">
                <p>Fitur ini dirancang untuk pencatatan absensi cepat oleh petugas piket di gerbang sekolah.</p>

                <ul class="list-decimal list-inside space-y-2">
                    <li><strong>Cari Siswa:</strong> Ketik nama atau NIS di kolom pencarian. Hasil akan muncul secara
                        instan.</li>
                    <li><strong>Input Cepat:</strong> Klik tombol <strong>Hadir</strong> untuk mencatat kehadiran siswa
                        dengan satu klik.</li>
                    <li><strong>Opsi Lain:</strong> Jika siswa Izin, Sakit, Alpa, atau Dispen, gunakan tombol
                        <strong>Lainnya...</strong> untuk memilih status tersebut.
                    </li>
                    <li><strong>Riwayat Hari Ini:</strong> Anda bisa memantau siapa saja yang baru saja diinput pada
                        tabel di bawah kolom pencarian.</li>
                    <li><strong>Hapus Kesalahan:</strong> Jika salah input, gunakan ikon <strong>Tempat Sampah</strong>
                        pada tabel riwayat untuk membatalkan data tersebut.</li>
                    <li><strong>Siswa Belum Absen:</strong> Klik baris berwarna merah untuk melihat daftar siswa yang
                        belum melakukan absensi hari ini.</li>
                </ul>

                <div
                    class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-100 dark:border-amber-800">
                    <flux:heading size="sm" class="text-amber-800 dark:text-amber-300">Catatan Penting:</flux:heading>
                    <p class="mt-1 text-xs">Pastikan data yang diinput sudah benar. Data yang dihapus tidak dapat
                        dikembalikan secara otomatis.</p>
                </div>
            </div>

            <div class="flex justify-end">
                <flux:button variant="primary" wire:click="$set('isPiketGuideOpen', false)">Mengerti</flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>