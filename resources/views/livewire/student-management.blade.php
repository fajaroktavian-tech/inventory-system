<div class="p-6">
    <flux:heading size="xl">Data Siswa (Master)</flux:heading>

    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari nama, NIS, atau UID..." class="max-w-xs" />
        <flux:button variant="primary" icon="plus" wire:click="create">Tambah Siswa</flux:button>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">{{ session('message') }}</div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Profil</flux:table.column>
            <flux:table.column>NIS & UID</flux:table.column>
            <flux:table.column>Kelas</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($students as $student)
                <flux:table.row :key="$student->id">
                    <flux:table.cell>
                        <div class="flex items-center gap-4">
                            {{-- Avatar diperbesar ke size="lg" --}}
                            <flux:avatar src="{{ $student->avatar ? asset('storage/' . $student->avatar) : null }}"
                                initials="{{ $student->initials() }}" size="lg" class="rounded-xl" />
                            <div>
                                <p class="font-semibold text-zinc-800 dark:text-white leading-tight">{{ $student->name }}</p>
                                <p class="text-xs text-zinc-500">{{ $student->username }}</p>
                            </div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="space-y-1">
                            <p class="text-sm font-medium">NIS: {{ $student->nis ?? '-' }}</p>
                            <flux:badge color="blue" size="sm">{{ $student->rfid_uid }}</flux:badge>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <p class="font-medium">{{ $student->class->name ?? '-' }}</p>
                        <p class="text-[10px] text-zinc-500 uppercase">{{ $student->class?->prodi?->alias ?? 'N/A' }}</p>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex justify gap-2">
                            {{-- Tombol Detail Baru --}}
                            <flux:button variant="ghost" size="sm" icon="eye" wire:click="showDetail({{ $student->id }})">Detail</flux:button>
                            
                            <flux:button variant="filled" size="sm" icon="pencil-square"
                                wire:click="edit({{ $student->id }})" style="background-color: #f59e0b; color: white;">
                            </flux:button>
                            <flux:button variant="filled" size="sm" icon="trash" wire:click="delete({{ $student->id }})"
                                wire:confirm="Hapus siswa ini?" style="background-color: #f43f5e; color: white;">
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">{{ $students->links() }}</div>

    {{-- MODAL FORM (TAMBAH/EDIT) --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[500px]">
        <form wire:submit="store" class="space-y-4">
            <flux:heading size="lg">{{ $studentId ? 'Edit Siswa' : 'Pendaftaran Siswa Baru' }}</flux:heading>
            
            <div class="grid grid-cols-2 gap-4">
                <flux:input label="Nama Lengkap" wire:model="name" />
                <flux:input label="NIS" wire:model="nis" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:select label="Kelas" wire:model="class_id" placeholder="Pilih Kelas...">
                    @foreach($classes as $class)
                        <flux:select.option value="{{ $class->id }}">
                            {{ $class->name }} ({{ $class->prodi?->alias ?? 'Tanpa Prodi' }})
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input label="Username Login" wire:model="username" />
            </div>

            <flux:input label="No. Telepon" wire:model="phone" />
            <flux:textarea label="Alamat" wire:model="address" />
            <flux:input type="file" label="Foto Profil" wire:model="new_avatar" />

            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-lg">
                <flux:input label="RFID UID" wire:model="rfid_uid" placeholder="Tap kartu..." />
            </div>

            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Data</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- MODAL DETAIL SISWA --}}
    <flux:modal wire:model="isDetailModalOpen" class="md:w-[500px]">
        @if($selectedStudent)
        <div class="space-y-6">
            <div class="flex flex-col items-center">
                {{-- Foto Profil Besar --}}
                <div class="relative">
                    @if($selectedStudent->avatar)
                        <img src="{{ asset('storage/' . $selectedStudent->avatar) }}" 
                             alt="{{ $selectedStudent->name }}" 
                             class="w-32 h-32 object-cover rounded-3xl shadow-lg border-4 border-white dark:border-zinc-800">
                    @else
                        {{-- Placeholder jika tidak ada foto --}}
                        <div class="w-48 h-48 flex items-center justify-center bg-zinc-100 dark:bg-zinc-800 rounded-3xl border-4 border-white dark:border-zinc-700">
                            <span class="text-4xl font-bold text-zinc-400">{{ $selectedStudent->initials() }}</span>
                        </div>
                    @endif
                </div>

                <div class="mt-4 text-center">
                    <flux:heading size="xl">{{ $selectedStudent->name }}</flux:heading>
                    <div class="flex justify-center gap-2 mt-1">
                        <flux:badge color="zinc" variant="subtle">{{ $selectedStudent->username }}</flux:badge>
                        <flux:badge color="blue" variant="subtle">Siswa</flux:badge>
                    </div>
                </div>
            </div>

            <hr class="border-zinc-200 dark:border-zinc-700" />

            <div class="grid grid-cols-2 gap-x-4 gap-y-5 text-sm">
                <div class="space-y-1">
                    <p class="text-zinc-500 flex items-center gap-2">
                        <flux:icon name="identification" variant="micro" /> NIS
                    </p>
                    <p class="font-semibold text-zinc-800 dark:text-white">{{ $selectedStudent->nis ?? '-' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-zinc-500 flex items-center gap-2">
                        <flux:icon name="academic-cap" variant="micro" /> Program Keahlian
                    </p>
                    <p class="font-semibold text-zinc-800 dark:text-white">{{ $selectedStudent->class?->prodi?->name ?? '-' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-zinc-500 flex items-center gap-2">
                        <flux:icon name="home-modern" variant="micro" /> Kelas
                    </p>
                    <p class="font-semibold text-zinc-800 dark:text-white">{{ $selectedStudent->class?->name ?? '-' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-zinc-500 flex items-center gap-2">
                        <flux:icon name="credit-card" variant="micro" /> RFID UID
                    </p>
                    <p class="font-mono font-bold text-blue-600 dark:text-blue-400">{{ $selectedStudent->rfid_uid }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-zinc-500 flex items-center gap-2">
                        <flux:icon name="phone" variant="micro" /> No. Telepon
                    </p>
                    <p class="font-semibold text-zinc-800 dark:text-white">{{ $selectedStudent->phone ?? '-' }}</p>
                </div>
                <div class="col-span-2 space-y-1">
                    <p class="text-zinc-500 flex items-center gap-2">
                        <flux:icon name="map-pin" variant="micro" /> Alamat Lengkap
                    </p>
                    <p class="font-semibold text-zinc-800 dark:text-white leading-relaxed">{{ $selectedStudent->address ?? '-' }}</p>
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