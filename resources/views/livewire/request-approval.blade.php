<div class="p-6">
    <flux:heading size="xl" class="mb-6">Daftar Persetujuan Permintaan (Pending)</flux:heading>

    <div class="mb-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari nama siswa..." class="max-w-xs" />
    </div>

    @if (session()->has('message'))
        <div class="p-3 mb-4 text-sm text-white bg-green-500 rounded-lg shadow-sm">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="p-3 mb-4 text-sm text-white bg-red-500 rounded-lg shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="space-y-4">
        @forelse($requests as $request)
            <div class="bg-white border rounded-xl shadow-sm p-5 hover:border-blue-300 transition">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="size-10 bg-zinc-100 rounded-full flex items-center justify-center font-bold text-blue-600 border">
                            {{ substr($request->student->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-zinc-800">{{ $request->student->name }}</p>
                            <p class="text-xs text-zinc-500">
                                {{ $request->type == 'class' ? 'Kelas: ' . ($request->class->name ?? '-') : '🏢 Ruang: ' . ($request->room->name ?? '-') }}
                                • {{ \Carbon\Carbon::parse($request->request_date)->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <flux:badge color="yellow" size="sm" inset="top bottom">Pending</flux:badge>
                </div>

                @if($request->notes)
                    <div class="mb-4 p-2 bg-zinc-50 rounded border-l-4 border-zinc-300 text-sm text-zinc-600">
                        "{{ $request->notes }}"
                    </div>
                @endif

                <div class="bg-zinc-50 rounded-lg p-3 mb-4">
                    <table class="w-full text-sm">
                        <thead class="text-zinc-500 border-b">
                            <tr>
                                <th class="text-left pb-2">Nama Barang</th>
                                <th class="text-center pb-2">Diminta</th>
                                <th class="text-right pb-2">Aksi Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($request->details as $detail)
                                <tr>
                                    <td class="py-2">{{ $detail->item->name }}</td>
                                    <td class="py-2 text-center font-medium">{{ $detail->quantity_requested }} {{ $detail->item->unit }}</td>
                                    <td class="py-2 text-right">
                                        @if($editingId == $request->id)
                                            <input type="number" wire:model="editQuantities.{{ $detail->id }}" 
                                                class="w-16 text-right border rounded px-1 text-sm text-blue-600 focus:ring-1 focus:ring-blue-500">
                                        @else
                                            <span class="text-zinc-400 italic">Sesuai permintaan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end gap-2">
                    @if($editingId == $request->id)
                        <flux:button size="sm" variant="ghost" wire:click="$set('editingId', null)">Batal Edit</flux:button>
                        <flux:button size="sm" color="blue" icon="check" wire:click="approve({{ $request->id }})">Setujui Hasil Edit</flux:button>
                    @else
                        <flux:button size="sm" variant="danger" color="red" wire:click="reject({{ $request->id }})" wire:confirm="Tolak permintaan ini?">Tolak</flux:button>
                        <flux:button size="sm" variant="primary" color="yellow" icon="pencil-square" wire:click="startEdit({{ $request->id }})">Edit Qty</flux:button>
                        <flux:button size="sm" variant="primary" color="green" icon="hand-thumb-up" wire:click="approve({{ $request->id }})">Setujui Langsung</flux:button>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-zinc-50 rounded-xl border-2 border-dashed">
                <flux:icon name="inbox" class="mx-auto size-12 text-zinc-300" />
                <p class="mt-2 text-zinc-500">Tidak ada permintaan yang menunggu persetujuan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $requests->links() }}
    </div>
</div>