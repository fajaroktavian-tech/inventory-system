<div class="max-w-4xl mx-auto p-4" x-data="{ tab: 'form' }">

    {{-- Header Section --}}
    <div class="mb-6">
        <h1 class="text-2xl font-black text-zinc-900">Permintaan Sarpras</h1>
        <p class="text-zinc-500 text-sm">Silakan isi formulir di bawah untuk permintaan alat atau bahan praktik.</p>
    </div>

    {{-- Navigasi Tab --}}
    <div class="flex mb-8 bg-zinc-100 p-1 rounded-2xl w-full sm:w-72">
        <button @click="tab = 'form'"
            :class="tab === 'form' ? 'bg-white shadow-sm text-red-600' : 'text-zinc-500 hover:text-zinc-700'"
            class="flex-1 py-2 rounded-xl text-sm font-bold transition-all duration-200">
            Formulir
        </button>
        <button @click="tab = 'status'"
            :class="tab === 'status' ? 'bg-white shadow-sm text-red-600' : 'text-zinc-500 hover:text-zinc-700'"
            class="flex-1 py-2 rounded-xl text-sm font-bold transition-all duration-200">
            Status Saya
        </button>
    </div>

    {{-- KONTEN TAB: FORM --}}
    <div x-show="tab === 'form'" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95">

        @if(session()->has('success'))
            <div class="p-4 mb-6 text-white bg-green-500 rounded-2xl text-center shadow-lg font-bold">
                {{ session('success') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="p-4 mb-6 text-red-700 bg-red-50 border border-red-100 rounded-2xl text-center font-medium italic">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid lg:grid-cols-5 gap-8">

            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white p-6 rounded-[2rem] border border-zinc-200 shadow-sm space-y-6">
                    <div class="relative">
                        <flux:input wire:model.live.debounce.300ms="search_item" label="Cari Barang"
                            icon="magnifying-glass" placeholder="Ketik nama alat/bahan..." class="py-3" />

                        @if(count($availableItems) > 0)
                            <div
                                class="absolute z-50 w-full bg-white border border-zinc-200 rounded-2xl shadow-2xl mt-2 overflow-hidden">
                                @foreach($availableItems as $item)
                                    <button wire:click="addItem({{ $item->id }})"
                                        class="w-full text-left p-4 hover:bg-red-50 flex justify-between items-center border-b border-zinc-50 last:border-0 transition-colors">
                                        <div>
                                            <p class="text-sm font-bold text-zinc-900">{{ $item->name }}</p>
                                            <p class="text-[10px] text-zinc-400 font-medium tracking-wider">STOK:
                                                {{ $item->stock }} {{ $item->unit }}</p>
                                        </div>
                                        <flux:icon name="plus-circle" class="text-red-600 size-6" />
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <p class="text-xs font-black text-zinc-400 uppercase tracking-widest">Daftar Pilihan</p>
                        <div class="divide-y divide-zinc-100 border border-zinc-100 rounded-2xl overflow-hidden">
                            @forelse($selectedItems as $index => $item)
                                <div class="p-4 bg-white flex items-center justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-sm text-zinc-900 truncate">{{ $item['name'] }}</p>
                                        <p class="text-[10px] text-zinc-400 uppercase font-bold">{{ $item['stock'] }}
                                            {{ $item['unit'] }} Tersedia</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-20">
                                            <flux:input type="number" wire:model.live="selectedItems.{{ $index }}.qty"
                                                size="sm" class="text-center font-bold" min="1" max="{{ $item['stock'] }}"
                                                {{-- Memberi batas maksimal di sisi browser --}}
                                                oninput="this.value = !!this.value && Math.abs(this.value) >= 1 ? Math.abs(this.value) : 1"
                                                {{-- Mencegah angka minus di sisi client --}} />
                                        </div>
                                        <button wire:click="removeItem({{ $index }})"
                                            class="text-zinc-300 hover:text-red-500 transition-colors">
                                            <flux:icon name="trash" size="sm" />
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <flux:icon name="archive-box" class="size-12 text-zinc-200 mx-auto mb-3" />
                                    <p class="text-zinc-400 text-sm italic">Belum ada barang yang dipilih.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-zinc-900 p-8 rounded-[2rem] shadow-xl text-white space-y-6 sticky top-24">
                    <div class="flex items-center gap-4 border-b border-white/10 pb-6">
                        <div
                            class="size-12 bg-red-600 rounded-xl flex items-center justify-center font-black text-white shadow-lg shadow-red-900/50">
                            {{ substr($this->userData->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Identitas Login</p>
                            <p class="font-bold text-lg leading-tight">{{ $this->userData->name }}</p>
                        </div>
                    </div>

                    @if($step == 2)
                        <flux:select label="Lokasi Ruangan" wire:model.live="room_id" class="dark transition-all">
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($rooms as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </flux:select>
                    @else
                        <div class="space-y-1">
                            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Tujuan / Kelas</p>
                            <p class="font-bold text-white">{{ $this->userData->class->name ?? 'Internal Staff' }}</p>
                        </div>
                    @endif

                    <flux:textarea label="Catatan" wire:model="notes" rows="3"
                        placeholder="Alasan peminjaman atau detail lainnya..." />

                    <div class="pt-4">
                        <flux:button wire:click="submitRequest" variant="primary"
                            class="w-full py-4 font-black bg-red-600 hover:bg-red-700 shadow-lg shadow-red-900/20"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Kirim Permintaan</span>
                            <span wire:loading>Memproses...</span>
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KONTEN TAB: STATUS --}}
    <div x-show="tab === 'status'" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95">
        <div class="bg-white rounded-[2rem] border border-zinc-200 shadow-sm overflow-hidden">
            @livewire('student-request-status')
        </div>
    </div>

</div>