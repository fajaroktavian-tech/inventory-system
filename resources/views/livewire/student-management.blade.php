<div class="p-6">
    <flux:heading size="xl">Data Siswa (RFID)</flux:heading>

    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari nama atau UID..."
            class="max-w-xs" />
        <flux:button variant="primary" icon="plus" wire:click="create">Tambah Siswa</flux:button>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">{{ session('message') }}</div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Nama</flux:table.column>
            <flux:table.column>Kelas</flux:table.column>
            <flux:table.column>Username</flux:table.column>
            <flux:table.column>RFID UID</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($students as $student)
                <flux:table.row :key="$student->id">
                    <flux:table.cell font="medium">{{ $student->name }}</flux:table.cell>
                    <flux:table.cell>{{ $student->class->name ?? '-' }}</flux:table.cell> {{-- Relasi --}}
                    <flux:table.cell>{{ $student->username }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="blue" size="sm" inset="top bottom">{{ $student->rfid_uid }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-2">
                            <flux:button variant="filled" size="sm" icon="pencil-square"
                                wire:click="edit({{ $student->id }})" style="background-color: #f59e0b; color: white;">Edit
                            </flux:button>
                            <flux:button variant="filled" size="sm" icon="trash" wire:click="delete({{ $student->id }})"
                                wire:confirm="Hapus siswa ini?" style="background-color: #f43f5e; color: white;">Hapus
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">{{ $students->links() }}</div>

    {{-- MODAL TAMBAH/EDIT --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[450px]">
        <form wire:submit="store" class="space-y-4">
            <flux:heading size="lg">{{ $studentId ? 'Edit Siswa' : 'Pendaftaran Siswa Baru' }}</flux:heading>
            <flux:input label="Nama Lengkap" wire:model="name" />
            {{-- Dropdown Kelas --}}
            <flux:select label="Kelas" wire:model="class_id">
                <option value="">-- Pilih Kelas --</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </flux:select>
            <flux:input label="Username / NIS" wire:model="username" />

            <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg">
                <flux:input label="RFID UID (Tap kartu sekarang)" wire:model="rfid_uid" autofocus />
                <p class="text-[10px] mt-1 text-blue-600 font-medium">* Letakkan kursor di sini lalu tap kartu pada
                    reader</p>
            </div>

            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Data</flux:button>
            </div>
        </form>
    </flux:modal>
</div>