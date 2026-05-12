<div class="space-y-4" wire:poll.10s>
    @forelse($myRequests as $req)
        <div class="bg-white p-4 rounded-2xl border shadow-sm border-l-4 {{ $req->status == 'approved' ? 'border-l-green-500' : 'border-l-yellow-500' }}">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-xs text-zinc-500">{{ $req->request_date }}</p>
                    <p class="font-bold text-sm uppercase">ID: #{{ $req->id }}</p>
                </div>
                <flux:badge size="sm" :color="$req->status == 'approved' ? 'green' : 'yellow'">
                    {{ strtoupper($req->status) }}
                </flux:badge>
            </div>

            <div class="space-y-1 mb-3">
                @foreach($req->details as $detail)
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-600">{{ $detail->item->name }}</span>
                        <span class="font-bold">{{ $detail->quantity_requested }} {{ $detail->item->unit }}</span>
                    </div>
                @endforeach
            </div>

            @if($req->status == 'approved')
                <div class="bg-green-50 p-3 rounded-lg border border-green-100 text-center">
                    <p class="text-green-700 text-[10px] font-bold uppercase tracking-widest">Tunjukkan ke Petugas Sarpras</p>
                    <div class="mt-2 flex justify-center">
                        {{-- Simulasi Barcode sederhana untuk estetika bukti --}}
                        <div class="h-8 w-full bg-[url('https://border-image.com/barcode.png')] bg-repeat-x opacity-20"></div>
                    </div>
                </div>
            @endif
        </div>
    @empty
        <div class="text-center py-10 text-zinc-400 italic">Belum ada riwayat permintaan.</div>
    @endforelse
</div>