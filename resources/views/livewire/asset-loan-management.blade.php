<div class="p-6 lg:p-8 space-y-6">
    <div>
        <flux:heading size="xl" level="1">Peminjaman Aset</flux:heading>
        <flux:subheading>Kelola sirkulasi peminjaman dan pengembalian aset sekolah.</flux:subheading>
    </div>

    {{-- KARTU STATISTIK RINGKASAN --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <flux:card class="p-4">
            <flux:text class="text-zinc-500 uppercase text-xs font-bold">Total Riwayat Peminjaman</flux:text>
            <div class="text-2xl font-black mt-1">{{ $stats['total'] }}</div>
        </flux:card>
        <flux:card class="p-4 border-l-4 border-blue-500">
            <flux:text class="text-zinc-500 uppercase text-xs font-bold">Sedang Dipinjam (Aktif)</flux:text>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $stats['active'] }}</div>
        </flux:card>
        <flux:card class="p-4 border-l-4 border-green-500">
            <flux:text class="text-zinc-500 uppercase text-xs font-bold">Sudah Dikembalikan</flux:text>
            <div class="text-2xl font-black text-green-600 mt-1">{{ $stats['returned'] }}</div>
        </flux:card>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">{{ session('message') }}</div>
    @endif

    {{-- KARTU KONTROL & TABEL --}}
    <flux:card>
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari peminjam, nama barang, atau SN..." class="max-w-md w-full" />
            
            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <flux:dropdown>
                    <flux:button icon-trailing="chevron-down" variant="outline">Export</flux:button>
                    <flux:menu class="min-w-48">
                        <flux:menu.item icon="document-text" wire:click="exportPdf">Export PDF</flux:menu.item>
                        <flux:menu.separator />
                        <flux:menu.item icon="table-cells" wire:click="exportExcel">Export Excel</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                <flux:button variant="primary" icon="plus" wire:click="create">Catat Peminjaman</flux:button>
            </div>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Peminjam</flux:table.column>
                <flux:table.column>Aset / SN</flux:table.column>
                <flux:table.column>Tgl Pinjam</flux:table.column>
                <flux:table.column>Catatan</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column align="end">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($loans as $loan)
                    <flux:table.row :key="$loan->id">
                        <flux:table.cell>
                            <p class="font-medium">{{ $loan->user->name ?? '-' }}</p>
                            <p class="text-[10px] text-zinc-500">
                                {{ strtoupper($loan->user->role ?? '-') }}
                                @if(strtolower(optional($loan->user)->role ?? '') === 'siswa' && optional($loan->user->class)->name)
                                    • {{ $loan->user->class->name }}
                                @endif
                            </p>
                        </flux:table.cell>
                        <flux:table.cell>
                            <p class="text-sm font-medium">{{ optional(optional($loan->asset)->itemInfo)->name ?? 'Aset Terhapus' }}</p>
                            <p class="text-[10px] text-zinc-500 italic">SN: {{ optional($loan->asset)->serial_number ?? '-' }}</p>
                        </flux:table.cell>
                        <flux:table.cell>
                            <p class="text-sm">{{ \Carbon\Carbon::parse($loan->loan_date)->format('d M Y') }}</p>
                            @if($loan->due_date && $loan->status === 'active')
                                <p class="text-[10px] text-red-500 font-medium">Tempo:
                                    {{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}
                                </p>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <p class="text-xs text-zinc-600 dark:text-zinc-300 italic max-w-xs truncate" title="{{ $loan->notes }}">
                                {{ $loan->notes ?: '-' }}
                            </p>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm" color="{{ $loan->status === 'active' ? 'blue' : 'green' }}" variant="subtle">
                                {{ $loan->status === 'active' ? 'Dipinjam' : 'Kembali' }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex justify-end gap-2">
                                @if($loan->status === 'active')
                                    <flux:button size="sm" variant="filled" wire:click="returnAsset({{ $loan->id }})" color="green" wire:confirm="Konfirmasi pengembalian barang ini?">
                                        Kembalikan
                                    </flux:button>
                                @else
                                    <span class="text-[10px] text-zinc-400 italic">Selesai:
                                        {{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d M Y') : '-' }}
                                    </span>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-10 text-zinc-500 italic">
                            Tidak ada data peminjaman aset yang ditemukan.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $loans->links() }}
        </div>
    </flux:card>

    {{-- MODAL PEMINJAMAN BARU --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[500px]">
        <div class="space-y-6">
            <flux:heading size="lg">Catat Peminjaman Baru</flux:heading>

            {{-- PENCARIAN ASET YANG DIPINJAM --}}
            <div class="relative">
                <flux:label>Pilih Aset (Tersedia)</flux:label>
                @if(!$selectedAsset)
                    <flux:input wire:model.live.debounce.300ms="search_asset" icon="magnifying-glass" placeholder="Ketik nama aset atau serial number..." />

                    @if(count($availableAssets) > 0)
                        <div class="absolute z-50 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-xl mt-1 overflow-hidden">
                            @foreach($availableAssets as $asset)
                                <button type="button" wire:click="selectAsset({{ $asset->id }})" class="w-full text-left p-3 hover:bg-zinc-50 dark:hover:bg-zinc-700 border-b border-zinc-100 dark:border-zinc-700">
                                    <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $asset->itemInfo->name ?? '-' }}</p>
                                    <p class="text-[10px] text-zinc-500 font-mono">SN: {{ $asset->serial_number ?? 'Tanpa SN' }}</p>
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="p-3 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-zinc-800 dark:text-zinc-200">{{ $selectedAsset['name'] }}</p>
                            <p class="text-[10px] text-zinc-500 font-mono">SN: {{ $selectedAsset['sn'] }}</p>
                        </div>
                        <button type="button" wire:click="removeSelectedAsset" class="text-red-500 hover:bg-red-50 p-1 rounded-md">
                            <flux:icon name="x-mark" size="sm" />
                        </button>
                    </div>
                @endif
            </div>

            {{-- PENCARIAN PEMINJAM --}}
            <div class="relative">
                <flux:label>Peminjam</flux:label>
                @if(!$selectedUser)
                    <flux:input wire:model.live.debounce.300ms="search_user" icon="magnifying-glass" placeholder="Ketik nama peminjam..." />

                    @if(count($availableUsers) > 0)
                        <div class="absolute z-50 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-xl mt-1 overflow-hidden">
                            @foreach($availableUsers as $user)
                                <button type="button" wire:click="selectUser({{ $user->id }})" class="w-full text-left p-3 hover:bg-zinc-50 dark:hover:bg-zinc-700 border-b border-zinc-100 dark:border-zinc-700">
                                    <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</p>
                                    <p class="text-[10px] text-zinc-400">
                                        {{ strtoupper($user->role) }}
                                        @if(strtolower($user->role) === 'siswa' && optional($user->class)->name)
                                            • {{ $user->class->name }}
                                        @endif
                                    </p>
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="p-3 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl flex items-center justify-between">
                        <p class="text-sm font-bold text-zinc-800 dark:text-zinc-200">{{ $selectedUser }}</p>
                        <button type="button" wire:click="removeSelectedUser" class="text-red-500 hover:bg-red-50 p-1 rounded-md">
                            <flux:icon name="x-mark" size="sm" />
                        </button>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input type="date" label="Tgl Pinjam" wire:model="loan_date" />
                <flux:input type="date" label="Tenggat" wire:model="due_date" />
            </div>

            <flux:textarea label="Catatan" wire:model="notes" />

            <div class="flex gap-2 justify-end pt-2">
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)">Batal</flux:button>
                <flux:button wire:click="store" variant="primary">Simpan Pinjaman</flux:button>
            </div>
        </div>
    </flux:modal>
</div>