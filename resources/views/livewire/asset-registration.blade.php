<div class="p-6">
    <flux:heading size="xl">Registrasi Unit Aset</flux:heading>
    <flux:subheading>Mendaftarkan unit fisik ke dalam ruangan dan penanggung jawab.</flux:subheading>

    <div class="flex justify-between mt-8 mb-4">
        <div class="flex flex-wrap items-end gap-3 flex-1">
            <div class="md:w-40">
                <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari Nama atau SN..." />
            </div>
            {{-- FILTER RUANGAN (SEARCHABLE) --}}
            <div class="w-40">
                @if(!$filterRoomName)
                    <flux:input wire:model.live.debounce.300ms="search_filter_room" placeholder="Cari Ruangan..."
                        size="l" />

                    @if(count($filteredFilterRooms) > 0)
                        <div
                            class="absolute z-50 w-full bg-white border border-zinc-200 rounded-xl shadow-xl mt-1 max-h-48 overflow-y-auto">
                            @foreach($filteredFilterRooms as $room)
                                <button type="button" wire:click="selectFilterRoom({{ $room->id }}, '{{ $room->name }}')"
                                    class="w-full text-left p-2 hover:bg-zinc-50 border-b border-zinc-50 text-sm">
                                    {{ $room->name }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="flex items-center justify-between p-1.5 bg-blue-50 border border-blue-200 rounded-lg">
                        <span class="text-xs font-medium text-blue-800 truncate">{{ $filterRoomName }}</span>
                        <button wire:click="$set('filterRoom', ''); $set('filterRoomName', null)"
                            class="text-blue-500 hover:text-red-500">
                            <flux:icon name="x-mark" size="xs" />
                        </button>
                    </div>
                @endif
            </div>

            <div class="w-40">
                <flux:select wire:model.live="filterCondition" placeholder="Kondisi">
                    <flux:select.option value="">Semua Kondisi</flux:select.option>
                    <flux:select.option value="baik">Baik</flux:select.option>
                    <flux:select.option value="rusak_ringan">Rusak Ringan</flux:select.option>
                    <flux:select.option value="rusak_berat">Rusak Berat</flux:select.option>
                </flux:select>
            </div>

            @if($filterRoom || $filterCondition || $search)
                <flux:button variant="ghost" size="sm"
                    wire:click="$set('filterRoom', ''); $set('filterCondition', ''); $set('search', '')" class="mb-1">
                    Reset
                </flux:button>
            @endif
        </div>

        {{-- Kelompok Kanan: Tombol Aksi Utama --}}
        <div class="flex items-center gap-2">
            {{-- Dropdown Export --}}
            <flux:dropdown>
                <flux:button variant="filled" icon="arrow-down-tray">Export</flux:button>

                <flux:menu>
                    <flux:menu.item wire:click="exportExcel" icon="table-cells">Excel (.xlsx)</flux:menu.item>
                    <flux:menu.item wire:click="exportPdf" icon="document-text">PDF (.pdf)</flux:menu.item>
                </flux:menu>
            </flux:dropdown>

            <flux:button variant="filled" icon="printer" x-on:click="$dispatch('trigger-print-all')">
                Cetak Label
            </flux:button>

            <div class="flex gap-2">
                <flux:button icon="question-mark-circle" wire:click="$set('isAssetGuideOpen', true)" variant="filled">
                    Panduan</flux:button>
                <flux:button variant="primary" icon="plus" wire:click="create">Input Aset</flux:button>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg">{{ session('message') }}</div>
    @endif

    <flux:table>
        <flux:table.columns>
            <!-- <flux:table.column>QR</flux:table.column> -->
            <flux:table.column>Barang / SN</flux:table.column>
            <flux:table.column>Lokasi & PIC</flux:table.column>
            <flux:table.column>Kondisi</flux:table.column>
            <flux:table.column>Sumber Dana</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach($assets as $asset)
                <flux:table.row :key="$asset->id">
                    <!-- <flux:table.cell>
                                                                                            <div
                                                                                                class="bg-white p-1 inline-block rounded border shadow-sm cursor-pointer hover:scale-110 transition-transform">
                                                                                                {!! $asset->getQrCode() !!}
                                                                                            </div>
                                                                                        </flux:table.cell> -->
                    <flux:table.cell>
                        <p class="font-medium">{{ $asset->itemInfo->name }}</p>
                        <p class="text-xs text-zinc-500 italic">SN: {{ $asset->serial_number ?? 'N/A' }}</p>
                        <p class="text-[10px] text-blue-600">Masuk: {{ $asset->created_at->format('d M Y') }}</p>
                    </flux:table.cell>
                    <flux:table.cell>
                        <p class="text-sm">{{ $asset->room->name }}</p>
                        <p class="text-[10px] text-zinc-500">PIC: {{ $asset->pic->name }}</p>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="{{ $asset->condition == 'baik' ? 'green' : 'orange' }}">
                            {{ strtoupper(str_replace('_', ' ', $asset->condition)) }}
                        </flux:badge>
                        @php
                            $statusColor = [
                                'tersedia' => 'green',
                                'dipinjam' => 'blue',
                                'hilang' => 'red',
                                'diserahkan' => 'zinc'
                            ][$asset->status] ?? 'zinc';
                        @endphp
                        <flux:badge size="sm" variant="outline" color="{{ $statusColor }}">
                            {{ strtoupper($asset->status) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <p class="text-sm">{{ $asset->source_fund }}</p>
                        <p class="text-[10px] text-zinc-400">{{ $asset->acquisition_year }}</p>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex justify gap-2">
                            <flux:button variant="filled" size="sm" icon="pencil-square" wire:click="edit({{ $asset->id }})"
                                style="background-color: #f59e0b; color: white;"></flux:button>

                            <flux:modal.trigger name="print-label-{{ $asset->id }}">
                                <flux:button variant="ghost" size="sm" icon="printer"></flux:button>
                            </flux:modal.trigger>

                            <flux:button variant="ghost" size="sm" icon="eye" wire:click="showDetail({{ $asset->id }})">
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
    <div class="mt-4">
        {{ $assets->links() }}
    </div>

    {{-- MODAL FORM --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[600px]">
        <form wire:submit="store" class="space-y-4">
            <flux:heading size="lg">Registrasi Unit Aset</flux:heading>

            <div class="relative">
                <flux:label>Katalog Barang</flux:label>
                @if(!$selectedCatalogData)
                    <flux:input wire:model.live.debounce.300ms="search_catalog" icon="magnifying-glass"
                        placeholder="Cari nama barang atau brand..." />
                    @if(count($filteredCatalogs) > 0)
                        <div
                            class="absolute z-50 w-full bg-white border border-zinc-200 rounded-xl shadow-xl mt-1 max-h-48 overflow-y-auto">
                            @foreach($filteredCatalogs as $item)
                                <button type="button" wire:click="selectCatalog({{ $item->id }}, '{{ $item->name }}')"
                                    class="w-full text-left p-2 hover:bg-zinc-50 border-b border-zinc-50 text-sm">
                                    <b>{{ $item->name }}</b> <span class="text-xs text-zinc-400">({{ $item->brand }})</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="flex items-center justify-between p-2 bg-zinc-50 border rounded-lg">
                        <span class="text-sm font-medium">{{ $selectedCatalogData }}</span>
                        <flux:button variant="ghost" size="sm" icon="x-mark"
                            wire:click="$set('selectedCatalogData', null)" />
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="relative">
                    <flux:label>Lokasi Ruangan</flux:label>
                    @if(!$selectedRoomData)
                        <flux:input wire:model.live.debounce.300ms="search_room" placeholder="Cari Ruangan..." />
                        @if(count($filteredRooms) > 0)
                            <div
                                class="absolute z-50 w-full bg-white border border-zinc-200 rounded-xl shadow-xl mt-1 max-h-48 overflow-y-auto">
                                @foreach($filteredRooms as $room)
                                    <button type="button" wire:click="selectRoom({{ $room->id }}, '{{ $room->name }}')"
                                        class="w-full text-left p-2 hover:bg-zinc-50 border-b border-zinc-50 text-sm">
                                        {{ $room->name }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="flex items-center justify-between p-2 bg-zinc-50 border rounded-lg">
                            <span class="text-sm font-medium">{{ $selectedRoomData }}</span>
                            <flux:button variant="ghost" size="sm" icon="x-mark"
                                wire:click="$set('selectedRoomData', null)" />
                        </div>
                    @endif
                </div>

                <div class="relative">
                    <flux:label>Penanggung Jawab</flux:label>
                    @if(!$selectedPicData)
                        <flux:input wire:model.live.debounce.300ms="search_pic" placeholder="Cari Nama Guru/Staff..." />
                        @if(count($filteredPics) > 0)
                            <div
                                class="absolute z-50 w-full bg-white border border-zinc-200 rounded-xl shadow-xl mt-1 max-h-48 overflow-y-auto">
                                @foreach($filteredPics as $p)
                                    <button type="button" wire:click="selectPic({{ $p->id }}, '{{ $p->name }}')"
                                        class="w-full text-left p-2 hover:bg-zinc-50 border-b border-zinc-50 text-sm">
                                        {{ $p->name }} <span class="text-[10px] text-zinc-400">({{ $p->role }})</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="flex items-center justify-between p-2 bg-zinc-50 border rounded-lg">
                            <span class="text-sm font-medium">{{ $selectedPicData }}</span>
                            <flux:button variant="ghost" size="sm" icon="x-mark"
                                wire:click="$set('selectedPicData', null)" />
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input label="Nomor Seri (SN)" wire:model="serial_number" placeholder="Kosongkan untuk otomatis" />
                <flux:input label="Sumber Dana" wire:model="source_fund" placeholder="BOS / BOPD / Hibah" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input type="number" label="Tahun Perolehan" wire:model="acquisition_year" />
                <flux:input type="number" label="Harga (Rp)" wire:model="price" />
            </div>

            {{-- Letakkan di bawah select Kondisi Awal --}}
            <div class="grid grid-cols-2 gap-4">
                <flux:select label="Kondisi" wire:model="condition">
                    <flux:select.option value="baik">Baik</flux:select.option>
                    <flux:select.option value="rusak_ringan">Rusak Ringan</flux:select.option>
                    <flux:select.option value="rusak_berat">Rusak Berat</flux:select.option>
                </flux:select>

                <flux:select label="Status Keberadaan" wire:model="status">
                    <flux:select.option value="tersedia">Tersedia (Ada)</flux:select.option>
                    <flux:select.option value="dipinjam">Sedang Dipinjam</flux:select.option>
                    <flux:select.option value="hilang">Hilang</flux:select.option>
                    <flux:select.option value="diserahkan">Sudah Diserahkan</flux:select.option>
                </flux:select>
            </div>

            <div class="flex mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="$set('isModalOpen', false)" class="mr-2">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Registrasi</flux:button>
            </div>
        </form>
    </flux:modal>
    @foreach($assets as $asset)
        <flux:modal name="print-label-{{ $asset->id }}" class="md:w-[400px]">
            <div class="text-center p-4">
                <flux:heading size="lg" class="mb-4">Label Inventaris</flux:heading>

                {{-- Area Label yang akan diprint --}}
                <div id="print-area-{{ $asset->id }}" class="hidden">
                    <p class="header-school">SMKN 7 BALEENDAH</p>

                    {!! $asset->getQrCode() !!}

                    <p class="sn-text">{{ $asset->serial_number }}</p>
                    <p>{{ Str::limit($asset->itemInfo->name, 25) }}</p>
                    <p style="font-size: 5pt; font-weight: normal;">Tahun: {{ $asset->acquisition_year }}</p>
                </div>

                <div class="bg-white p-4 border rounded shadow-inner mb-4">
                    <div class="flex justify-center">{!! $asset->getQrCode() !!}</div>
                    <p class="font-bold mt-2 text-sm">{{ $asset->serial_number }}</p>
                    <p class="text-xs">{{ $asset->itemInfo->name }}</p>
                </div>

                <div class="mt-6 flex gap-2">
                    <flux:spacer />
                    <flux:button variant="primary"
                        x-on:click="$dispatch('trigger-print', { id: 'print-area-{{ $asset->id }}' })">
                        Cetak Thermal
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endforeach

    {{-- MODAL DETAIL ASET --}}
    <flux:modal name="detail-asset" class="md:w-[500px]">
        @if($selectedAsset)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Detail Unit Aset</flux:heading>
                    <flux:subheading>Informasi lengkap spesifikasi dan status unit.</flux:subheading>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="flex flex-col p-3 border rounded-lg bg-zinc-50 dark:bg-zinc-900">
                        <span class="text-zinc-500 text-xs">Nama Barang</span>
                        <span class="font-medium">{{ $selectedAsset->itemInfo->name }}</span>
                    </div>
                    <div class="flex flex-col p-3 border rounded-lg bg-zinc-50 dark:bg-zinc-900">
                        <span class="text-zinc-500 text-xs">Nomor Seri (SN)</span>
                        <span class="font-medium">{{ $selectedAsset->serial_number ?? 'Tidak ada SN' }}</span>
                    </div>
                </div>

                <flux:separator variant="subtle" />

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Lokasi</span>
                        <span class="font-medium">{{ $selectedAsset->room->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Penanggung Jawab</span>
                        <span class="font-medium">{{ $selectedAsset->pic->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Kondisi</span>
                        <flux:badge size="sm" color="{{ $selectedAsset->condition == 'baik' ? 'green' : 'orange' }}">
                            {{ strtoupper(str_replace('_', ' ', $selectedAsset->condition)) }}
                        </flux:badge>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Status Unit</span>
                        <flux:badge size="sm" color="{{ $statusColor }}" variant="solid">
                            {{ strtoupper($asset->status) }}
                        </flux:badge>
                    </div>
                </div>

                <flux:separator variant="subtle" />

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-zinc-500 block text-xs">Sumber Dana</span>
                        <span class="font-medium">{{ $selectedAsset->source_fund }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block text-xs">Harga Perolehan</span>
                        <span class="font-medium text-green-600">Rp
                            {{ number_format($selectedAsset->price, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block text-xs">Tahun Perolehan</span>
                        <span class="font-medium">{{ $selectedAsset->acquisition_year }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 block text-xs">Tanggal Input</span>
                        <span class="font-medium">{{ $selectedAsset->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>

                <div class="flex mt-6">
                    <flux:spacer />
                    <flux:button x-on:click="$flux.modal('detail-asset').close()">Tutup</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>