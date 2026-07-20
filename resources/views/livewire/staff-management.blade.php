<div class="p-6">
    <flux:heading size="xl">Data Guru & Staff</flux:heading>
    {{-- Tambahkan Card Statistik di sini --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <flux:card class="p-4 border-l-4 border-blue-500">
            <flux:subheading>Total Guru</flux:subheading>
            <div class="text-3xl font-black mt-2 text-blue-600">{{ $totalGuru }}</div>
        </flux:card>
        
        <flux:card class="p-4 border-l-4 border-orange-500">
            <flux:subheading>Total Staff TU</flux:subheading>
            <div class="text-3xl font-black mt-2 text-orange-600">{{ $totalStaff }}</div>
        </flux:card>
    </div>

    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari NIP atau Nama..." class="max-w-xs" />
        <flux:button variant="primary" icon="plus" wire:click="create">Tambah Personel</flux:button>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">{{ session('message') }}</div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Personel</flux:table.column>
            <flux:table.column>NIP / Jabatan</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($staffs as $item)
                <flux:table.row :key="$item->id">
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <flux:avatar src="{{ $item->avatar ? asset('storage/' . $item->avatar) : null }}" initials="{{ $item->initials() }}" size="lg" />
                            <div>
                                <p class="font-medium">{{ $item->name }}</p>
                                <p class="text-xs text-zinc-500">{{ $item->username }}</p>
                            </div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <p class="text-sm font-medium">{{ $item->nip ?? '-' }}</p>
                        <p class="text-xs text-zinc-500">{{ $item->position ?? 'Staff' }}</p>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="{{ $item->role === 'guru' ? 'blue' : 'orange' }}">{{ strtoupper($item->role) }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex justify gap-2">
                            <flux:button variant="ghost" size="sm" icon="eye" wire:click="showDetail({{ $item->id }})"></flux:button>
                            <flux:button variant="filled" size="sm" icon="pencil-square" wire:click="edit({{ $item->id }})" style="background-color: #f59e0b; color: white;"></flux:button>
                            <flux:button variant="filled" size="sm" icon="trash" wire:click="delete({{ $item->id }})" wire:confirm="Hapus data ini?" style="background-color: #f43f5e; color: white;"></flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
    <div class="mt-4">
        {{ $staffs->links() }}
    </div>

    {{-- 1. MODAL FORM (UNTUK TAMBAH & EDIT) --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[500px]">
        <form wire:submit="store" class="space-y-4">
            <flux:heading size="lg">{{ $staffId ? 'Edit Personel' : 'Tambah Personel Baru' }}</flux:heading>
            
            <div class="grid grid-cols-2 gap-4">
                <flux:input label="Nama Lengkap" wire:model="name" />
                <flux:input label="NIP" wire:model="nip" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:select label="Role" wire:model="role" placeholder="Pilih Role...">
                    <flux:select.option value="guru">Guru</flux:select.option>
                    <flux:select.option value="staff">Staff TU</flux:select.option>
                </flux:select>
                <flux:input label="Jabatan" wire:model="position" placeholder="Contoh: Pembina OSIS" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input label="Username Login" wire:model="username" />
                <flux:input label="No. Telepon" wire:model="phone" />
            </div>

            <flux:textarea label="Alamat" wire:model="address" />

            <flux:input type="file" label="Foto Profil (Max 1MB)" wire:model="new_avatar" />

            <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                <flux:input label="RFID UID" wire:model="rfid_uid" placeholder="Tap kartu..." />
            </div>

            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Data</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- 2. MODAL DETAIL (UNTUK LIHAT DATA) --}}
    <flux:modal wire:model="isDetailModalOpen" class="md:w-[500px]">
        @if($selectedStaff)
            <div class="space-y-6">
                <div class="flex flex-col items-center">
                    <div class="relative">
                        @if($selectedStaff->avatar)
                            <img src="{{ asset('storage/' . $selectedStaff->avatar) }}" 
                                 class="w-40 h-40 object-cover rounded-3xl shadow-md border-4 border-white dark:border-zinc-800">
                        @else
                            <div class="w-40 h-40 flex items-center justify-center bg-zinc-100 dark:bg-zinc-800 rounded-3xl border-4 border-white dark:border-zinc-700">
                                <span class="text-3xl font-bold text-zinc-400">{{ $selectedStaff->initials() }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 text-center">
                        <flux:heading size="xl">{{ $selectedStaff->name }}</flux:heading>
                        <flux:badge color="zinc" variant="subtle" class="mt-1">{{ strtoupper($selectedStaff->role) }}</flux:badge>
                    </div>
                </div>

                <hr class="border-zinc-200 dark:border-zinc-700" />

                <div class="grid grid-cols-2 gap-y-5 text-sm">
                    <div class="space-y-1 text-zinc-800 dark:text-zinc-200">
                        <p class="text-zinc-500">NIP</p>
                        <p class="font-semibold">{{ $selectedStaff->nip ?? '-' }}</p>
                    </div>
                    <div class="space-y-1 text-zinc-800 dark:text-zinc-200">
                        <p class="text-zinc-500">Jabatan</p>
                        <p class="font-semibold">{{ $selectedStaff->position ?? '-' }}</p>
                    </div>
                    <div class="space-y-1 text-zinc-800 dark:text-zinc-200">
                        <p class="text-zinc-500 text-xs">No. Telepon</p>
                        <p class="font-semibold">{{ $selectedStaff->phone ?? '-' }}</p>
                    </div>
                    <div class="space-y-1 text-zinc-800 dark:text-zinc-200">
                        <p class="text-zinc-500">RFID UID</p>
                        <p class="font-mono font-bold text-blue-600">{{ $selectedStaff->rfid_uid ?? '-' }}</p>
                    </div>
                    <div class="col-span-2 space-y-1 text-zinc-800 dark:text-zinc-200">
                        <p class="text-zinc-500">Alamat</p>
                        <p class="font-semibold leading-relaxed">{{ $selectedStaff->address ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex mt-8">
                    <flux:spacer />
                    <flux:button variant="ghost" wire:click="$set('isDetailModalOpen', false)">Tutup</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>