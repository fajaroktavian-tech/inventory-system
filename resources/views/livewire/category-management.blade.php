<div class="p-6">
    <flux:heading size="xl" level="1">Kelola Kategori Barang</flux:heading>
    <flux:subheading>Daftar kategori untuk pengelompokan barang inventaris.</flux:subheading>

    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari kategori..." class="max-w-xs" />
        <flux:button variant="primary" icon="plus" wire:click="create">Tambah Kategori</flux:button>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="p-3 mb-4 text-sm text-white bg-red-500 rounded-lg">{{ session('error') }}</div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>No</flux:table.column>
            <flux:table.column>Nama Kategori</flux:table.column>
            <flux:table.column>Jumlah Barang</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($categories as $category)
                <flux:table.row :key="$category->id">
                    <flux:table.cell>
                        {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                    </flux:table.cell>
                    <flux:table.cell font="medium">{{ $category->name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="zinc" size="sm">{{ $category->items_count ?? $category->items()->count() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-2">
                            {{-- Tombol Edit dengan Style Manual Amber --}}
                            <flux:button variant="filled" size="sm" icon="pencil-square"
                                wire:click="edit({{ $category->id }})"
                                style="background-color: #f59e0b; border-color: #f59e0b; color: white;">
                                Edit
                            </flux:button>

                            {{-- Tombol Hapus dengan Style Manual Rose --}}
                            <flux:button variant="filled" size="sm" icon="trash" wire:click="delete({{ $category->id }})"
                                wire:confirm="Hapus kategori ini?"
                                style="background-color: #f43f5e; border-color: #f43f5e; color: white;">
                                Hapus
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">{{ $categories->links() }}</div>
    {{-- MODAL --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[400px]">
        <form wire:submit="store" class="space-y-4">
            <flux:heading size="lg">{{ $categoryId ? 'Edit Kategori' : 'Tambah Kategori' }}</flux:heading>
            <flux:input label="Nama Kategori" wire:model="name" />
            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>