<div class="p-6 lg:p-8 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Monitoring Inventaris Aset</flux:heading>
            <flux:subheading>Kelola dan pantau seluruh status fisik aset satuan pendidikan.</flux:subheading>
        </div>
    </div>

    {{-- KARTU STATISTIK RINGKASAN --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <flux:card class="p-4">
            <flux:text class="text-zinc-500 uppercase text-xs font-bold">Total Aset</flux:text>
            <div class="text-2xl font-black mt-1">{{ $stats['total'] }}</div>
        </flux:card>
        <flux:card class="p-4 border-l-4 border-green-500">
            <flux:text class="text-zinc-500 uppercase text-xs font-bold">Tersedia</flux:text>
            <div class="text-2xl font-black text-green-600 mt-1">{{ $stats['tersedia'] }}</div>
        </flux:card>
        <flux:card class="p-4 border-l-4 border-blue-500">
            <flux:text class="text-zinc-500 uppercase text-xs font-bold">Dipinjam</flux:text>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $stats['dipinjam'] }}</div>
        </flux:card>
        <flux:card class="p-4 border-l-4 border-purple-500">
            <flux:text class="text-zinc-500 uppercase text-xs font-bold">Diserahkan</flux:text>
            <div class="text-2xl font-black text-purple-600 mt-1">{{ $stats['diserahkan'] }}</div>
        </flux:card>
        <flux:card class="p-4 border-l-4 border-red-500">
            <flux:text class="text-zinc-500 uppercase text-xs font-bold">Hilang</flux:text>
            <div class="text-2xl font-black text-red-600 mt-1">{{ $stats['hilang'] }}</div>
        </flux:card>
    </div>

    {{-- FILTER & PENCARIAN --}}
    <flux:card>
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <div class="flex flex-wrap flex-1 gap-3 w-full">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama barang atau serial number..." class="max-w-xs flex-1" />
                <flux:dropdown>
            <flux:button icon-trailing="chevron-down" variant="outline">
                Eksport
            </flux:button>
            <flux:menu class="min-w-48">
                <flux:menu.item wire:click="exportPdf" icon="document-text">
                    Ekspor ke PDF (.pdf)
                </flux:menu.item>
                <flux:menu.separator />
                <flux:menu.item icon="printer" onclick="window.print()">
                    Cetak Halaman (Print)
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
                
                <flux:select wire:model.live="filterStatus" class="max-w-[160px]">
                    <option value="">Semua Status</option>
                    <option value="tersedia">Tersedia</option>
                    <option value="dipinjam">Dipinjam</option>
                    <option value="diserahkan">Diserahkan</option>
                    <option value="hilang">Hilang</option>
                </flux:select>

                <flux:select wire:model.live="filterCondition" class="max-w-[160px]">
                    <option value="">Semua Kondisi</option>
                    <option value="baik">Baik</option>
                    <option value="rusak_ringan">Rusak Ringan</option>
                    <option value="rusak_berat">Rusak Berat</option>
                </flux:select>
            </div>
        </div>

        {{-- TABEL DATA ASET --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Nama Aset / SN</flux:table.column>
                <flux:table.column>Lokasi Ruangan</flux:table.column>
                <flux:table.column>Penanggung Jawab (PIC)</flux:table.column>
                <flux:table.column>Peminjam / Pemegang</flux:table.column>
                <flux:table.column>Kondisi</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($assets as $asset)
                    <flux:table.row :key="$asset->id">
                        <flux:table.cell>
                            <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $asset->itemInfo->name ?? '-' }}</div>
                            <div class="text-xs text-zinc-500 font-mono">SN: {{ $asset->serial_number ?? 'Tanpa SN' }}</div>
                        </flux:table.cell>

                        <flux:table.cell>{{ $asset->room->name ?? '-' }}</flux:table.cell>

                        <flux:table.cell>{{ $asset->pic->name ?? '-' }}</flux:table.cell>

                        <flux:table.cell>
                            <span class="text-zinc-800 dark:text-zinc-200 font-medium">
                                {{ $asset->activeLoan->user->name ?? '-' }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            @php
                                $condColor = match($asset->condition) {
                                    'baik' => 'green',
                                    'rusak_ringan' => 'amber',
                                    'rusak_berat' => 'red',
                                    default => 'zinc'
                                };
                            @endphp
                            <flux:badge :color="$condColor" size="sm">{{ ucwords(str_replace('_', ' ', $asset->condition)) }}</flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            @php
                                $statusColor = match($asset->status) {
                                    'tersedia' => 'green',
                                    'dipinjam' => 'blue',
                                    'diserahkan' => 'purple',
                                    'hilang' => 'red',
                                    default => 'zinc'
                                };
                            @endphp
                            <flux:badge :color="$statusColor" size="sm" inset="right">{{ strtoupper($asset->status) }}</flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="openEditModal({{ $asset->id }})" />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-10 text-zinc-500 italic">
                            Tidak ada data aset yang ditemukan.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $assets->links() }}
        </div>
    </flux:card>

    {{-- MODAL EDIT STATUS & LOKASI ASET --}}
    <flux:modal wire:model="isEditModalOpen" class="md:w-[450px]">
        <form wire:submit="updateAsset" class="space-y-4">
            <flux:heading size="lg">Perbarui Status & Lokasi Aset</flux:heading>

            <flux:select label="Status Aset" wire:model="newStatus">
                <option value="tersedia">Tersedia</option>
                <option value="dipinjam">Dipinjam</option>
                <option value="diserahkan">Diserahkan</option>
                <option value="hilang">Hilang</option>
            </flux:select>

            <flux:select label="Kondisi Fisik" wire:model="newCondition">
                <option value="baik">Baik</option>
                <option value="rusak_ringan">Rusak Ringan</option>
                <option value="rusak_berat">Rusak Berat</option>
            </flux:select>

            <flux:select label="Lokasi Ruangan" wire:model="newRoomId">
                @foreach($rooms as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                @endforeach
            </flux:select>

            <flux:select label="Penanggung Jawab (PIC)" wire:model="newPicId">
                @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                @endforeach
            </flux:select>

            <div class="flex gap-2 justify-end pt-2">
                <flux:button wire:click="$set('isEditModalOpen', false)" variant="ghost">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Perubahan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>