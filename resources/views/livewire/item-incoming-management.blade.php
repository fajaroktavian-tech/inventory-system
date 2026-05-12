<div class="p-6">
    <flux:heading size="xl">Barang Masuk (Stok In)</flux:heading>

    <div class="flex flex-wrap justify-between mt-8 mb-4 gap-4">
        <div class="flex flex-wrap items-end gap-x-4 gap-y-4">
            <flux:input type="date" wire:model.live="startDate" label="Dari" size="sm" />
            <flux:input type="date" wire:model.live="endDate" label="Sampai" size="sm" />
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari barang..." size="sm" />
        </div>

        <div class="flex items-end gap-2">
            <flux:button variant="outline" icon="document-text" wire:click="exportExcel" size="sm">Excel</flux:button>
            <flux:button variant="outline" icon="printer" wire:click="exportPDF" size="sm">PDF</flux:button>
            <flux:button variant="primary" icon="plus" wire:click="create" size="sm">Input Barang Masuk</flux:button>
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
</div>