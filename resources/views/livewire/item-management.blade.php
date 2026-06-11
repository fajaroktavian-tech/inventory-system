<div class="p-6">
    <flux:heading size="xl" level="1">Kelola Data Barang</flux:heading>
    <flux:subheading>Daftar inventaris barang SMKN 7 Baleendah.</flux:subheading>

    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari barang atau kategori..."
            class="max-w-xs" />

        <div class="flex gap-2">
            {{-- Dropdown Export --}}
            <flux:button icon="question-mark-circle" wire:click="$set('isItemsGuideOpen', true)" variant="ghost">
                    Panduan
                </flux:button>
            <flux:dropdown align="end">
                
                <flux:button icon-trailing="chevron-down" variant="outline">
                    Export Data
                </flux:button>
                <flux:menu>
                    <flux:menu.item icon="document-text" wire:click="exportExcel">
                        Export ke Excel
                    </flux:menu.item>
                    <flux:menu.item icon="printer" wire:click="exportPDF">
                        Export ke PDF
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>

            <flux:button variant="primary" icon="plus" wire:click="create">Tambah Barang</flux:button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <flux:table>
        <flux:table.columns>
            {{-- Tambah Kolom No --}}
            <flux:table.column>No</flux:table.column>
            <flux:table.column>Nama Barang</flux:table.column>
            <flux:table.column>Kategori</flux:table.column>
            <flux:table.column>Stok</flux:table.column>
            <flux:table.column>Min. Stok</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($items as $index => $item)
                <flux:table.row :key="$item->id">
                    {{-- Rumus No Urut Pagination: (HalamanSekarang - 1) * PerHalaman + Iterasi + 1 --}}
                    <flux:table.cell>
                        {{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}
                    </flux:table.cell>

                    <flux:table.cell font="medium">{{ $item->name }}</flux:table.cell>
                    <flux:table.cell>{{ $item->category->name ?? 'Tanpa Kategori' }}</flux:table.cell>
                    <flux:table.cell>
                        @php
                            // Logika: Jika stok <= min_stock, berikan warna merah/peringatan
                            $isCritical = $item->stock <= $item->min_stock;
                        @endphp

                        <span
                            class="{{ $isCritical ? 'font-bold text-red-600 dark:text-red-400' : 'text-zinc-700 dark:text-zinc-300' }}">
                            {{ $item->stock }} {{ $item->unit }}
                        </span>
                    </flux:table.cell>
                    <flux:table.cell>{{ $item->min_stock }}</flux:table.cell>

                    <flux:table.cell>
                        <div class="flex gap-2">
                            {{-- Edit - Menggunakan inline style untuk warna Amber --}}
                            <flux:button variant="filled" size="sm" icon="pencil-square" wire:click="edit({{ $item->id }})"
                                style="background-color: #f59e0b; border-color: #f59e0b; color: white;">
                                Edit
                            </flux:button>

                            {{-- Hapus - Menggunakan inline style untuk warna Rose --}}
                            <flux:button variant="filled" size="sm" icon="trash" wire:click="delete({{ $item->id }})"
                                wire:confirm="Hapus?"
                                style="background-color: #f43f5e; border-color: #f43f5e; color: white;">
                                Hapus
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        {{ $items->links() }}
    </div>

    {{-- MODAL tetap sama seperti sebelumnya --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[450px]">
        <form wire:submit="store" class="space-y-4">
            <flux:heading size="lg">{{ $itemId ? 'Edit Identitas Barang' : 'Daftarkan Barang Baru' }}</flux:heading>

            <flux:input label="Nama Barang" wire:model="name" placeholder="Contoh: Kertas A4" />

            <flux:select label="Kategori" wire:model="category_id" placeholder="Pilih Kategori...">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-2 gap-4">
                <flux:input label="Satuan" wire:model="unit" placeholder="Pcs, Rim, dll" />
                {{-- Input Stok Dihapus/Disabled di sini --}}
                <flux:input label="Stok Saat Ini" type="number" wire:model="stock" disabled
                    help="Tambah stok melalui menu Barang Masuk" />
            </div>

            <flux:input label="Minimal Stok" type="number" wire:model="min_stock" />

            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal wire:model="isItemsGuideOpen" class="md:w-[600px]">
        <div class="space-y-6">
            <flux:heading size="lg">Panduan Kelola Inventaris</flux:heading>

            <div class="space-y-4 text-sm text-zinc-600 dark:text-zinc-300">
                <p>Halaman ini digunakan untuk mengelola data master barang. Harap perhatikan hal-hal berikut:</p>

                <ul class="list-decimal list-inside space-y-2">
                    <li><strong>Tambah Barang:</strong> Klik "Tambah Barang" untuk mendaftarkan barang baru ke sistem.
                    </li>
                    <li><strong>Edit Barang:</strong> Gunakan tombol Edit (kuning) untuk memperbarui nama, kategori,
                        atau batas stok minimal.</li>
                    <li><strong>Aturan Stok:</strong> Anda <strong>tidak bisa</strong> menambah stok langsung di sini.
                        Stok akan terupdate otomatis saat Anda melakukan transaksi di menu <strong>Barang
                            Masuk</strong>.</li>
                    <li><strong>Minimal Stok:</strong> Isi angka ini sebagai pengingat sistem. Jika stok saat ini di
                        bawah angka ini, sistem akan memberikan notifikasi agar Anda segera melakukan pengadaan.</li>
                    <li><strong>Hapus Barang:</strong> Hapus barang hanya jika barang tersebut sudah tidak ada atau
                        tidak digunakan lagi di lingkungan sekolah.</li>
                </ul>

                <div
                    class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-100 dark:border-yellow-800">
                    <flux:heading size="sm" class="text-yellow-800 dark:text-yellow-300">Penting:</flux:heading>
                    <p class="mt-1 text-xs">Untuk menambah jumlah fisik barang yang ada di gudang, silakan kunjungi menu
                        <strong>Barang Masuk</strong> agar data inventaris tetap akurat dan tercatat dalam laporan.
                    </p>
                </div>
            </div>

            <div class="flex justify-end">
                <flux:button variant="primary" wire:click="$set('isItemsGuideOpen', false)">Mengerti</flux:button>
            </div>
        </div>
    </flux:modal>
</div>