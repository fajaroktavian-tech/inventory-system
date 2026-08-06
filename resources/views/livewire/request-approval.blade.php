<div class="p-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">Manajemen Persetujuan Permintaan</flux:heading>
            <flux:subheading>Kelola permintaan barang masuk dari siswa dan pantau riwayat keputusannya.</flux:subheading>
        </div>
    </div>

    <!-- FILTER & PENCARIAN -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white p-4 rounded-xl border shadow-sm">
        <div class="flex flex-wrap items-center gap-3 w-full">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama siswa..." class="max-w-xs flex-1" />
            
            <flux:select wire:model.live="filterStatus" class="max-w-[180px]">
                <option value="">Semua Status</option>
                <option value="pending">Pending (Menunggu)</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
            </flux:select>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-3 text-sm text-white bg-green-500 rounded-lg shadow-sm">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="p-3 text-sm text-white bg-red-500 rounded-lg shadow-sm">{{ session('error') }}</div>
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
                                • Diajukan: {{ \Carbon\Carbon::parse($request->request_date)->format('d M Y') }}
                            </p>
                        </div>
                    </div>

                    <!-- BADGE STATUS DINAMIS -->
                    @php
                        $badgeColor = match($request->status) {
                            'pending' => 'yellow',
                            'approved' => 'green',
                            'rejected' => 'red',
                            default => 'zinc'
                        };
                    @endphp
                    <flux:badge :color="$badgeColor" size="sm" inset="top bottom">
                        {{ ucfirst($request->status) }}
                    </flux:badge>
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
                                <th class="text-right pb-2">{{ $request->status == 'pending' ? 'Aksi Stok' : 'Disetujui' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($request->details as $detail)
                                <tr>
                                    <td class="py-2">{{ $detail->item->name }}</td>
                                    <td class="py-2 text-center font-medium">{{ $detail->quantity_requested }} {{ $detail->item->unit }}</td>
                                    <td class="py-2 text-right">
                                        @if($request->status == 'pending')
                                            @if($editingId == $request->id)
                                                <input type="number" wire:model="editQuantities.{{ $detail->id }}" 
                                                    class="w-16 text-right border rounded px-1 text-sm text-blue-600 focus:ring-1 focus:ring-blue-500">
                                            @else
                                                <span class="text-zinc-400 italic">Sesuai permintaan</span>
                                            @endif
                                        @else
                                            <!-- Tampilkan hasil kuantitas yang disetujui untuk riwayat -->
                                            <span class="font-bold text-zinc-700">{{ $detail->quantity_approved ?? $detail->quantity_requested }} {{ $detail->item->unit }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- BAGIAN Kaki Kartu / Tombol Aksi -->
                <div class="flex justify-between items-center text-xs text-zinc-400 pt-2 border-t">
                    <div>
                        @if($request->status != 'pending' && $request->approved_at)
                            Diproses pada: {{ \Carbon\Carbon::parse($request->approved_at)->format('d M Y H:i') }}
                        @else
                            <span>Status menunggu tindakan</span>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        @if($request->status == 'pending')
                            @if($editingId == $request->id)
                                <flux:button size="sm" variant="ghost" wire:click="$set('editingId', null)">Batal Edit</flux:button>
                                <flux:button size="sm" color="blue" icon="check" wire:click="approve({{ $request->id }})">Setujui Hasil Edit</flux:button>
                            @else
                                <flux:button size="sm" variant="danger" color="red" wire:click="reject({{ $request->id }})" wire:confirm="Tolak permintaan ini?">Tolak</flux:button>
                                <flux:button size="sm" variant="primary" color="yellow" icon="pencil-square" wire:click="startEdit({{ $request->id }})">Edit Qty</flux:button>
                                <flux:button size="sm" variant="primary" color="green" icon="hand-thumb-up" wire:click="approve({{ $request->id }})">Setujui Langsung</flux:button>
                            @endif
                        @else
                            <span class="italic text-zinc-500">Selesai diproses</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-zinc-50 rounded-xl border-2 border-dashed">
                <flux:icon name="inbox" class="mx-auto size-12 text-zinc-300" />
                <p class="mt-2 text-zinc-500">Tidak ada data permintaan ditemukan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $requests->links() }}
    </div>
</div>