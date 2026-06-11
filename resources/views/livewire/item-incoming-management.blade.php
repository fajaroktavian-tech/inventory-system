<div class="p-6">
    <flux:heading size="xl">Barang Masuk (Stok In)</flux:heading>

    <div class="flex flex-wrap justify-between mt-8 mb-4 gap-4">
        <div class="flex flex-wrap items-end gap-x-4 gap-y-4">
            <flux:input type="date" wire:model.live="startDate" label="Dari" size="sm" />
            <flux:input type="date" wire:model.live="endDate" label="Sampai" size="sm" />
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari barang..." size="sm" />
        </div>

        <div class="flex items-end gap-2">
        <flux:button icon="question-mark-circle" wire:click="$set('isIncomingGuideOpen', true)" variant="ghost" size="sm">Panduan</flux:button>
            <flux:dropdown align="end">
                <flux:button icon-trailing="chevron-down" variant="outline" size="sm">
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
            <flux:button variant="primary" icon="plus" wire:click="create" size="sm">Barang Masuk</flux:button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">{{ session('message') }}</div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Tanggal</flux:table.column>
            <flux:table.column>Nama Barang</flux:table.column>
            <flux:table.column>Jumlah</flux:table.column>
            <flux:table.column>Petugas</flux:table.column>
            <flux:table.column>Keterangan</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($incomings as $incoming)
                <flux:table.row :key="$incoming->id">
                    <flux:table.cell>{{ \Carbon\Carbon::parse($incoming->date)->format('d/m/Y') }}</flux:table.cell>
                    <flux:table.cell font="medium">{{ $incoming->item->name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="green" size="sm">+ {{ $incoming->quantity }} {{ $incoming->item->unit }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $incoming->user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $incoming->description ?? '-' }}</flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">{{ $incomings->links() }}</div>

    {{-- MODAL INPUT --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[450px]">
        <form wire:submit="store" class="space-y-4">
            <flux:heading size="lg">Tambah Stok Barang</flux:heading>

            <flux:input label="Tanggal" type="date" wire:model="date" />

            <div class="relative">
                <flux:label>Cari Barang</flux:label>
                @if($selectedItemName)
                    <div class="flex items-center justify-between p-2 mb-2 bg-blue-50 border border-blue-200 rounded-lg">
                        <span class="text-sm font-medium text-blue-800">Terpilih: {{ $selectedItemName }}</span>
                        <button type="button" wire:click="$set('selectedItemName', '')"
                            class="text-blue-600 hover:text-blue-800">
                            <flux:icon name="x-mark" variant="micro" />
                        </button>
                    </div>
                @endif

                <flux:input wire:model.live.debounce.300ms="search_item" icon="magnifying-glass"
                    placeholder="Ketik nama barang..." autocomplete="off" />

                @if(count($availableItems) > 0)
                    <div
                        class="absolute z-50 w-full bg-white border border-zinc-200 rounded-lg shadow-xl mt-1 overflow-hidden">
                        @foreach($availableItems as $item)
                            <button type="button" wire:click="selectItem({{ $item->id }}, '{{ $item->name }}')"
                                class="w-full text-left p-3 hover:bg-zinc-50 flex justify-between items-center border-b last:border-0">
                                <div>
                                    <span class="font-medium text-zinc-800">{{ $item->name }}</span>
                                    <span class="text-xs text-zinc-500 ml-2">(Stok: {{ $item->stock }})</span>
                                </div>
                                <flux:icon name="plus-circle" class="text-zinc-400 size-5" />
                            </button>
                        @endforeach
                    </div>
                @endif
                <flux:error name="itemId" />
            </div>

            <flux:input label="Jumlah Masuk" type="number" wire:model="quantity" placeholder="0" />

            <flux:textarea label="Keterangan (Opsional)" wire:model="description" />

            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Stok</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal wire:model="isIncomingGuideOpen" class="md:w-[600px]">
        <div class="space-y-6">
            <flux:heading size="lg">Panduan Input Barang Masuk</flux:heading>

            <div class="space-y-4 text-sm text-zinc-600 dark:text-zinc-300">
                <p>Menu ini digunakan untuk mencatat penambahan stok barang ke gudang sekolah. Pastikan setiap barang
                    masuk tercatat agar stok tetap akurat.</p>

                <ul class="list-decimal list-inside space-y-2">
                    <li><strong>Tanggal:</strong> Sesuaikan dengan tanggal fisik barang diterima di sekolah, bukan
                        tanggal saat input ke sistem.</li>
                    <li><strong>Cari Barang:</strong> Ketik nama barang pada kolom pencarian dan klik barang yang sesuai
                        dari daftar yang muncul.</li>
                    <li><strong>Verifikasi Stok:</strong> Perhatikan stok saat ini yang muncul di samping nama barang
                        saat mencari untuk memastikan Anda memilih barang yang benar.</li>
                    <li><strong>Jumlah:</strong> Masukkan jumlah barang yang diterima sesuai dengan nota pengiriman atau
                        surat jalan.</li>
                    <li><strong>Keterangan:</strong> Sangat disarankan mengisi keterangan (misal: "Pengadaan BOS 2026",
                        "Hibah", atau "Sisa Proyek") untuk memudahkan pelacakan di laporan akhir tahun.</li>
                </ul>

                <div
                    class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-100 dark:border-green-800">
                    <flux:heading size="sm" class="text-green-800 dark:text-green-300">Mengapa Harus Tercatat?
                    </flux:heading>
                    <p class="mt-1 text-xs">Setiap input di sini akan secara otomatis memperbarui jumlah stok di
                        <strong>Kelola Data Barang</strong>. Kesalahan input akan berdampak pada selisih aset sekolah.
                    </p>
                </div>
            </div>

            <div class="flex justify-end">
                <flux:button variant="primary" wire:click="$set('isIncomingGuideOpen', false)">Mengerti</flux:button>
            </div>
        </div>
    </flux:modal>
</div>