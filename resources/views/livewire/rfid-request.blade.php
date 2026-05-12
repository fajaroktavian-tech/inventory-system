{{-- Layout Kios RFID (Tanpa Tab Status, Fokus pada Tap Kartu) --}}
<div class="max-w-2xl mx-auto p-4" x-data="{ 
        focusInput() { if($wire.step == 1) $refs.rfidInput.focus() } 
    }" x-init="focusInput(); setInterval(() => focusInput(), 800)" @click="focusInput()"
    @keydown.window="focusInput()">

    <div class="mb-8 text-center">
        <h2 class="text-2xl font-black text-zinc-900">Layanan Sarpras Mandiri</h2>
        <p class="text-zinc-500 text-sm">Silakan tap kartu RFID Anda untuk memulai permintaan</p>
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

    {{-- STEP 1: SCAN KARTU --}}
    @if($step == 1)
        <div
            class="flex flex-col items-center justify-center space-y-6 py-16 bg-white border-2 border-dashed border-zinc-200 shadow-sm rounded-[2rem] relative overflow-hidden">
            <div class="p-6 bg-blue-50 rounded-full relative z-10">
                <flux:icon name="credit-card" class="size-20 text-blue-600" />
            </div>

            <div class="text-center relative z-10">
                <flux:heading size="xl" class="font-black">Siap Scan...</flux:heading>
                <flux:subheading class="text-lg">Tempelkan kartu RFID pada reader</flux:subheading>
            </div>

            {{-- Input Hidden yang selalu fokus --}}
            <input type="text" x-ref="rfidInput" wire:model.live="rfid_uid"
                class="opacity-0 absolute inset-0 w-full h-full cursor-none" autocomplete="off" autofocus />

            <div class="pt-4 relative z-10">
                <div
                    class="inline-flex items-center px-4 py-2 bg-zinc-100 rounded-full text-zinc-600 text-xs font-bold uppercase tracking-widest">
                    <span class="relative flex h-2 w-2 mr-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    Sensor Aktif
                </div>
            </div>
        </div>
    @endif

    {{-- STEP 2: PILIH TUJUAN (Hanya untuk Guru/Staff) --}}
    @if($step == 2 && $this->userData)
        <div class="bg-white p-8 rounded-[2rem] border shadow-xl space-y-6 animate-fade-in">
            <div class="flex items-center gap-4 border-b border-zinc-100 pb-6">
                <div
                    class="size-14 bg-red-600 rounded-2xl flex items-center justify-center font-black text-xl text-white shadow-lg shadow-red-200">
                    {{ substr($this->userData->name, 0, 1) }}
                </div>
                <div>
                    <p class="text-xs text-zinc-500 font-bold uppercase tracking-wider">Identitas Terdeteksi</p>
                    <p class="font-black text-xl text-zinc-900">{{ $this->userData->name }}</p>
                </div>
            </div>

            <flux:select label="Ruangan yang Dituju" wire:model.live="room_id" variant="listbox">
                <option value="">-- Pilih Ruangan --</option>
                @foreach($rooms as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                @endforeach
            </flux:select>

            <div class="flex gap-3">
                <flux:button wire:click="$set('step', 1)" variant="ghost" class="flex-1 py-4">Batal</flux:button>
                <flux:button wire:click="setLocation" variant="primary" class="flex-1 py-4 shadow-lg shadow-blue-100"
                    :disabled="!$room_id">
                    Lanjut
                </flux:button>
            </div>
        </div>
    @endif

    {{-- STEP 3: PILIH BARANG --}}
    @if($step == 3 && $this->userData)
        <div class="bg-white p-6 rounded-[2rem] border shadow-xl space-y-6 animate-fade-in">
            <div class="flex items-center justify-between bg-zinc-50 p-4 rounded-2xl text-sm">
                <div>
                    <p class="text-[10px] text-zinc-500 uppercase font-bold">Peminjam</p>
                    <p class="font-black text-zinc-900">{{ $this->userData->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-zinc-500 uppercase font-bold">
                        {{ $this->userData->role == 'siswa' ? 'Kelas' : 'Unit' }}</p>
                    <p class="font-black text-zinc-900">
                        {{ $this->userData->class->name ?? strtoupper($this->userData->role) }}</p>
                </div>
            </div>

            <div class="relative">
                <flux:input wire:model.live.debounce.300ms="search_item" icon="magnifying-glass"
                    placeholder="Cari nama barang..." class="py-3" />

                @if(count($availableItems) > 0)
                    <div
                        class="absolute z-50 w-full bg-white border border-zinc-200 rounded-2xl shadow-2xl mt-2 overflow-hidden">
                        @foreach($availableItems as $item)
                            <button wire:click="addItem({{ $item->id }})"
                                class="w-full text-left p-4 hover:bg-red-50 flex justify-between items-center border-b border-zinc-50 last:border-0 transition-colors">
                                <div>
                                    <p class="text-sm font-bold text-zinc-900">{{ $item->name }}</p>
                                    <p class="text-[10px] text-zinc-400 font-medium">Tersedia: {{ $item->stock }} {{ $item->unit }}
                                    </p>
                                </div>
                                <flux:icon name="plus-circle" class="text-red-600 size-6" />
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="space-y-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                @forelse($selectedItems as $index => $item)
                    <div class="p-3 bg-zinc-50 rounded-xl flex items-center justify-between gap-4 border border-zinc-100">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm text-zinc-900 truncate">{{ $item['name'] }}</p>
                            <p class="text-[10px] text-zinc-500">Satuan: {{ $item['unit'] }}</p>
                        </div>
                        <div class="w-20">
                            <flux:input type="number" wire:model.live="selectedItems.{{ $index }}.qty" size="sm"
                                class="text-center font-bold" />
                        </div>
                        <button wire:click="removeItem({{ $index }})"
                            class="text-red-500 p-1 hover:bg-red-100 rounded-lg transition-colors">
                            <flux:icon name="trash" size="sm" />
                        </button>
                    </div>
                @empty
                    <div class="text-center py-8 border-2 border-dashed border-zinc-100 rounded-2xl">
                        <p class="text-zinc-400 text-sm italic">Daftar permintaan masih kosong.</p>
                    </div>
                @endforelse
            </div>

            <flux:textarea label="Catatan Tambahan (Opsional)" wire:model="notes" rows="2"
                placeholder="Contoh: Untuk praktik bengkel..." />

            <div class="flex gap-3 pt-2">
                <flux:button wire:click="$set('step', 1)" variant="ghost" class="flex-1 py-4 font-bold">Batal</flux:button>
                <flux:button wire:click="submitRequest" variant="primary"
                    class="flex-1 py-4 font-black shadow-lg shadow-red-200 bg-red-600 hover:bg-red-700"
                    wire:loading.attr="disabled">
                    KIRIM DATA
                </flux:button>
            </div>
        </div>
    @endif

    @auth
        <div class="fixed top-6 right-6 z-[60] flex gap-2">
            <flux:button href="{{ route('dashboard') }}" variant="filled" size="sm" icon="home">
                Kembali ke Dashboard
            </flux:button>
        </div>
    @endauth

    <div class="mt-12 text-center">
        <a href="{{ route('home') }}"
            class="text-zinc-400 hover:text-zinc-600 text-xs font-bold uppercase tracking-widest transition-colors">
            ← Kembali ke Halaman Utama
        </a>
    </div>
</div>