<div class="p-6">
    <flux:heading size="xl" level="1">Kelola Kategori Barang</flux:heading>
    <flux:subheading>Daftar kategori untuk pengelompokan barang inventaris.</flux:subheading>

    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari kategori..." class="max-w-xs" />
        <div class="flex gap-2">
            <flux:button icon="question-mark-circle" wire:click="$set('isCategoryGuideOpen', true)" variant="ghost">
                Panduan
            </flux:button>
            <flux:button variant="primary" icon="plus" wire:click="create">Tambah Kategori</flux:button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="p-3 mb-4 text-sm text-white bg-red-500 rounded-lg">{{ session('error') }}</div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>No</flux:table.column>
            <flux:table.column>Nama Kategori</flux:table.column>
            <flux:table.column>Jumlah Barang</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($categories as $category)
                <flux:table.row :key="$category->id">
                    <flux:table.cell>
                        {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                    </flux:table.cell>
                    <flux:table.cell font="medium">{{ $category->name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="zinc" size="sm">{{ $category->items_count ?? $category->items()->count() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-2">
                            {{-- Tombol Edit dengan Style Manual Amber --}}
                            <flux:button variant="filled" size="sm" icon="pencil-square"
                                wire:click="edit({{ $category->id }})"
                                style="background-color: #f59e0b; border-color: #f59e0b; color: white;">
                                Edit
                            </flux:button>

                            {{-- Tombol Hapus dengan Style Manual Rose --}}
                            <flux:button variant="filled" size="sm" icon="trash" wire:click="delete({{ $category->id }})"
                                wire:confirm="Hapus kategori ini?"
                                style="background-color: #f43f5e; border-color: #f43f5e; color: white;">
                                Hapus
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">{{ $categories->links() }}</div>
    {{-- MODAL --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[400px]">
        <form wire:submit="store" class="space-y-4">
            <flux:heading size="lg">{{ $categoryId ? 'Edit Kategori' : 'Tambah Kategori' }}</flux:heading>
            <flux:input label="Nama Kategori" wire:model="name" />
            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- MODAL PANDUAN KATEGORI --}}
<flux:modal wire:model="isCategoryGuideOpen" class="md:w-[500px]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Panduan Kelola Kategori</flux:heading>
            <flux:subheading>Cara mengatur pengelompokan barang inventaris Anda.</flux:subheading>
        </div>
        
        <div class="space-y-4 text-sm text-zinc-600">
            <!-- 1. Menambah Kategori -->
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 font-bold">1</div>
                <div>
                    <p class="font-bold text-zinc-900">Menambah Kategori</p>
                    <p>Klik tombol <b>"Tambah Kategori"</b>, isi nama kategori (misalnya: Elektronik, Mebel, atau Alat Tulis), lalu klik <b>Simpan</b>.</p>
                </div>
            </div>

            <!-- 2. Mengelola Barang -->
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-orange-100 text-orange-600 font-bold">2</div>
                <div>
                    <p class="font-bold text-zinc-900">Edit & Hapus</p>
                    <p>Gunakan tombol <b>Edit</b> (oranye) untuk mengubah nama kategori. Gunakan tombol <b>Hapus</b> (merah) untuk menghapus kategori yang tidak lagi digunakan.</p>
                </div>
            </div>

            <!-- 3. Catatan Penting -->
            <div class="p-3 bg-amber-50 border border-amber-100 rounded-lg text-amber-800">
                <p class="text-xs font-semibold">Catatan Penting:</p>
                <p class="text-xs">Pastikan tidak ada barang yang terdaftar di dalam kategori sebelum Anda menghapusnya, agar data inventaris tetap rapi.</p>
            </div>
        </div>

        <div class="flex">
            <flux:spacer />
            <flux:button variant="primary" wire:click="$set('isCategoryGuideOpen', false)">Mengerti</flux:button>
        </div>
    </div>
</flux:modal>

</div>