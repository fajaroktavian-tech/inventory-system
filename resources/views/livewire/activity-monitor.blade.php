<div wire:poll.10s class="p-6 lg:p-10 font-sans">
    
    {{-- HEADER HALAMAN --}}
    <div class="mb-10 flex justify-between items-end">
        <div>
            <h1 class="text-white text-2xl font-black uppercase tracking-tight">Monitor Aktivitas</h1>
            <p class="text-white/70 text-sm font-bold uppercase tracking-widest">Update Otomatis • SMKN 7 Baleendah</p>
        </div>
        <div class="text-right">
             <flux:badge color="green" inset="left" icon="clock" class="text-white border-none">
                {{ now()->timezone('Asia/Jakarta')->format('H:i') }} WIB
             </flux:badge>
        </div>
    </div>

    {{-- BARIS PERTAMA: STATISTIC CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        
        <flux:card class="flex flex-col justify-between bg-zinc-900 border-zinc-800">
            <div>
                <div class="flex justify-between items-start">
                    <flux:heading size="sm" class="text-white uppercase tracking-widest font-bold">Total Barang</flux:heading>
                    <flux:icon name="archive-box" class="text-blue-400" />
                </div>
                <div class="mt-4">
                    <div class="text-4xl font-black text-white">{{ number_format($totalItems) }}</div>
                    <flux:text class="text-[10px] uppercase font-bold text-white/80">Item Terdaftar</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="flex flex-col justify-between bg-zinc-900 border-zinc-800">
            <div>
                <div class="flex justify-between items-start">
                    <flux:heading size="sm" class="text-white uppercase tracking-widest font-bold">Ruang & Lab</flux:heading>
                    <flux:icon name="building-office" class="text-purple-400" />
                </div>
                <div class="mt-4">
                    <div class="text-4xl font-black text-white">{{ $totalRooms }}</div>
                    <flux:text class="text-[10px] uppercase font-bold text-white/80">Area Terdata</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="flex flex-col justify-between bg-zinc-900 border-zinc-800 border-b-4 border-b-green-500">
            <div>
                <div class="flex justify-between items-start">
                    <flux:heading size="sm" class="text-white uppercase tracking-widest font-bold">Permintaan Hari Ini</flux:heading>
                    <flux:icon name="arrow-path" class="text-green-400 animate-spin-slow" />
                </div>
                <div class="mt-4">
                    <div class="text-4xl font-black text-white">{{ $todayRequests }}</div>
                    <flux:text class="text-[10px] uppercase font-bold text-white/80">Transaksi Berhasil</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="bg-red-600 border-none shadow-lg shadow-red-900/40">
            <div class="flex justify-between items-start">
                <flux:heading size="sm" class="text-white uppercase tracking-widest font-bold">Stok Menipis</flux:heading>
                <flux:icon name="exclamation-triangle" class="text-white" />
            </div>
            <div class="mt-4">
                <div class="text-4xl font-black text-white">{{ $lowStockCount }}</div>
                <flux:text class="text-[10px] uppercase font-bold text-white tracking-tighter">Butuh Pengisian Segera</flux:text>
            </div>
        </flux:card>

    </div>

    {{-- BARIS KEDUA: TABEL AKTIVITAS REAL-TIME --}}
    <div class="bg-zinc-900/50 border border-zinc-800 rounded-[2rem] overflow-hidden backdrop-blur-sm">
        <div class="p-6 border-b border-zinc-800 flex justify-between items-center bg-zinc-800/30">
            <h2 class="text-white text-lg font-bold flex items-center gap-3">
                <span class="flex h-3 w-3 rounded-full bg-green-500 animate-pulse"></span>
                Aktivitas Pengguna Terkini
            </h2>
            <flux:badge color="zinc" class="!text-white border-zinc-700 uppercase text-[10px] font-black">Log Real-time</flux:badge>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column class="!text-white font-black uppercase text-xs">Waktu</flux:table.column>
                <flux:table.column class="!text-white font-black uppercase text-xs">Pengguna</flux:table.column>
                <flux:table.column class="!text-white font-black uppercase text-xs">Tujuan</flux:table.column>
                <flux:table.column class="!text-white font-black uppercase text-xs">Item</flux:table.column>
                <flux:table.column class="!text-white font-black uppercase text-xs text-right">Status</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($recentActivities as $activity)
                    <flux:table.row class="border-zinc-800/50 hover:bg-white/[0.02] transition-colors">
                        <flux:table.cell class="!text-white font-bold">
                            {{ $activity->created_at->timezone('Asia/Jakarta')->format('H:i') }}
                        </flux:table.cell>
                        
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <div class="size-8 bg-red-600 rounded-lg flex items-center justify-center text-xs font-black text-white uppercase shadow-sm">
                                    {{ substr($activity->student->name, 0, 1) }}
                                </div>
                                <span class="!text-white font-bold">{{ $activity->student->name }}</span>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            @if($activity->type === 'class')
                                <flux:badge size="sm" variant="outline" class="!text-white border-white/20" icon="academic-cap">
                                    {{ $activity->class->name ?? 'Kelas' }}
                                </flux:badge>
                            @else
                                <flux:badge size="sm" variant="outline" class="!text-white border-white/20" icon="building-office">
                                    {{ $activity->room->name ?? 'Ruangan' }}
                                </flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                @foreach($activity->details as $detail)
                                    <span class="text-[11px] bg-zinc-800 border border-zinc-700 px-2 py-1 rounded-md text-white font-medium">
                                        {{ $detail->item->name }} <span class="text-red-400">({{ $detail->quantity_requested }})</span>
                                    </span>
                                @endforeach
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            @php
                                $color = match($activity->status) {
                                    'approved' => 'green',
                                    'rejected' => 'red',
                                    default => 'amber',
                                };
                            @endphp
                            <flux:badge size="sm" color="{{ $color }}" class="font-black uppercase tracking-widest px-3">
                                {{ $activity->status }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-20 text-white/50 italic text-sm">
                            <flux:icon name="magnifying-glass" class="mx-auto size-8 mb-4 opacity-20" />
                            Belum ada aktivitas yang tercatat hari ini...
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>