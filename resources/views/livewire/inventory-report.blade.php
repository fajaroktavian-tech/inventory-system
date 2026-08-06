<div class="p-6" x-data="{ activeTab: @entangle('activeTab') }">
    <div class="flex justify-between items-start mb-6">
        <div>
            <flux:heading size="xl" class="mb-2">Modul Laporan</flux:heading>
            <flux:subheading>Pantau stok, log keluar, dan statistik penggunaan barang secara real-time.
            </flux:subheading>
        </div>
        {{-- Dropdown Export --}}
        <flux:dropdown>
            <flux:button icon-trailing="chevron-down" variant="outline">
                <!-- <flux:icon name="document-arrow-down" variant="mini" class="mr-2" /> -->
                Eksport
            </flux:button>

            <flux:menu class="min-w-48">
                <flux:menu.item wire:click="exportPDF" icon="document-text">
                    Ekspor ke PDF (.pdf)
                </flux:menu.item>

                <flux:menu.item wire:click="exportExcel" icon="table-cells">
                    Ekspor ke Excel (.xlsx)
                </flux:menu.item>

                <flux:menu.separator />

                <flux:menu.item icon="printer" onclick="window.print()">
                    Cetak Halaman (Print)
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>

    {{-- CUSTOM TAB NAVIGATION (Pengganti flux:tabs yang error) --}}
    <div class="flex items-center p-1 bg-zinc-100 rounded-lg mb-6 w-fit">
        <button @click="activeTab = 'stok'"
            :class="activeTab === 'stok' ? 'bg-white shadow-sm text-zinc-800' : 'text-zinc-500 hover:text-zinc-700'"
            class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition-all">
            <flux:icon name="archive-box" variant="mini" />
            Stok Barang
        </button>
        <button @click="activeTab = 'keluar'"
            :class="activeTab === 'keluar' ? 'bg-white shadow-sm text-zinc-800' : 'text-zinc-500 hover:text-zinc-700'"
            class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition-all">
            <flux:icon name="arrow-up-tray" variant="mini" />
            Barang Keluar
        </button>
        <button @click="activeTab = 'statistik'"
            :class="activeTab === 'statistik' ? 'bg-white shadow-sm text-zinc-800' : 'text-zinc-500 hover:text-zinc-700'"
            class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition-all">
            <flux:icon name="chart-bar" variant="mini" />
            Statistik
        </button>
        <button @click="activeTab = 'rekap'"
            :class="activeTab === 'rekap' ? 'bg-white shadow-sm text-zinc-800' : 'text-zinc-500 hover:text-zinc-700'"
            class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition-all">
            <flux:icon name="clipboard-document-list" variant="mini" />
            Rekapitulasi
        </button>
    </div>

    <div class="mt-6">
        {{-- FR-21: LAPORAN STOK --}}
        <div x-show="activeTab === 'stok'" x-transition>
            <div class="space-y-4">
                {{-- Filter Bar Stok - Compact & Menyamping --}}
                {{-- Filter Bar Stok - Ultra Compact --}}
                <div class="flex flex-wrap items-end gap-2 bg-white p-2 rounded-xl border border-zinc-200 shadow-sm">

                    {{-- Search Nama Barang - Dibuat Fleksibel --}}
                    <div class="flex-1 min-w-[180px]">
                        <flux:input wire:model.live.debounce.300ms="searchStockItem" label="Cari Barang" size="sm"
                            placeholder="Ketik nama barang..." icon="magnifying-glass" />
                    </div>

                    {{-- Filter Kategori - Dibuat lebih ramping (w-44) --}}
                    <div class="w-40 md:w-44">
                        <flux:select wire:model.live="selectedStockCategory" label="Kategori" size="sm"
                            placeholder="Semua">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    {{-- Tombol Reset - Sejajar di samping --}}
                    <flux:button variant="ghost" icon="arrow-path" size="sm" class="mb-0.5"
                        wire:click="$set('searchStockItem', ''); $set('selectedStockCategory', '');"
                        x-tooltip="Reset Filter" />
                </div>

                {{-- Tabel Stok --}}
                <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column class="w-10">No</flux:table.column>
                            <flux:table.column>Nama Barang</flux:table.column>
                            <flux:table.column>Kategori</flux:table.column>
                            <flux:table.column>Stok</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse($inventoryStock as $index => $item)
                                <flux:table.row>
                                    <flux:table.cell class="text-zinc-400 text-xs font-mono">{{ $index + 1 }}
                                    </flux:table.cell>
                                    <flux:table.cell class="font-semibold text-zinc-900">{{ $item->name }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge size="sm" variant="subtle" color="zinc">
                                            {{ $item->category->name ?? 'Tanpa Kategori' }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <span
                                            class="font-mono font-bold {{ $item->stock <= $item->min_stock ? 'text-red-600' : 'text-zinc-700' }}">
                                            {{ $item->stock }}
                                        </span>
                                        <span class="text-xs text-zinc-400 uppercase">{{ $item->unit }}</span>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        @if($item->stock <= $item->min_stock)
                                            <flux:badge color="red" size="sm" icon="exclamation-triangle" inset="top bottom">
                                                Kritis</flux:badge>
                                        @else
                                            <flux:badge color="green" size="sm" icon="check" inset="top bottom">Aman
                                            </flux:badge>
                                        @endif
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="5" class="text-center py-10 text-zinc-400 italic">
                                        Barang tidak ditemukan.
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                    @if($inventoryStock->hasPages())
                        <div class="p-4 border-t bg-zinc-50/50">
                            {{ $inventoryStock->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- FR-22: LAPORAN BARANG KELUAR --}}
        <div x-show="activeTab === 'keluar'" x-transition>
            <div class="space-y-4">
                {{-- Filter Bar Horizontal Compact - Universal Search Version --}}
                <div class="flex flex-wrap items-end gap-3 bg-white p-3 rounded-xl border border-zinc-200 shadow-sm">

                    {{-- Universal Search: Mencakup Penerima, Barang, dan Kelas --}}
                    <div class="flex-1 min-w-[250px]">
                        <flux:input wire:model.live.debounce.300ms="searchOutbound" label="Pencarian Cepat" size="sm"
                            placeholder="Cari nama barang, siswa, atau kelas..." icon="magnifying-glass" clearable />
                    </div>

                    {{-- Filter Tanggal Mulai --}}
                    <div class="w-32 md:w-40">
                        <flux:input type="date" label="Mulai" size="sm" wire:model.live="startDate" />
                    </div>

                    {{-- Filter Tanggal Sampai --}}
                    <div class="w-32 md:w-40">
                        <flux:input type="date" label="Sampai" size="sm" wire:model.live="endDate" />
                    </div>

                    {{-- Tombol Reset --}}
                    <flux:button variant="ghost" icon="arrow-path" size="sm" class="mb-0.5"
                        wire:click="$set('searchOutbound', '');" x-tooltip="Reset Pencarian" />
                </div>
                {{-- Tabel Laporan --}}
                <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                    <flux:table>
                        <flux:table.columns>
                            {{-- Tambah Kolom No --}}
                            <flux:table.column>No</flux:table.column>
                            <flux:table.column>Tanggal</flux:table.column>
                            <flux:table.column>Penerima</flux:table.column>
                            <flux:table.column>Nama Barang</flux:table.column>
                            <flux:table.column>Jumlah Keluar</flux:table.column>
                            <flux:table.column>Catatan</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse($outboundLogs as $index => $log)
                                <flux:table.row>
                                    {{-- No Urut Sinkron dengan Pagination --}}
                                    <flux:table.cell class="text-zinc-400 text-xs font-mono">
                                        {{ $outboundLogs->firstItem() + $index }}
                                    </flux:table.cell>

                                    <flux:table.cell class="text-zinc-600 text-sm whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <flux:icon name="calendar" variant="mini" class="text-zinc-400" />
                                            {{ \Carbon\Carbon::parse($log->request->request_date)->format('d M Y') }}
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="flex flex-col">
                                            <span
                                                class="font-semibold text-zinc-900">{{ $log->request->student->name ?? 'User' }}</span>
                                            <span class="text-[10px] text-zinc-500 uppercase tracking-tighter leading-none">
                                                {{ $log->request->class->name ?? ($log->request->room->name ?? 'Staff') }}
                                            </span>
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-zinc-200"></div>
                                            <span class="text-zinc-800 font-medium">{{ $log->item->name }}</span>
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell align="center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $log->quantity_approved }} {{ $log->item->unit }}
                                        </span>
                                    </flux:table.cell>

                                    {{-- Isi Kolom Catatan --}}
                                    <flux:table.cell>
                                        <span class="text-xs text-zinc-600 dark:text-zinc-300 italic max-w-xs truncate block" title="{{ $log->request->notes ?? '' }}">
                                            {{ $log->request->notes ?: '-' }}
                                        </span>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="5" class="text-center py-12">
                                        <div class="flex flex-col items-center">
                                            <flux:icon name="magnifying-glass" class="mb-2 text-zinc-300" size="xl" />
                                            <span class="text-zinc-400 italic">Tidak ada data transaksi yang
                                                ditemukan.</span>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>

                    @if($outboundLogs->hasPages())
                        <div class="p-4 border-t bg-zinc-50/50">
                            {{ $outboundLogs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- FR-23: STATISTIK --}}
        <div x-show="activeTab === 'statistik'" x-transition>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Barang Terpopuler --}}
                <div class="bg-white p-6 border border-zinc-200 rounded-xl shadow-sm">
                    <div class="flex items-center gap-2 mb-6">
                        <flux:icon name="fire" class="text-orange-500" />
                        <flux:heading size="md">Barang Terlaris</flux:heading>
                    </div>
                    <div class="space-y-6">
                        @foreach($topItems as $top)
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-zinc-700">{{ $top->item->name }}</span>
                                    <span class="font-bold text-blue-600">{{ $top->total }} <span
                                            class="text-[10px] text-zinc-400 font-normal uppercase">{{ $top->item->unit }}</span></span>
                                </div>
                                <div class="w-full bg-zinc-100 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-500"
                                        style="width: {{ $topItems->max('total') > 0 ? ($top->total / $topItems->max('total')) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pengguna Teraktif (Perubahan di sini) --}}
                <div class="bg-white p-6 border border-zinc-200 rounded-xl shadow-sm">
                    <div class="flex items-center gap-2 mb-6">
                        <flux:icon name="user" class="text-green-500" />
                        <flux:heading size="md">Pengguna Teraktif</flux:heading>
                    </div>
                    <div class="space-y-6">
                        @forelse($topUsers as $top)
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-zinc-700">{{ $top->name }}</span>
                                    <span class="font-bold text-green-600">{{ $top->total }} <span
                                            class="text-[10px] text-zinc-400 font-normal uppercase">Permintaan</span></span>
                                </div>
                                <div class="w-full bg-zinc-100 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full transition-all duration-500"
                                        style="width: {{ $topUsers->max('total') > 0 ? ($top->total / $topUsers->max('total')) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-400 italic">Belum ada data aktivitas.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Kelas Teraktif --}}
                <div class="bg-white p-6 border border-zinc-200 rounded-xl shadow-sm">
                    <div class="flex items-center gap-2 mb-6">
                        <flux:icon name="user-group" class="text-green-500" />
                        <flux:heading size="md">Kelas Paling Aktif</flux:heading>
                    </div>
                    <div class="space-y-6">
                        @foreach($topClasses as $top)
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-zinc-700">{{ $top->name ?? 'Lainnya' }}</span>
                                    <span class="font-bold text-green-600">{{ $top->total }} <span
                                            class="text-[10px] text-zinc-400 font-normal uppercase">Transaksi</span></span>
                                </div>
                                <div class="w-full bg-zinc-100 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full transition-all duration-500"
                                        style="width: {{ $topClasses->max('total') > 0 ? ($top->total / $topClasses->max('total')) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        {{-- FR-24: REKAPITULASI MUTASI --}}
        <div x-show="activeTab === 'rekap'" x-transition>
            <div class="space-y-4">

                {{-- Search Bar khusus Rekap --}}
                <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-zinc-200 shadow-sm">
                    <div class="flex-1">
                        <flux:input wire:model.live.debounce.300ms="searchRekap" icon="magnifying-glass"
                            placeholder="Cari nama barang di rekapitulasi..." size="sm" clearable />
                    </div>
                </div>

                {{-- Tabel Rekap --}}
                <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Nama Barang</flux:table.column>
                            <flux:table.column>Kategori</flux:table.column>
                            <flux:table.column class="text-center">Total Masuk</flux:table.column>
                            <flux:table.column class="text-center">Total Keluar</flux:table.column>
                            <flux:table.column>Stok Akhir</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse($summaryReport as $item)
                                <flux:table.row>
                                    <flux:table.cell class="font-semibold text-zinc-900">{{ $item->name }}</flux:table.cell>
                                    <flux:table.cell>
                                        <span class="text-xs text-zinc-500">{{ $item->category->name ?? '-' }}</span>
                                    </flux:table.cell>

                                    <flux:table.cell align="center">
                                        <span class="text-green-600 font-bold font-mono">
                                            +{{ $item->total_masuk ?? 0 }}
                                        </span>
                                    </flux:table.cell>

                                    <flux:table.cell align="center">
                                        <span class="text-red-500 font-bold font-mono">
                                            -{{ $item->total_keluar ?? 0 }}
                                        </span>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="flex flex-col">
                                            <span class="font-black text-zinc-800">{{ $item->stock }}
                                                {{ $item->unit }}</span>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="5" class="text-center py-10 text-zinc-400 italic">
                                        Barang tidak ditemukan dalam pencarian rekap.
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>

                    {{-- Pagination khusus Rekap --}}
                    @if($summaryReport->hasPages())
                        <div class="p-4 border-t bg-zinc-50/50">
                            {{ $summaryReport->links() }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-4 p-4 bg-zinc-50 rounded-xl border border-dashed border-zinc-300">
                <p class="text-xs text-zinc-500 leading-relaxed">
                    <strong>Keterangan:</strong> Total masuk dihitung dari keseluruhan histori pengadaan barang,
                    sedangkan total keluar dihitung dari permintaan yang telah disetujui (Approved).
                </p>
            </div>
        </div>
    </div>
</div>