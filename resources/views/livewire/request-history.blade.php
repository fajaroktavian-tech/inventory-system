<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900">Riwayat Permintaan</h1>
            <p class="text-sm text-zinc-500">Pantau status permintaan alat dan bahan Anda.</p>
        </div>

        <div class="flex gap-2">
    <flux:button 
        wire:click="exportExcel" 
        wire:loading.attr="disabled"
        icon="document-arrow-down" 
        variant="filled"
    >
        <span wire:loading.remove>Excel</span>
        <span wire:loading>Exporting...</span>
    </flux:button>
</div>
    </div>

    {{-- Filter Section --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-white p-4 rounded-2xl border border-zinc-200">
        <flux:select wire:model.live="filterStatus" label="Status">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </flux:select>

        <flux:input type="date" wire:model.live="filterDate" label="Tanggal" />
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-2xl border border-zinc-200 overflow-hidden shadow-sm">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Tanggal</flux:table.column>
                <flux:table.column>Tujuan</flux:table.column>
                <flux:table.column>Barang</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($requests as $request)
                    <flux:table.row>
                        <flux:table.cell class="whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($request->request_date)->format('d M Y') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            @if($request->type === 'class')
                                <flux:badge size="sm" inset="left" icon="academic-cap">{{ $request->class->name ?? 'Kelas' }}</flux:badge>
                            @else
                                <flux:badge size="sm" inset="left" icon="building-office" color="zinc">{{ $request->room->name ?? 'Ruangan' }}</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="text-sm">
                                @foreach($request->details as $detail)
                                    <span class="inline-block bg-zinc-100 px-2 py-0.5 rounded text-[11px] mb-1">
                                        {{ $detail->item->name }} ({{ $detail->quantity_requested }})
                                    </span>
                                @endforeach
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            @php
                                $color = match($request->status) {
                                    'approved' => 'green',
                                    'rejected' => 'red',
                                    default => 'amber',
                                };
                            @endphp
                            <flux:badge color="{{ $color }}" size="sm" class="uppercase font-bold">
                                {{ $request->status }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:modal.trigger name="detail-{{ $request->id }}">
                                <flux:button variant="ghost" size="sm" icon="eye"></flux:button>
                            </flux:modal.trigger>

                            {{-- Modal Detail --}}
                            <flux:modal name="detail-{{ $request->id }}" class="md:w-[500px]">
                                <div class="space-y-4">
                                    <flux:heading size="lg">Detail Permintaan</flux:heading>
                                    <flux:separator />
                                    
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-zinc-500">Tanggal Pengajuan:</p>
                                            <p class="font-bold">{{ $request->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-zinc-500">Status:</p>
                                            <p class="font-bold uppercase">{{ $request->status }}</p>
                                        </div>
                                    </div>

                                    <div class="bg-zinc-50 p-4 rounded-xl">
                                        <p class="text-xs font-bold uppercase text-zinc-400 mb-2">Catatan:</p>
                                        <p class="text-sm italic">{{ $request->notes ?? 'Tidak ada catatan.' }}</p>
                                    </div>

                                    <flux:table>
                                        <flux:table.columns>
                                            <flux:table.column>Barang</flux:table.column>
                                            <flux:table.column>Qty</flux:table.column>
                                        </flux:table.columns>
                                        <flux:table.rows>
                                            @foreach($request->details as $detail)
                                                <flux:table.row>
                                                    <flux:table.cell>{{ $detail->item->name }}</flux:table.cell>
                                                    <flux:table.cell>{{ $detail->quantity_requested }}</flux:table.cell>
                                                </flux:table.row>
                                            @endforeach
                                        </flux:table.rows>
                                    </flux:table>
                                </div>
                            </flux:modal>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-10 text-zinc-400 italic">
                            Belum ada riwayat permintaan.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <div class="mt-4">
        {{ $requests->links() }}
    </div>
</div>