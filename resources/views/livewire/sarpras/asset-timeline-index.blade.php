<div class="py-6 max-w-7xl mx-auto px-4 lg:px-8 space-y-6">
    <div>
        <flux:heading size="xl">Siklus Hidup Aset (Audit Trail)</flux:heading>
        <flux:subheading>Pilih unit aset di bawah untuk melihat rekam jejak lengkap, riwayat peminjaman, dan perbaikan.</flux:subheading>
    </div>

    <!-- Kotak Pencarian -->
    <flux:card>
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari berdasarkan Nama Barang, No. Seri, atau Ruangan..." />
    </flux:card>

    <!-- Tabel Daftar Aset -->
    <flux:card class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700 text-zinc-500 bg-zinc-50 dark:bg-zinc-900/50">
                    <th class="p-3 font-semibold">No. Seri / Token</th>
                    <th class="p-3 font-semibold">Nama Barang</th>
                    <th class="p-3 font-semibold">Ruangan Saat Ini</th>
                    <th class="p-3 font-semibold">Kondisi</th>
                    <th class="p-3 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($assets as $asset)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/35 transition">
                        <td class="p-3 font-mono text-zinc-700 dark:text-zinc-300">
                            {{ $asset->serial_number ?? '-' }}
                        </td>
                        <td class="p-3 font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $asset->itemInfo->name ?? 'Katalog Dihapus' }}
                            <div class="text-xs text-zinc-500 font-normal">Merek: {{ $asset->itemInfo->brand ?? '-' }}</div>
                        </td>
                        <td class="p-3 text-zinc-600 dark:text-zinc-400">
                            {{ $asset->room->name ?? '-' }}
                        </td>
                        <td class="p-3">
                            <flux:badge color="{{ $asset->condition === 'baik' ? 'green' : ($asset->condition === 'rusak_ringan' ? 'amber' : 'red') }}" size="sm">
                                {{ ucwords(str_replace('_', ' ', $asset->condition)) }}
                            </flux:badge>
                        </td>
                        <td class="p-3 text-center">
                            <flux:button :href="route('assets.timeline.show', $asset->id)" icon="clock" size="sm" variant="primary" wire:navigate>
                                Lihat Riwayat
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-zinc-500 italic">
                            Tidak ada data unit aset ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $assets->links() }}
        </div>
    </flux:card>
</div>