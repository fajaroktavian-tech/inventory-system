<div class="p-6">
    <flux:heading size="xl">Peminjaman Aset</flux:heading>
    <flux:subheading>Kelola sirkulasi peminjaman dan pengembalian aset sekolah.</flux:subheading>

    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari peminjam..." class="max-w-xs" />
        <flux:button variant="primary" icon="plus" wire:click="create">Catat Peminjaman</flux:button>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">{{ session('message') }}</div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Peminjam</flux:table.column>
            <flux:table.column>Aset / SN</flux:table.column>
            <flux:table.column>Tgl Pinjam</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column align="end">Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($loans as $loan)
                <flux:table.row :key="$loan->id">
                    <flux:table.cell>
                        <p class="font-medium">{{ $loan->user->name }}</p>
                        <p class="text-[10px] text-zinc-500">{{ strtoupper($loan->user->role) }}</p>
                    </flux:table.cell>
                    <flux:table.cell>
                        <p class="text-sm font-medium">{{ $loan->asset->itemInfo->name ?? 'Aset Terhapus' }}</p>
                        <p class="text-[10px] text-zinc-500 italic">SN: {{ $loan->asset->serial_number ?? '-' }}</p>
                    </flux:table.cell>
                    <flux:table.cell>
                        <p class="text-sm">{{ \Carbon\Carbon::parse($loan->loan_date)->format('d M Y') }}</p>
                        @if($loan->due_date && $loan->status === 'active')
                            <p class="text-[10px] text-red-500 font-medium">Tempo:
                                {{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}</p>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="{{ $loan->status === 'active' ? 'blue' : 'green' }}" variant="subtle">
                            {{ $loan->status === 'active' ? 'Dipinjam' : 'Kembali' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex justify-end gap-2">
                            @if($loan->status === 'active')
                                <flux:button size="sm" variant="filled" wire:click="returnAsset({{ $loan->id }})" color="green"
                                    wire:confirm="Konfirmasi pengembalian barang ini?">
                                    Kembalikan
                                </flux:button>
                            @else
                                <span class="text-[10px] text-zinc-400 italic">Selesai:
                                    {{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d M Y') : '-' }}</span>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-4">
        {{ $loans->links() }}
    </div>

    {{-- MODAL PEMINJAMAN --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[500px]">
        <div class="space-y-6">
            <flux:heading size="lg">Catat Peminjaman Baru</flux:heading>

            {{-- BAGIAN CARI ASET --}}
            <div class="relative">
                <flux:label>Cari Aset</flux:label>
                @if(!$selectedAsset)
                    <flux:input wire:model.live.debounce.300ms="search_asset" icon="magnifying-glass"
                        placeholder="Ketik nama aset atau No. Seri..." />

                    @if(count($availableAssets) > 0)
                        <div
                            class="absolute z-50 w-full bg-white border border-zinc-200 rounded-xl shadow-xl mt-1 overflow-hidden">
                            @foreach($availableAssets as $asset)
                                <button wire:click="selectAsset({{ $asset->id }})"
                                    class="w-full text-left p-3 hover:bg-zinc-50 flex justify-between items-center border-b border-zinc-50 last:border-0">
                                    <div>
                                        <p class="text-sm font-bold">{{ $asset->itemInfo->name }}</p>
                                        <p class="text-[10px] text-zinc-400 font-medium">SN: {{ $asset->serial_number ?? '-' }}</p>
                                    </div>
                                    <flux:icon name="plus-circle" class="text-blue-600 size-5" />
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    {{-- TAMPILAN JIKA ASET SUDAH DIPILIH --}}
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-blue-900">{{ $selectedAsset['name'] }}</p>
                            <p class="text-[10px] text-blue-700">SN: {{ $selectedAsset['sn'] ?? '-' }}</p>
                        </div>
                        <button wire:click="removeSelectedAsset" class="text-red-500 hover:bg-red-50 p-1 rounded-md">
                            <flux:icon name="x-mark" size="sm" />
                        </button>
                    </div>
                @endif
            </div>

            <div class="relative">
    <flux:label>Peminjam</flux:label>
    
    @if(!$selectedUser)
        <flux:input wire:model.live.debounce.300ms="search_user" icon="magnifying-glass"
            placeholder="Ketik nama peminjam..." />

        @if(count($availableUsers) > 0)
            <div class="absolute z-50 w-full bg-white border border-zinc-200 rounded-xl shadow-xl mt-1 overflow-hidden">
                @foreach($availableUsers as $user)
                    <button type="button" wire:click="selectUser({{ $user->id }})"
                        class="w-full text-left p-3 hover:bg-zinc-50 border-b border-zinc-50">
                        <p class="text-sm font-bold">{{ $user->name }}</p>
                        <p class="text-[10px] text-zinc-400">{{ strtoupper($user->role) }}</p>
                    </button>
                @endforeach
            </div>
        @endif
    @else
        {{-- TAMPILAN JIKA USER SUDAH DIPILIH --}}
        <div class="p-3 bg-zinc-100 border border-zinc-200 rounded-xl flex items-center justify-between">
            <p class="text-sm font-bold text-zinc-800">{{ $selectedUser }}</p>
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

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)">Batal</flux:button>
                <flux:button wire:click="store" variant="primary">Simpan Pinjaman</flux:button>
            </div>
        </div>
    </flux:modal>
</div>