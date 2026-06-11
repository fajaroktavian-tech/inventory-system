<div class="p-6">
    <div class="flex items-center gap-3 mb-1">
        <flux:icon name="home-modern" variant="outline" class="text-zinc-500" />
        <flux:heading size="xl" level="1">Kelola Data Ruangan</flux:heading>
    </div>
    <flux:subheading>Daftar ruangan atau lokasi penyimpanan aset di lingkungan sekolah.</flux:subheading>

    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari ruangan..." class="max-w-xs" />
        <div class="flex gap-2">
            <flux:button icon="question-mark-circle" wire:click="$set('isRoomGuideOpen', true)" variant="ghost">Panduan
            </flux:button>
            <flux:button variant="primary" icon="plus" wire:click="create">Tambah Ruangan</flux:button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg shadow-sm flex items-center gap-2">
            <flux:icon name="check-circle" variant="mini" />
            {{ session('message') }}
        </div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column width="50px">No</flux:table.column>
            <flux:table.column>Nama Ruangan / Lokasi</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($rooms as $room)
                <flux:table.row :key="$room->id">
                    <flux:table.cell>
                        <span
                            class="text-zinc-500">{{ ($rooms->currentPage() - 1) * $rooms->perPage() + $loop->iteration }}</span>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                <flux:icon name="map-pin" variant="micro" class="text-zinc-500" />
                            </div>
                            <span class="font-semibold text-zinc-800 dark:text-white">{{ $room->name }}</span>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex justify gap-2">
                            <flux:button variant="filled" size="sm" icon="pencil-square" wire:click="edit({{ $room->id }})"
                                style="background-color: #f59e0b; color: white; border: none;">
                            </flux:button>

                            <flux:button variant="filled" size="sm" icon="trash" wire:click="delete({{ $room->id }})"
                                wire:confirm="Hapus ruangan ini? Semua data kaitan mungkin terpengaruh."
                                style="background-color: #f43f5e; color: white; border: none;">
                            </flux:button>

                            <flux:button variant="ghost" size="sm" icon="eye" wire:click="showAssets({{ $room->id }})">
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="3" class="text-center py-10">
                        <div class="flex flex-col items-center">
                            <flux:icon name="magnifying-glass" class="mb-2 text-zinc-300" size="xl" />
                            <p class="text-zinc-500">Tidak ada data ruangan ditemukan.</p>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        {{ $rooms->links() }}
    </div>

    {{-- MODAL FORM RUANGAN --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[400px]">
        <form wire:submit="store" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $roomId ? 'Update Ruangan' : 'Tambah Ruangan Baru' }}</flux:heading>
                <flux:subheading>Pastikan nama ruangan spesifik (misal: Lab RPL 1).</flux:subheading>
            </div>

            <flux:input label="Nama Ruangan" wire:model="name" placeholder="Contoh: Lab Komputer 1, TU, dll"
                icon="home-modern" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Ruangan</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="room-assets-modal" class="md:w-[800px]">
        @if($selectedRoom)
            <div class="space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <flux:heading size="lg">Inventaris: {{ $selectedRoom->name }}</flux:heading>
                        <flux:subheading>Daftar seluruh unit aset yang berada di lokasi ini.</flux:subheading>
                    </div>
                </div>

                {{-- Ringkasan Kondisi di Ruangan --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-3 border rounded-lg bg-zinc-50 dark:bg-zinc-900">
                        <p class="text-xs text-zinc-500">Total Unit</p>
                        <p class="text-xl font-bold">{{ $selectedRoom->assets->count() }}</p>
                    </div>
                    <div class="p-3 border rounded-lg border-green-200 bg-green-50 dark:bg-green-950/20">
                        <p class="text-xs text-green-600">Kondisi Baik</p>
                        <p class="text-xl font-bold text-green-700">
                            {{ $selectedRoom->assets->where('condition', 'baik')->count() }}
                        </p>
                    </div>
                    <div class="p-3 border rounded-lg border-orange-200 bg-orange-50 dark:bg-orange-950/20">
                        <p class="text-xs text-orange-600">Rusak/Maintenance</p>
                        <p class="text-xl font-bold text-orange-700">
                            {{ $selectedRoom->assets->where('condition', '!=', 'baik')->count() }}
                        </p>
                    </div>
                    <div class="p-3 border rounded-lg border-blue-200 bg-blue-50 dark:bg-blue-950/20">
                        <p class="text-xs text-blue-600 italic">Nilai Total Aset</p>
                        <p class="text-lg font-black text-blue-800 dark:text-blue-400">
                            Rp {{ number_format($totalValue, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Tabel Daftar Barang --}}
                <div class="max-h-[400px] overflow-y-auto">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Nama Barang</flux:table.column>
                            <flux:table.column>No. Seri (SN)</flux:table.column>
                            <flux:table.column>PIC</flux:table.column>
                            <flux:table.column>Kondisi</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse($selectedRoom->assets as $asset)
                                <flux:table.row>
                                    <flux:table.cell class="font-medium text-zinc-800 dark:text-white">
                                        {{ $asset->itemInfo->name }}
                                    </flux:table.cell>
                                    <flux:table.cell class="text-xs">
                                        {{ $asset->serial_number ?? '-' }}
                                    </flux:table.cell>
                                    <flux:table.cell class="text-xs">
                                        {{ $asset->pic->name ?? '-' }}
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge size="sm" color="{{ $asset->condition == 'baik' ? 'green' : 'orange' }}"
                                            inset="top bottom">
                                            {{ strtoupper(str_replace('_', ' ', $asset->condition)) }}
                                        </flux:badge>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="4" class="text-center py-4 text-zinc-500 italic">
                                        Belum ada aset di ruangan ini.
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>

                <div class="flex mt-6">
                    <flux:spacer />
                    <flux:button x-on:click="$flux.modal('room-assets-modal').close()">Tutup</flux:button>

                    <flux:button icon="printer" variant="filled" color="zinc" wire:click="printDir">
                        Cetak DIR (A4)
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    <flux:modal wire:model="isRoomGuideOpen" class="md:w-[600px]">
        <div class="space-y-6">
            <flux:heading size="lg">Panduan Kelola Ruangan</flux:heading>

            <div class="space-y-4 text-sm text-zinc-600 dark:text-zinc-300">
                <p>Halaman ini berfungsi untuk mendata lokasi fisik aset di lingkungan sekolah.</p>

                <ul class="list-decimal list-inside space-y-2">
                    <li><strong>Naming Convention:</strong> Gunakan nama yang spesifik dan mudah dikenali (Contoh: "Lab
                        RPL 1" atau "TU - Lantai 2") agar memudahkan saat proses mutasi barang.</li>
                    <li><strong>Melihat Isi Ruangan:</strong> Klik ikon <strong>Mata (Eye)</strong> pada baris ruangan
                        untuk melihat detail daftar barang yang tersimpan di ruangan tersebut.</li>
                    <li><strong>Statistik Ruangan:</strong> Di dalam modal detail, Anda bisa memantau kondisi barang
                        (Baik/Rusak) dan total nilai aset secara real-time.</li>
                    <li><strong>Dokumentasi (DIR):</strong> Anda dapat menggunakan tombol <strong>Cetak DIR</strong>
                        untuk mendapatkan Daftar Inventaris Ruangan (DIR) dalam format A4, yang berguna untuk penempelan
                        daftar barang di pintu ruangan.</li>
                    <li><strong>Data Kaitan:</strong> Berhati-hatilah saat menghapus ruangan; pastikan aset yang ada di
                        dalamnya sudah dipindahkan ke lokasi lain agar data tidak hilang.</li>
                </ul>

                <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200">
                    <flux:heading size="sm" class="text-zinc-800 dark:text-zinc-100">Tips Operasional:</flux:heading>
                    <p class="mt-1 text-xs">Selalu perbarui daftar ini jika sekolah melakukan penambahan ruangan baru
                        atau perubahan fungsi ruangan agar pelaporan aset tetap akurat.</p>
                </div>
            </div>

            <div class="flex justify-end">
                <flux:button variant="primary" wire:click="$set('isRoomGuideOpen', false)">Mengerti</flux:button>
            </div>
        </div>
    </flux:modal>
</div>