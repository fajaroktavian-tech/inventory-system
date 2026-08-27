<div class="py-6 max-w-7xl mx-auto px-4 lg:px-8 space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <flux:heading size="xl">Laporan Sarana & Prasarana</flux:heading>
            <flux:subheading>Rekapitulasi data pengadaan barang dan riwayat pemeliharaan aset sekolah.</flux:subheading>
        </div>

        <!-- Tombol Ekspor -->
        <div class="flex items-center gap-2">
            <flux:button wire:click="exportExcel" icon="document-arrow-down" variant="subtle">Ekspor Excel</flux:button>
            <flux:button wire:click="exportPdf" icon="printer" variant="primary">Cetak PDF</flux:button>
        </div>
    </div>

    <!-- Filter & Kontrol Laporan -->
    <flux:card class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Pilihan Jenis Laporan -->
            <div>
                <flux:select wire:model.live="reportType" label="Jenis Laporan">
                    <option value="procurement">Pengadaan Barang (Baru)</option>
                    <option value="maintenance">Perbaikan / Pemeliharaan Aset</option>
                </flux:select>
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <flux:input type="date" wire:model.live="startDate" label="Dari Tanggal" />
            </div>

            <!-- Tanggal Selesai -->
            <div>
                <flux:input type="date" wire:model.live="endDate" label="Sampai Tanggal" />
            </div>
        </div>
    </flux:card>

    <!-- Kartu Statistik Ringkasan -->
    <!-- Kartu Statistik Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Data -->
        <flux:card class="p-4 flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-zinc-500">Total Periode Ini</div>
                <div class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">{{ $totalCount }}</div>
            </div>
            <flux:icon name="document-text" class="w-8 h-8 text-zinc-400" />
        </flux:card>

        <!-- Pending -->
        <flux:card class="p-4 flex items-center justify-between border-l-4 border-amber-500">
            <div>
                <div class="text-sm font-medium text-zinc-500">Pending / Menunggu</div>
                <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $pendingCount }}</div>
            </div>
            <flux:icon name="clock" class="w-8 h-8 text-amber-500" />
        </flux:card>

        <!-- Disetujui / Selesai -->
        <flux:card class="p-4 flex items-center justify-between border-l-4 border-emerald-500">
            <div>
                <div class="text-sm font-medium text-zinc-500">Disetujui / Selesai</div>
                <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $approvedCount }}</div>
            </div>
            <flux:icon name="check-circle" class="w-8 h-8 text-emerald-500" />
        </flux:card>

        <!-- Ditolak -->
        <flux:card class="p-4 flex items-center justify-between border-l-4 border-rose-500">
            <div>
                <div class="text-sm font-medium text-zinc-500">Ditolak</div>
                <div class="text-2xl font-bold text-rose-600 dark:text-rose-400">{{ $rejectedCount }}</div>
            </div>
            <flux:icon name="x-circle" class="w-8 h-8 text-rose-500" />
        </flux:card>
    </div>

    @if($reportType === 'procurement')
    <!-- Kartu Tambahan khusus Pengadaan (Estimasi Anggaran) -->
    <flux:card class="p-4 flex items-center justify-between bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800">
        <div>
            <div class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Total Estimasi Anggaran Pengadaan</div>
            <div class="text-2xl font-bold text-emerald-800 dark:text-emerald-200">Rp {{ number_format($totalCost, 0, ',', '.') }}</div>
        </div>
        <flux:icon name="currency-dollar" class="w-10 h-10 text-emerald-600" />
    </flux:card>
    @endif

    <!-- Tabel Data Laporan -->
    <flux:card class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700 text-zinc-500 bg-zinc-50 dark:bg-zinc-900/50">
                    <th class="p-3 font-semibold">Tanggal</th>
                    <th class="p-3 font-semibold">Pemohon</th>
                    @if($reportType === 'procurement')
                        <th class="p-3 font-semibold">Nama Barang</th>
                        <th class="p-3 font-semibold">Jml</th>
                        <th class="p-3 font-semibold">Estimasi Biaya</th>
                    @else
                        <th class="p-3 font-semibold">Aset & Ruangan</th>
                        <th class="p-3 font-semibold">Kendala / Kerusakan</th>
                    @endif
                    <th class="p-3 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($reportData as $row)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/30 transition">
                        <td class="p-3 text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                            {{ $row->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="p-3 font-medium text-zinc-800 dark:text-zinc-200">
                            {{ $row->user->name ?? 'User Dihapus' }}
                        </td>
                        
                        @if($reportType === 'procurement')
                            <td class="p-3 font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $row->item_name }} <span class="text-xs font-normal text-zinc-500">({{ ucfirst($row->type) }})</span>
                            </td>
                            <td class="p-3 text-zinc-600 dark:text-zinc-400">{{ $row->qty }}</td>
                            <td class="p-3 text-zinc-600 dark:text-zinc-400">Rp {{ number_format($row->estimated_price * $row->qty, 0, ',', '.') }}</td>
                        @else
                            <td class="p-3">
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $row->asset->itemInfo->name ?? 'Aset Tidak Ditemukan' }}</div>
                                <div class="text-xs text-zinc-500">S/N: {{ $row->asset->serial_number ?? '-' }} | Ruang: {{ $row->asset->room->name ?? '-' }}</div>
                            </td>
                            <td class="p-3 text-zinc-600 dark:text-zinc-400 max-w-xs truncate">{{ $row->damage_description }}</td>
                        @endif

                        <td class="p-3 whitespace-nowrap">
                            @if($row->status === 'approved' || $row->status === 'repaired')
                                <flux:badge color="green" size="sm">{{ ucwords(str_replace('_', ' ', $row->status)) }}</flux:badge>
                            @elseif($row->status === 'rejected')
                                <flux:badge color="red" size="sm">Ditolak</flux:badge>
                            @else
                                <flux:badge color="amber" size="sm">Pending</flux:badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-zinc-500 italic">
                            Tidak ada data laporan untuk rentang tanggal yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </flux:card>
</div>