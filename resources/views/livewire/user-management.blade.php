<div class="p-6">
    <flux:heading size="xl" level="1">Kelola Pengguna</flux:heading>
    <flux:subheading>Manajemen data admin, petugas, dan owner sistem.</flux:subheading>

    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari user..." class="max-w-xs" />
        <flux:button variant="primary" icon="plus" wire:click="create">Tambah User</flux:button>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Nama</flux:table.column>
            <flux:table.column>Username</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell>{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->username }}</flux:table.cell>
                    <flux:table.cell>
                        {{-- Gunakan badge tanpa atribut tambahan --}}
                        <flux:badge>{{ strtoupper($user->role) }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-2">
                            <flux:button variant="primary" color="yellow" size="sm" wire:click="edit({{ $user->id }})">
                                Edit
                            </flux:button>

                            @if($user->id !== auth()->id())
                                <flux:button variant="danger" size="sm" wire:click="delete({{ $user->id }})"
                                    wire:confirm="Hapus?">
                                    Hapus
                                </flux:button>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

    {{-- MODAL FORM --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[400px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $userId ? 'Edit User' : 'Tambah User' }}</flux:heading>
            </div>

            <form wire:submit="store" class="space-y-4">
                <flux:input label="Nama Lengkap" wire:model="name" />
                <flux:input label="Username" wire:model="username" />
                <flux:input label="Email (Opsional)" wire:model="email" />

                <flux:select label="Role" wire:model="role">
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas Gudang</option>
                    <option value="owner">Owner</option>
                    <option value="guru">Guru</option>
                    <option value="staff">Staff</option>
                    <option value="siswa">Siswa</option>
                </flux:select>

                <flux:input label="Password" type="password" wire:model="password"
                    :placeholder="$userId ? 'Kosongkan jika tidak diubah' : ''" />

                <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg">
                    <flux:input label="RFID UID (Tap kartu sekarang)" wire:model="rfid_uid" autofocus />
                    <p class="text-[10px] mt-1 text-blue-600 font-medium">* Letakkan kursor di sini lalu tap kartu pada
                        reader</p>
                </div>


                <div class="flex mt-6">
                    <flux:spacer />
                    <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>