<div class="max-w-2xl mx-auto p-4" x-data="{ focusInput() { if($wire.step == 1) $refs.rfidInput.focus() } }"
    x-init="focusInput(); setInterval(() => focusInput(), 800)" @click="focusInput()" @keydown.window="focusInput()">

    <div class="flex justify-center gap-4 mb-8">
        <a href="{{ route('rfid.request') }}"
            class="px-6 py-2 rounded-full font-bold text-sm transition-all {{ request()->routeIs('rfid.request') ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-zinc-500 border border-zinc-200' }}">
            Barang Habis Pakai
        </a>
        <a href="{{ route('kios-aset') }}"
            class="px-6 py-2 rounded-full font-bold text-sm transition-all {{ request()->routeIs('kios-aset') ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-zinc-500 border border-zinc-200' }}">
            Peminjaman Aset
        </a>
    </div>

    <div class="mb-8 text-center">
        <h2 class="text-2xl font-black text-black-600 uppercase">Kios Peminjaman Aset</h2>
        <p class="text-zinc-500 text-sm ">SMK Negeri 7 Baleendah</p>
    </div>

    @if(session()->has('success'))
        <div class="p-4 mb-6 text-white bg-green-500 rounded-2xl text-center shadow-lg font-bold animate-bounce">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 mb-6 text-red-700 bg-red-50 border border-red-100 rounded-2xl text-center font-medium italic">
            {{ session('error') }}
        </div>
    @endif

    {{-- STEP 1: SCAN KARTU (Logika Alpine dari BHP) --}}
    @if($step == 1)
        <div
            class="flex flex-col items-center justify-center space-y-6 py-16 bg-white border-2 border-dashed border-red-200 shadow-sm rounded-[2rem] relative overflow-hidden">
            <div class="p-6 bg-red-50 rounded-full relative z-10">
                <flux:icon name="identification" class="size-20 text-red-600" />
            </div>

            <div class="text-center relative z-10">
                <flux:heading size="xl" class="font-black">Siap Scan Kartu...</flux:heading>
                <flux:subheading class="text-lg italic">Tempelkan kartu RFID Anda</flux:subheading>
            </div>

            <input type="text" x-ref="rfidInput" wire:model.live="rfid_uid"
                class="opacity-0 absolute inset-0 w-full h-full cursor-none" autocomplete="off" autofocus />

            <div class="pt-4 relative z-10">
                <div
                    class="inline-flex items-center px-4 py-2 bg-zinc-100 rounded-full text-zinc-600 text-xs font-bold uppercase tracking-widest">
                    <span class="relative flex h-2 w-2 mr-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    Scanner Aset Aktif
                </div>
            </div>
        </div>
    @endif

    {{-- STEP 2: PILIH ASET --}}
    @if($step == 2 && $this->userData)
        <div class="bg-white p-6 rounded-[2rem] border shadow-xl space-y-6 animate-fade-in">
            <div class="flex items-center justify-between bg-zinc-50 p-4 rounded-2xl">
                <div>
                    <p class="text-[10px] text-zinc-500 uppercase font-bold tracking-tighter">Peminjam</p>
                    <p class="font-black text-zinc-900 leading-none">{{ $this->userData->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-zinc-500 uppercase font-bold tracking-tighter">Status</p>
                    <p class="font-black text-red-600 uppercase leading-none text-xs">{{ $this->userData->role }}</p>
                </div>
            </div>

            <div class="relative">
                <flux:input wire:model.live.debounce.300ms="search_asset" icon="magnifying-glass"
                    placeholder="Cari nama aset / Nomor Seri..." class="py-3" />

                @if(count($availableAssets) > 0)
                    <div
                        class="absolute z-50 w-full bg-white border border-zinc-200 rounded-2xl shadow-2xl mt-2 overflow-hidden">
                        @foreach($availableAssets as $asset)
                            <button wire:click="selectAsset({{ $asset->id }})"
                                class="w-full text-left p-4 hover:bg-red-50 flex justify-between items-center border-b border-zinc-50 last:border-0 transition-colors">
                                <div>
                                    <p class="text-sm font-bold text-zinc-900">{{ $asset->itemInfo->name }}</p>
                                    <p class="text-[10px] text-zinc-400 font-medium italic">SN: {{ $asset->serial_number ?? '-' }}
                                    </p>
                                </div>
                                <flux:icon name="plus-circle" class="text-red-600 size-6" />
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <flux:button wire:click="$set('step', 1)" variant="ghost" class="w-full py-4 font-bold">Batal</flux:button>
        </div>
    @endif

    {{-- STEP 3: KONFIRMASI --}}
    @if($step == 3 && $this->userData)
        <div class="bg-white p-6 rounded-[2rem] border shadow-xl space-y-6 animate-fade-in">
            <div class="text-center pb-2">
                <h3 class="font-black text-lg">Konfirmasi Peminjaman</h3>
            </div>

            <div class="p-4 bg-zinc-50 rounded-2xl border-l-4 border-red-600">
                <p class="text-[10px] text-zinc-500 font-bold uppercase">Aset yang dipilih:</p>
                <p class="font-black text-zinc-900">{{ $selectedAsset['name'] }}</p>
                <p class="text-xs text-zinc-500 italic">SN: {{ $selectedAsset['sn'] ?? '-' }}</p>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <flux:input type="date" label="Tenggat Pengembalian" wire:model="due_date" />
                <flux:textarea label="Catatan Tambahan (Opsional)" wire:model="notes" rows="2" />
            </div>

            <div class="flex gap-3">
                <flux:button wire:click="$set('step', 2)" variant="ghost" class="flex-1 py-4 font-bold">Ubah Aset
                </flux:button>
                <flux:button wire:click="submitLoan" variant="primary"
                    class="flex-1 py-4 font-black shadow-lg shadow-red-200 bg-red-600 hover:bg-red-700">
                    PINJAM SEKARANG
                </flux:button>
            </div>
        </div>
    @endif
</div>