<div>
    <flux:modal name="stock-modal" variant="wide" class="min-w-[80vw]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Daftar Stok Barang</flux:heading>
                <flux:subheading>Data inventaris sarana prasarana SMKN 7 Baleendah secara real-time.</flux:subheading>
            </div>

            {{-- Input Pencarian --}}
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari nama barang..." />

            <div class="max-h-[60vh] overflow-y-auto border border-zinc-200 rounded-xl">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Nama Barang</flux:table.column>
                        <flux:table.column>Kategori</flux:table.column>
                        <flux:table.column>Stok</flux:table.column>
                        <flux:table.column>Satuan</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse($items as $item)
                            <flux:table.row>
                                <flux:table.cell class="font-bold text-zinc-900">{{ $item->name }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge size="sm" color="zinc" inset="left">{{ $item->category->name ?? '-' }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="font-mono">{{ $item->stock }}</flux:table.cell>
                                <flux:table.cell>{{ $item->unit }}</flux:table.cell>
                                <flux:table.cell>
                                    @if($item->stock <= $item->min_stock)
                                        <flux:badge color="red" size="sm">Hampir Habis</flux:badge>
                                    @else
                                        <flux:badge color="green" size="sm">Tersedia</flux:badge>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5" class="text-center py-10 text-zinc-500 italic">
                                    Barang tidak ditemukan...
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <div class="flex justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Tutup</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</div>