<div class="p-6">
    <flux:heading size="xl">Kelola Program Keahlian (Prodi)</flux:heading>
    
    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari prodi atau alias..." class="max-w-xs" />
        <flux:button variant="primary" icon="plus" wire:click="create">Tambah Prodi</flux:button>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">{{ session('message') }}</div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>No</flux:table.column>
            <flux:table.column>Nama Program Keahlian</flux:table.column>
            <flux:table.column>Alias / Singkatan</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($prodis as $prodi)
            <flux:table.row :key="$prodi->id">
                <flux:table.cell>{{ ($prodis->currentPage() - 1) * $prodis->perPage() + $loop->iteration }}</flux:table.cell>
                <flux:table.cell font="medium">{{ $prodi->name }}</flux:table.cell>
                <flux:table.cell>
                    <flux:badge size="sm" color="zinc">{{ $prodi->alias }}</flux:badge>
                </flux:table.cell>
                <flux:table.cell>
                    <div class="flex gap-2">
                        <flux:button variant="filled" size="sm" icon="pencil-square" wire:click="edit({{ $prodi->id }})" 
                            style="background-color: #f59e0b; border-color: #f59e0b; color: white;">Edit</flux:button>
                        <flux:button variant="filled" size="sm" icon="trash" wire:click="delete({{ $prodi->id }})" 
                            wire:confirm="Hapus program keahlian ini?"
                            style="background-color: #f43f5e; border-color: #f43f5e; color: white;">Hapus</flux:button>
                    </div>
                </flux:table.cell>
            </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">{{ $prodis->links() }}</div>

    {{-- Modal menggunakan wire:model sesuai contoh Kelas Anda --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[450px]">
        <form wire:submit="store" class="space-y-4">
            <flux:heading size="lg">{{ $prodiId ? 'Edit Prodi' : 'Tambah Prodi' }}</flux:heading>
            
            <flux:input label="Nama Program Keahlian" wire:model="name" placeholder="Contoh: Pengembangan Perangkat Lunak dan Gim" />
            <flux:input label="Alias / Singkatan" wire:model="alias" placeholder="Contoh: PPLG" />

            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>