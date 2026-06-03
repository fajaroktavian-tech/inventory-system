<div class="p-6">
    <flux:heading size="xl">Kelola Data Kelas</flux:heading>
    
    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari kelas..." class="max-w-xs" />
        <flux:button variant="primary" icon="plus" wire:click="create">Tambah Kelas</flux:button>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">{{ session('message') }}</div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>No</flux:table.column>
            <flux:table.column>Nama Kelas</flux:table.column>
            <flux:table.column>Program Keahlian</flux:table.column> {{-- Tambah Kolom --}}
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($classes as $class)
            <flux:table.row :key="$class->id">
                <flux:table.cell>{{ ($classes->currentPage() - 1) * $classes->perPage() + $loop->iteration }}</flux:table.cell>
                <flux:table.cell font="medium">{{ $class->name }}</flux:table.cell>
                <flux:table.cell>
                    {{-- Menampilkan Alias Prodi --}}
                    <flux:badge size="sm" color="zinc">{{ $class->prodi->alias ?? '-' }}</flux:badge>
                </flux:table.cell>
                <flux:table.cell>
                    <div class="flex gap-2">
                        <flux:button variant="filled" size="sm" icon="pencil-square" wire:click="edit({{ $class->id }})" 
                            style="background-color: #f59e0b; border-color: #f59e0b; color: white;">Edit</flux:button>
                        <flux:button variant="filled" size="sm" icon="trash" wire:click="delete({{ $class->id }})" 
                            wire:confirm="Hapus kelas ini?"
                            style="background-color: #f43f5e; border-color: #f43f5e; color: white;">Hapus</flux:button>
                    </div>
                </flux:table.cell>
            </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">{{ $classes->links() }}</div>

    <flux:modal wire:model="isModalOpen" class="md:w-[450px]">
        <form wire:submit="store" class="space-y-4">
            <flux:heading size="lg">{{ $classId ? 'Edit Kelas' : 'Tambah Kelas' }}</flux:heading>
            
            <flux:input label="Nama Kelas" wire:model="name" placeholder="Contoh: XII RPL 1" />

            {{-- Input Select untuk Memilih Prodi --}}
            <flux:select label="Program Keahlian" wire:model="prodi_id" placeholder="Pilih Prodi...">
                @foreach($prodis as $prodi)
                    <flux:select.option value="{{ $prodi->id }}">{{ $prodi->name }} ({{ $prodi->alias }})</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>