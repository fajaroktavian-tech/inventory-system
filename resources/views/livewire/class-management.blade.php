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
            <flux:table.column>Wali Kelas</flux:table.column>
            <flux:table.column>Jumlah Siswa</flux:table.column>
            <flux:table.column>Program Keahlian</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($classes as $class)
                <flux:table.row :key="$class->id">
                    <flux:table.cell>{{ ($classes->currentPage() - 1) * $classes->perPage() + $loop->iteration }}
                    </flux:table.cell>
                    <flux:table.cell font="medium">{{ $class->name }}</flux:table.cell>

                    {{-- Menampilkan Nama Wali Kelas --}}
                    <flux:table.cell>{{ $class->waliKelas->name ?? 'Belum Ditentukan' }}</flux:table.cell>

                    {{-- Menampilkan Jumlah Siswa (Hasil withCount) --}}
                    <flux:table.cell>
                        <flux:badge color="zinc" variant="subtle">{{ $class->users_count }} Siswa</flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>
                        {{-- Menampilkan Alias Prodi --}}
                        <flux:badge size="sm" color="zinc">{{ $class->prodi->alias ?? '-' }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" />

                            <flux:menu>
                                <flux:menu.item icon="arrow-up-circle" wire:click="openPromotionModal({{ $class->id }})">
                                    Naikkan Kelas
                                </flux:menu.item>

                                @if(str_contains(strtolower($class->name), 'xii'))
                                    <flux:menu.item icon="academic-cap" color="indigo"
                                        wire:click="graduateClass({{ $class->id }})"
                                        wire:confirm="Yakin ingin meluluskan kelas ini?">
                                        Luluskan Siswa
                                    </flux:menu.item>
                                @endif

                                <flux:menu.separator />

                                <flux:menu.item icon="pencil-square" wire:click="edit({{ $class->id }})">
                                    Edit Data
                                </flux:menu.item>

                                <flux:menu.item icon="trash" color="danger" wire:click="delete({{ $class->id }})"
                                    wire:confirm="Hapus kelas ini?">
                                    Hapus Kelas
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
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
                    <flux:select.option value="{{ $prodi->id }}">{{ $prodi->name }} ({{ $prodi->alias }})
                    </flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Tambahkan Modal Kenaikan di bawah --}}
    <flux:modal wire:model="isPromotionModalOpen" class="md:w-[600px]">
        <form wire:submit="promoteClass" class="space-y-4">
            <flux:heading size="lg">Konfirmasi Kenaikan Kelas</flux:heading>

            <flux:text>Berikut adalah daftar siswa yang akan dipindahkan:</flux:text>

            <div class="max-h-60 overflow-y-auto border rounded-lg p-2">
                <table class="w-full text-sm">
                    @foreach($studentsInClass as $index => $student)
                        <tr class="border-b">
                            <td class="py-2">{{ $student['name'] }}</td>
                            <td class="py-2 text-right">
                                {{-- Tombol untuk mengeluarkan siswa dari daftar migrasi --}}
                                <flux:button size="xs" variant="ghost" color="danger"
                                    wire:click="removeStudentFromList({{ $index }})">
                                    X
                                </flux:button>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <div class="space-y-2">
            <flux:label>Pilih Kelas</flux:label>
            <flux:input wire:model.live="searchKelas" placeholder="Ketik untuk cari kelas..." />
            
            <flux:select wire:model="class_id" placeholder="Pilih Kelas...">
                @foreach($classes as $class)
                    <flux:select.option value="{{ $class->id }}">
                        {{ $class->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>

            <div class="flex mt-6 gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isPromotionModalOpen', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary" color="danger">Proses Kenaikan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>