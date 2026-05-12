<div class="p-6 space-y-6">
    @if(request()->routeIs('public.display'))
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-black">MONITORING LOGISTIK SARPRAS</h1>
            <p class="text-zinc-400">SMKN 7 BALEENDAH</p>
        </div>
    @endif

    <div class="flex flex-col">
        <flux:heading size="xl">Dashboard Ringkasan</flux:heading>
        <flux:subheading>Selamat datang, {{ auth()->user()->name }}. Berikut adalah status logistik saat ini.
        </flux:subheading>
    </div>

    {{-- 1. CEK ROLE: JIKA ADMIN/OWNER/PETUGAS, TAMPILKAN STATISTIK --}}
    @if(in_array(auth()->user()->role, ['admin', 'owner', 'petugas']))
        
        {{-- Indikator Terakhir Diperbarui --}}
        <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-full border border-zinc-200 shadow-sm w-fit">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            <flux:text size="sm" class="text-zinc-500 font-medium">
                Terakhir diperbarui: <span class="text-zinc-800 font-mono">{{ $lastUpdated }}</span>
            </flux:text>
        </div>

        {{-- Grid Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Card Total Barang --}}
            <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                    <flux:icon name="archive-box" />
                </div>
                <div>
                    <flux:text size="sm" class="text-zinc-500">Total Jenis Barang</flux:text>
                    <flux:heading size="xl">{{ $totalItems }}</flux:heading>
                </div>
            </div>

            {{-- Card Stok Kritis --}}
            <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-red-50 rounded-lg text-red-600">
                    <flux:icon name="exclamation-triangle" />
                </div>
                <div>
                    <flux:text size="sm" class="text-zinc-500">Stok Menipis</flux:text>
                    <flux:heading size="xl" class="text-red-600">{{ $lowStockCount }}</flux:heading>
                </div>
            </div>

            {{-- Card Transaksi --}}
            <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-green-50 rounded-lg text-green-600">
                    <flux:icon name="arrow-path" />
                </div>
                <div>
                    <flux:text size="sm" class="text-zinc-500">Aktivitas Hari Ini</flux:text>
                    <flux:heading size="xl">{{ $todayActivityCount }}</flux:heading>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Daftar Stok Menipis --}}
            <flux:card>
                <flux:heading class="mb-4" icon="exclamation-circle">Daftar Stok Kritis</flux:heading>
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Barang</flux:table.column>
                        <flux:table.column>Sisa</flux:table.column>
                        <flux:table.column>Min</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($lowStockItems->take(5) as $item)
                            <flux:table.row>
                                <flux:table.cell class="font-medium">{{ $item->name }}</flux:table.cell>
                                <flux:table.cell class="text-red-600 font-bold">{{ $item->stock }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-400">{{ $item->min_stock }}</flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="3" class="text-center text-zinc-400 py-4">Semua stok aman.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>

            {{-- Aktivitas Keluar Terbaru --}}
            <flux:card>
                <flux:heading class="mb-4" icon="clock">Pengeluaran Terakhir</flux:heading>
                <div class="space-y-4">
                    @forelse($recentOutgoing as $out)
                        <div class="flex items-start gap-3 text-sm border-b border-zinc-100 pb-3 last:border-0">
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold flex-shrink-0">
                                {{ substr($out->student->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-zinc-900 font-medium leading-none">{{ $out->student->name ?? 'User' }}</p>
                                        <p class="text-zinc-500 text-xs mt-1">{{ $out->class->name ?? 'Staff/Umum' }}</p>
                                    </div>
                                    <p class="text-zinc-400 text-[10px]">
                                        {{ \Carbon\Carbon::parse($out->request_date)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="mt-2 space-y-1">
                                    @foreach($out->details as $detail)
                                        <div class="p-2 bg-zinc-50 rounded-lg border border-zinc-100 flex justify-between items-center">
                                            <span class="text-zinc-700 text-xs font-semibold">{{ $detail->item->name ?? 'Barang Terhapus' }}</span>
                                            <flux:badge size="sm" variant="subtle" color="blue">{{ $detail->quantity_approved }} {{ $detail->item->unit ?? '' }}</flux:badge>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-zinc-400 py-4">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </flux:card>
        </div>

    {{-- 2. JIKA YANG LOGIN ADALAH SISWA/GURU --}}
    @else
        <div class="bg-blue-600 rounded-3xl p-8 text-white shadow-lg overflow-hidden relative">
            <div class="relative z-10">
                <h1 class="text-2xl font-bold">Layanan Mandiri Logistik</h1>
                <p class="mt-2 text-blue-100 max-w-md">Butuh peralatan praktek atau ATK? Silakan buat permintaan melalui menu Kios.</p>
                <!-- <div class="mt-6">
                    <flux:button href="{{ route('rfid.request') }}" variant="ghost" icon="shopping-cart">
                        Buka Kios Permintaan
                    </flux:button>
                </div> -->
            </div>
            <flux:icon name="archive-box" class="absolute -right-10 -bottom-10 size-64 opacity-10 rotate-12" />
        </div>

        <div class="mt-8">
            <flux:heading class="mb-4" icon="clock">Status Permintaan Terakhir Anda</flux:heading>
            @livewire('student-request-status')
        </div>
    @endif
</div>