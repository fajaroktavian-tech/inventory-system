<div class="p-6">
    <flux:heading size="xl" level="1">Kelola Pengguna</flux:heading>
    <flux:subheading>Manajemen data staf, guru, dan administrator sistem.</flux:subheading>

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
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell>
                        <div class="font-medium">{{ $user->name }}</div>
                        <div class="text-xs text-zinc-500">{{ $user->email ?? '-' }}</div>
                    </flux:table.cell>
                    <flux:table.cell class="font-mono text-xs">{{ $user->username }}</flux:table.cell>
                    <flux:table.cell>
                        @php
                            // Logika warna badge berdasarkan role
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
                            <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="edit({{ $user->id }})" />
                            
                            @if($user->id !== auth()->id())
                                <flux:button variant="ghost" size="sm" icon="trash" color="red" 
                                    wire:click="delete({{ $user->id }})"
                                    wire:confirm="Apakah Anda yakin ingin menghapus user ini?" />
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
        <div class="space-y-6">
            <flux:heading size="lg">{{ $userId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</flux:heading>

            <form wire:submit="store" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <flux:input label="Nama Lengkap" wire:model="name" placeholder="Contoh: Budi Santoso, S.Pd" />
                </div>
                
                <flux:input label="Username" wire:model="username" />
                <flux:input label="Email (Opsional)" wire:model="email" type="email" />

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
                    :placeholder="$userId ? 'Kosongkan jika tidak diganti' : 'Minimal 8 karakter'" />

                {{-- Jika Role adalah Wali Kelas, tampilkan pilihan kelas (Opsional jika ada properti class_id di component) --}}
                @if($role === 'walikelas' || $role === 'siswa')
                <div class="md:col-span-2">
                    <flux:select label="Penempatan Kelas" wire:model="class_id">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach(\App\Models\ClassModel::all() as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                @endif

                <div class="md:col-span-2 p-4 bg-zinc-50 dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-xl">
                    <flux:input label="RFID UID" wire:model="rfid_uid" placeholder="Tap kartu pada reader..." />
                    <flux:text size="xs" class="mt-2 text-zinc-500">Gunakan RFID untuk login cepat di Kios Gateway atau absen Staf.</flux:text>
                </div>

                <div class="md:col-span-2 flex mt-4">
                    <flux:spacer />
                    <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Simpan Data</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>