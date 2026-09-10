<div class="p-6">
    <flux:heading size="xl" level="1">Kelola Pengguna</flux:heading>
    <flux:subheading>Manajemen data staf, guru, dan administrator sistem.</flux:subheading>

    <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4 mt-8 mb-4">
        <div class="flex flex-wrap items-center gap-3">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari user..." class="max-w-xs" />
        </div>
        {{-- Dropdown Filter Role --}}
        <flux:select wire:model.live="selectedRole" class="max-w-xs">
                <option value="">Semua Role</option>
                <option value="admin">Administrator</option>
                <option value="kesiswaan">Kesiswaan</option>
                <option value="walikelas">Wali Kelas</option>
                <option value="piket">Guru Piket</option>
                <option value="guru">Guru Mapel</option>
                <option value="staff">Staf Tata Usaha</option>
                <option value="petugas">Petugas</option>
                <option value="owner">Owner</option>
                <option value="siswa">Siswa</option>
            </flux:select>

        <flux:button variant="primary" icon="plus" wire:click="create">Tambah User</flux:button>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Pengguna</flux:table.column>
            <flux:table.column>Username</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($users as $user)
                <flux:table.row :key="$user->id">
                    {{-- Kolom Pengguna dengan Avatar dan Nama/Email --}}
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <flux:avatar src="{{ $user->avatar ? asset('storage/' . $user->avatar) : null }}" :name="$user->name" size="lg" />
                            <div>
                                <p class="font-medium">{{ $user->name }}</p>
                                <p class="text-xs text-zinc-500">{{ $user->email ?? '-' }}</p>
                            </div>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell class="font-mono text-xs">{{ $user->username }}</flux:table.cell>
                    
                    <flux:table.cell>
                        @php
                            $color = match($user->role) {
                                'admin' => 'red',
                                'kesiswaan' => 'purple',
                                'walikelas' => 'blue',
                                'piket' => 'orange',
                                'petugas' => 'emerald',
                                'guru', 'staff' => 'zinc',
                                'siswa' => 'cyan',
                                default => 'zinc'
                            };
                        @endphp
                        <flux:badge :color="$color" size="sm" inset="top bottom">
                            {{ strtoupper($user->role) }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" :color="$user->is_active ? 'green' : 'red'" variant="pill">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex gap-2">
                            <flux:button variant="filled" size="sm" icon="pencil-square" wire:click="edit({{ $user->id }})" style="background-color: #f59e0b; color: white;" />
                            
                            @if($user->id !== auth()->id())
                                <flux:button variant="filled" size="sm" icon="trash" wire:click="delete({{ $user->id }})" wire:confirm="Apakah Anda yakin ingin menghapus user ini?" style="background-color: #f43f5e; color: white;" />
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
    <flux:modal wire:model="isModalOpen" class="md:w-[500px]">
        <form wire:submit="store" class="space-y-4">
            <flux:heading size="lg">{{ $userId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</flux:heading>

            {{-- Preview Foto di dalam Modal --}}
            <div class="flex items-center gap-4 py-2">
                @if ($new_avatar)
                    <img src="{{ $new_avatar->temporaryUrl() }}" class="w-16 h-16 rounded-full object-cover border shadow-sm">
                @elseif ($avatar)
                    <img src="{{ asset('storage/' . $avatar) }}" class="w-16 h-16 rounded-full object-cover border shadow-sm">
                @endif
                <div class="flex-1">
                    <flux:input type="file" label="Foto Profil (Max 1MB)" wire:model="new_avatar" />
                </div>
            </div>

            <flux:input label="Nama Lengkap" wire:model="name" placeholder="Contoh: Budi Santoso, S.Pd" />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input label="Username" wire:model="username" />
                <flux:input label="Email (Opsional)" wire:model="email" type="email" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:select label="Role / Hak Akses" wire:model.live="role">
                    <option value="">-- Pilih Role --</option>
                    <optgroup label="Administrator">
                        <option value="admin">Administrator (Full)</option>
                        <option value="kesiswaan">Bagian Kesiswaan</option>
                    </optgroup>
                    <optgroup label="Tenaga Pendidik">
                        <option value="walikelas">Wali Kelas</option>
                        <option value="piket">Guru Piket</option>
                        <option value="guru">Guru Mapel</option>
                    </optgroup>
                    <optgroup label="Staf & Lainnya">
                        <option value="petugas">Petugas Gudang/Aset</option>
                        <option value="staff">Staf Tata Usaha</option>
                        <option value="owner">Yayasan/Owner</option>
                        <option value="siswa">Siswa</option>
                    </optgroup>
                </flux:select>

                <flux:input label="Password" type="password" wire:model="password"
                    :placeholder="$userId ? 'Kosongkan' : 'Min. 6 karakter'" />
            </div>

            @if($role === 'walikelas' || $role === 'siswa')
            <flux:select label="Penempatan Kelas" wire:model="class_id">
                <option value="">-- Pilih Kelas --</option>
                @foreach(\App\Models\ClassModel::all() as $cls)
                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                @endforeach
            </flux:select>
            @endif

            <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                <flux:input label="RFID UID" wire:model="rfid_uid" placeholder="Tap kartu pada reader..." />
            </div>

            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Data</flux:button>
            </div>
        </form>
    </flux:modal>
</div>