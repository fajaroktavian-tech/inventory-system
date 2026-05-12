<div class="p-6">
    <flux:heading size="xl" level="1">Kelola Data Ruangan</flux:heading>
    <flux:subheading>Daftar ruangan atau lokasi di lingkungan sekolah.</flux:subheading>

    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari ruangan..." class="max-w-xs" />
        <flux:button variant="primary" icon="plus" wire:click="create">Tambah Ruangan</flux:button>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>No</flux:table.column>
            <flux:table.column>Nama Ruangan</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($rooms as $room)
            <flux:table.row :key="$room->id">
                <flux:table.cell>
                    {{ ($rooms->currentPage() - 1) * $rooms->perPage() + $loop->iteration }}
                </flux:table.cell>
                
                <flux:table.cell font="medium">{{ $room->name }}</flux:table.cell>
                
                <flux:table.cell>
                    <div class="flex gap-2">
                        {{-- Tombol Edit --}}
                        <flux:button variant="filled" size="sm" icon="pencil-square" wire:click="edit({{ $room->id }})" 
                            style="background-color: #f59e0b; border-color: #f59e0b; color: white;">
                            Edit
                        </flux:button>
                        
                        {{-- Tombol Hapus --}}
                        <flux:button variant="filled" size="sm" icon="trash" wire:click="delete({{ $room->id }})" 
                            wire:confirm="Hapus ruangan ini?"
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
        {{ $rooms->links() }}
    </div>

    {{-- MODAL FORM RUANGAN --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[400px]">
        <form wire:submit="store" class="space-y-4">
            <flux:heading size="lg">{{ $roomId ? 'Edit Ruangan' : 'Tambah Ruangan' }}</flux:heading>

            <flux:input label="Nama Ruangan" wire:model="name" placeholder="Contoh: Lab Komputer 1, TU, dll" />

            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>