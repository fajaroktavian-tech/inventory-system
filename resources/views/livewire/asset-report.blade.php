<div class="p-6">
    <div class="flex items-center gap-3 mb-1">
        <flux:icon name="document-chart-bar" variant="outline" class="text-zinc-500" />
        <flux:heading size="xl" level="1">Rekapitulasi Populasi Aset</flux:heading>
    </div>
    <flux:subheading>Statistik jumlah aset berdasarkan merk dan tipe barang yang sama.</flux:subheading>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
            <p class="text-sm text-zinc-500 uppercase tracking-wider">Total Model Barang</p>
            <p class="text-2xl font-bold">{{ $assetSummary->count() }} Jenis</p>
        </div>
        <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
            <p class="text-sm text-zinc-500 uppercase tracking-wider">Total Seluruh Unit</p>
            <p class="text-2xl font-bold text-blue-600">{{ $assetSummary->sum('total_unit') }} Unit</p>
        </div>
        <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
            <p class="text-sm text-zinc-500 uppercase tracking-wider">Total Unit Rusak</p>
            <p class="text-2xl font-bold text-red-600">{{ $assetSummary->sum('kondisi_rusak') }} Unit</p>
        </div>
    </div>

    <div class="flex justify-between mt-8 mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari nama barang atau merk..." class="max-w-xs" />
        <div class="flex flex-wrap gap-2">
    {{-- Tombol Export Excel --}}
    <flux:button variant="filled" color="green" icon="document-text" wire:click="exportExcel" size="sm">Excel</flux:button>
    {{-- Tombol Export PDF --}}
    <flux:button variant="filled" color="red" icon="document-check" wire:click="exportPDF" size="sm">PDF</flux:button>
    </div>  
    </div>
    

    <div class="mt-8">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Nama Barang</flux:table.column>
                <flux:table.column>Merk</flux:table.column>
                <flux:table.column>Spesifikasi</flux:table.column>
                <flux:table.column>Total Unit</flux:table.column>
                <flux:table.column>Kondisi Baik</flux:table.column>
                <flux:table.column>Rusak (RR/RB)</flux:table.column>
                <flux:table.column>Kelayakan</flux:table.column>
                <flux:table.column>Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($assetSummary as $item)
                 <flux:table.row>
                    <flux:table.cell class="font-bold text-zinc-800 dark:text-white">
                        {{ $item->name }}
                    </flux:table.cell>
                    <flux:table.cell>{{ $item->brand ?? '-' }}</flux:table.cell>
                    <flux:table.cell class="text-xs text-zinc-500 italic">
                        {{ Str::limit($item->specification, 50) }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="zinc" size="sm" inset="top bottom">{{ $item->total_unit }} Unit</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="text-green-600 font-semibold">{{ $item->kondisi_baik }}</span>
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="text-red-500 font-semibold">{{ $item->kondisi_rusak }}</span>
                    </flux:table.cell>
                    <flux:table.cell>
                        @php 
                            $percent = ($item->kondisi_baik / $item->total_unit) * 100;
                            $color = $percent > 80 ? 'green' : ($percent > 50 ? 'yellow' : 'red');
                        @endphp
                            <div class="flex items-center gap-2">
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 w-16">
                                    <div class="bg-{{ $color }}-500 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="text-xs font-medium">{{ round($percent) }}%</span>
                            </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button size="sm" variant="ghost" icon="eye" href="{{ route('asset-registration.index', ['search' => $item->name]) }}" wire:navigate>
                        Detail
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
        <div class="mt-4">
            {{ $assetSummary->links() }}
        </div>
    </div>
</div>