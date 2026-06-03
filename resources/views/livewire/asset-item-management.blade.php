<div class="p-6">
    <flux:heading size="xl">Master Katalog Aset</flux:heading>
    <flux:subheading>Daftar template barang aset sebelum registrasi unit fisik.</flux:subheading>

    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari nama atau merk..."
            class="max-w-xs" />
        <flux:button variant="primary" icon="plus" wire:click="create">Tambah Katalog</flux:button>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">{{ session('message') }}</div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Nama Barang</flux:table.column>
            <flux:table.column>Kategori</flux:table.column>
            <flux:table.column>Merk</flux:table.column>
            <flux:table.column>Spesifikasi</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($items as $item)
                <flux:table.row :key="$item->id">
                    <flux:table.cell class="font-medium">{{ $item->name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm">{{ $item->category->name }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $item->brand ?? '-' }}</flux:table.cell>
                    <flux:table.cell class="text-xs text-zinc-500">{{ Str::limit($item->specification, 50) }}</flux:cell>
                        <flux:table.cell>
                            <div class="flex justify gap-2">
                                <flux:button variant="filled" size="sm" icon="pencil-square"
                                    wire:click="edit({{ $item->id }})" style="background-color: #f59e0b; color: white;">
                                </flux:button>
                                <flux:button variant="filled" size="sm" icon="trash" wire:click="delete({{ $item->id }})"
                                    wire:confirm="Hapus katalog ini?" style="background-color: #f43f5e; color: white;">
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

    {{-- MODAL FORM --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[500px]">
        <form wire:submit="store" class="space-y-4">
            <flux:heading size="lg">{{ $itemId ? 'Edit Katalog' : 'Tambah Katalog Baru' }}</flux:heading>

            <flux:input label="Nama Barang" wire:model="name" placeholder="Misal: Laptop ASUS Expertbook" />

            <div class="relative">
                <flux:label>Kategori Barang</flux:label>

                @if(!$selectedCategoryName)
                    {{-- Tampilan saat belum memilih kategori --}}
                    <flux:input wire:model.live.debounce.300ms="search_category" icon="magnifying-glass"
                        placeholder="Ketik nama kategori (Misal: Elektronik)..." />

                    @if(count($filteredCategories) > 0)
                        <div
                            class="absolute z-50 w-full bg-white border border-zinc-200 rounded-xl shadow-xl mt-1 max-h-48 overflow-y-auto">
                            @foreach($filteredCategories as $cat)
                                <button type="button" wire:click="selectCategory({{ $cat->id }}, '{{ $cat->name }}')"
                                    class="w-full text-left p-3 hover:bg-zinc-50 border-b border-zinc-50 last:border-0 text-sm">
                                    {{ $cat->name }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    {{-- Tampilan saat kategori sudah terpilih --}}
                    <div class="flex items-center justify-between p-3 bg-zinc-50 border border-zinc-200 rounded-lg">
                        <div class="flex items-center gap-2">
                            <flux:icon name="tag" variant="mini" class="text-zinc-400" />
                            <span class="text-sm font-medium">{{ $selectedCategoryName }}</span>
                        </div>
                        <button type="button" wire:click="$set('selectedCategoryName', null); $set('category_id', null)"
                            class="text-zinc-400 hover:text-red-500 transition-colors">
                            <flux:icon name="x-mark" variant="mini" />
                        </button>
                    </div>
                @endif

                @error('category_id')
                    <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <flux:input label="Merk" wire:model="brand" placeholder="Misal: ASUS / IKEA / Epson" />

            <flux:textarea label="Spesifikasi Umum" wire:model="specification"
                placeholder="Detail teknis umum barang..." />

            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Katalog</flux:button>
            </div>
        </form>
    </flux:modal>
</div>