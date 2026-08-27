<div class="py-6 max-w-5xl mx-auto px-4 lg:px-8 space-y-6">
    <!-- Navigasi Kembali -->
    <div>
        <flux:button :href="route('assets.timeline.index')" icon="arrow-left" variant="subtle" wire:navigate>
            Kembali ke Daftar Aset
        </flux:button>
    </div>

    <!-- Informasi Ringkas Unit Aset -->
    <flux:card class="space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <flux:badge size="sm" color="zinc" class="mb-1">{{ $asset->itemInfo->category->name ?? 'Kategori' }}</flux:badge>
                <flux:heading size="xl">{{ $asset->itemInfo->name ?? 'Nama Aset' }}</flux:heading>
                <flux:subheading>Nomor Seri: <span class="font-mono font-semibold text-zinc-800 dark:text-zinc-200">{{ $asset->serial_number ?? 'Tidak ada S/N' }}</span></flux:subheading>
            </div>
            <div class="flex items-center gap-2">
                <flux:badge color="{{ $asset->condition === 'baik' ? 'green' : 'red' }}">
                    Kondisi: {{ ucwords(str_replace('_', ' ', $asset->condition)) }}
                </flux:badge>
                <flux:badge color="zinc">
                    Status: {{ ucwords($asset->status) }}
                </flux:badge>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-zinc-200 dark:border-zinc-700 text-sm">
            <div>
                <span class="text-zinc-500 block">Ruangan Saat Ini</span>
                <span class="font-medium text-zinc-800 dark:text-zinc-100">{{ $asset->room->name ?? '-' }}</span>
            </div>
            <div>
                <span class="text-zinc-500 block">Penanggung Jawab (PIC)</span>
                <span class="font-medium text-zinc-800 dark:text-zinc-100">{{ $asset->pic->name ?? '-' }}</span>
            </div>
            <div>
                <span class="text-zinc-500 block">Tahun Perolehan</span>
                <span class="font-medium text-zinc-800 dark:text-zinc-100">{{ $asset->acquisition_year ?? '-' }}</span>
            </div>
            <div>
                <span class="text-zinc-500 block">Nilai Aset</span>
                <span class="font-medium text-zinc-800 dark:text-zinc-100">Rp {{ number_format($asset->price, 0, ',', '.') }}</span>
            </div>
        </div>
    </flux:card>

    <!-- Garis Waktu (Timeline Audit Trail) -->
    <flux:card class="space-y-6">
        <flux:heading size="lg">Rekam Jejak & Siklus Hidup Aset</flux:heading>

        <div class="relative border-l-2 border-zinc-200 dark:border-zinc-700 ml-4 space-y-8 py-2">
            @forelse($timeline as $item)
                <div class="relative pl-6">
                    <!-- Titik Ikon Timeline -->
                    <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-600 flex items-center justify-center">
                        <flux:icon name="{{ $item['icon'] }}" class="w-4 h-4 text-{{ $item['color'] }}-500" />
                    </div>

                    <!-- Konten Timeline -->
                    <div class="bg-zinc-50 dark:bg-zinc-900/40 p-4 rounded-xl border border-zinc-200/80 dark:border-zinc-700/60 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $item['title'] }}</span>
                            <span class="text-xs text-zinc-500 font-mono">{{ $item['date'] ? $item['date']->format('d M Y, H:i') : '-' }}</span>
                        </div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $item['description'] }}</p>
                    </div>
                </div>
            @empty
                <div class="pl-6 text-zinc-500 italic">Belum ada riwayat aktivitas untuk aset ini.</div>
            @endforelse
        </div>
    </flux:card>
</div>