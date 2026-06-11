<div class="h-screen w-full flex flex-col p-6 gap-6 relative bg-[#09090b] text-slate-200 overflow-hidden">

    {{-- SCANNER INPUT (HIDDEN) --}}
    <input wire:model.live="searchRfid" type="text" autofocus onblur="this.focus()"
        class="absolute opacity-0 pointer-events-none">

    {{-- HEADER --}}
    <header
        class="bg-zinc-900/50 border border-zinc-800 rounded-3xl p-5 shadow-xl backdrop-blur-md flex items-center justify-between shrink-0">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-zinc-800 rounded-2xl border border-zinc-700 flex items-center justify-center p-2">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/9c/Logo_of_Ministry_of_Education_and_Culture_of_Republic_of_Indonesia.svg"
                    class="h-full w-auto brightness-90">
            </div>
            <div>
                <h1 class="text-xl font-black text-white tracking-tight uppercase leading-none">KIOS ABSENSI DIGITAL
                </h1>
                <p class="text-zinc-200 font-bold text-xs mt-1 uppercase tracking-widest">Gate Terminal</p>
            </div>
        </div>

        <div class="bg-zinc-800/50 border border-zinc-700 rounded-2xl px-6 py-2.5">
            <p id="kios-clock" class="text-green-500 font-black text-lg font-mono tracking-tighter">Memuat Waktu...</p>
        </div>
    </header>

    {{-- KONTEN UTAMA: 2 KOLOM --}}
    <main class="flex-1 flex flex-row gap-6 min-h-0">

        {{-- KOLOM KIRI: SCANNER AREA (Lebar Tetap) --}}
        <section class="w-[400px] shrink-0 flex flex-col gap-6">
            {{-- STATUS CARD --}}
            <div
                class="flex-1 bg-zinc-900/30 border border-zinc-800 rounded-[2.5rem] p-8 flex flex-col items-center justify-center relative overflow-hidden">
                @if($lastTap)
                    {{-- Foto Siswa --}}
                    <div
                        class="w-64 h-80 rounded-[2rem] border-4 {{ $lastTap->status == 'terlambat' ? 'border-rose-500' : 'border-emerald-500' }} overflow-hidden shadow-2xl mb-6 bg-zinc-800 transition-all duration-500">
                        @if($lastTap->student->avatar)
                            <img src="{{ asset('storage/' . $lastTap->student->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <div
                                class="w-full h-full flex items-center justify-center text-6xl font-black text-zinc-700 bg-zinc-800">
                                {{ substr($lastTap->student->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    {{-- Info Siswa --}}
                    <div class="text-center">
                        <h2 class="text-3xl font-black text-white uppercase leading-tight px-4">
                            {{ $lastTap->student->name }}
                        </h2>
                        <p class="text-zinc-500 font-bold mt-2 uppercase tracking-[0.2em]">
                            {{ $lastTap->student->class->name ?? 'STAFF' }}
                        </p>
                    </div>

                    {{-- Badge Status --}}
                    <div class="mt-8 w-full">
                        <div
                            class="py-4 rounded-2xl text-center font-black uppercase tracking-[0.3em] text-lg border-2 {{ $lastTap->status == 'terlambat' ? 'bg-rose-500/20 border-rose-500 text-rose-500' : 'bg-emerald-500/20 border-emerald-500 text-emerald-500' }}">
                            {{ strtoupper($lastTap->status) }}
                        </div>
                    </div>
                @else
                    {{-- Standby Mode --}}
                    <div class="flex flex-col items-center opacity-30 text-zinc-350">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                            stroke="currentColor" class="size-32 mb-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.834.166-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243-1.59-1.59" />
                        </svg>
                        <p class="text-center font-black uppercase tracking-[0.4em] text-sm leading-relaxed">Silakan
                            Tempelkan<br>Kartu Identitas</p>
                    </div>
                @endif

                {{-- Alert Floating Overlay --}}
                <div wire:key="alert-container-{{ now()->toDateTimeString() }}">
                    {{-- Overlay Error --}}
                    @if($message && $status == 'error')
                        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                            class="absolute inset-0 z-50 bg-rose-600/90 backdrop-blur-md flex flex-col items-center justify-center p-8 text-center animate-pulse">
                            <svg class="size-20 text-white mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-white font-black text-2xl uppercase tracking-wider">{{ $message }}</p>
                        </div>
                    @endif

                    {{-- Overlay Success --}}
                    @if($message && $status == 'success')
                        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                            class="absolute inset-0 z-50 bg-emerald-600/90 backdrop-blur-md flex flex-col items-center justify-center p-8 text-center animate-pulse">
                            <svg class="size-20 text-white mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <p class="text-white font-black text-2xl uppercase tracking-wider">{{ $message }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- KOLOM KANAN: TABEL RIWAYAT --}}
        <section
            class="flex-1 bg-zinc-900/30 border border-zinc-800 rounded-[2.5rem] flex flex-col overflow-hidden backdrop-blur-sm">
            <div class="p-8 pb-5 flex items-center justify-between border-b border-zinc-800/50">
                <h3 class="text-xs font-black text-zinc-350 uppercase tracking-[0.4em] flex items-center gap-3">
                    LOG AKTIVITAS HARI INI
                </h3>
                <span
                    class="px-4 py-1.5 bg-zinc-800 rounded-lg text-[10px] font-black text-zinc-350 border border-zinc-700">TERBARU:
                    {{ count($recentTaps) }}</span>
            </div>

            <div class="flex-1 overflow-y-auto no-scrollbar p-8 pt-4">
                <table class="w-full border-separate border-spacing-y-3">
                    <thead class="sticky top-0 bg-[#09090b]/80 backdrop-blur-md z-10">
                        <tr class="text-zinc-350 text-[10px] font-black uppercase tracking-[0.2em]">
                            <th class="px-6 py-4 text-left">INFORMASI SISWA</th>
                            <th class="px-6 py-4 text-center">MASUK</th>
                            <th class="px-6 py-4 text-center">PULANG</th> {{-- Kolom Jam Pulang Ditambahkan --}}
                            <th class="px-6 py-4 text-right">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTaps as $tap)
                            <tr class="group">
                                {{-- Nama & Kelas --}}
                                <td
                                    class="bg-zinc-900/60 group-hover:bg-zinc-800/80 border-y border-l border-zinc-800 rounded-l-2xl px-6 py-4 transition-all">
                                    <div
                                        class="font-bold text-white text-sm uppercase tracking-wide group-hover:text-blue-400 transition-colors">
                                        {{ $tap->student->name }}
                                    </div>
                                    <div class="text-[10px] font-bold text-zinc-350 uppercase mt-0.5">
                                        {{ $tap->student->class->name ?? 'STAFF' }}
                                    </div>
                                </td>

                                {{-- Jam Masuk (Warna Hijau) --}}
                                <td
                                    class="bg-zinc-900/60 group-hover:bg-zinc-800/80 border-y border-zinc-800 px-6 py-4 text-center font-black text-emerald-400 font-mono text-base">
                                    {{ $tap->time_in ? \Carbon\Carbon::parse($tap->time_in)->format('H:i') : '--:--' }}
                                </td>

                                {{-- Jam Pulang (Warna Oranye - Sesuai Mockup) --}}
                                <td
                                    class="bg-zinc-900/60 group-hover:bg-zinc-800/80 border-y border-zinc-800 px-6 py-4 text-center font-black text-orange-400 font-mono text-base">
                                    {{ $tap->time_out ? \Carbon\Carbon::parse($tap->time_out)->format('H:i') : '--:--' }}
                                </td>

                                {{-- Status Badge --}}
                                <td
                                    class="bg-zinc-900/60 group-hover:bg-zinc-800/80 border-y border-r border-zinc-800 rounded-r-2xl px-6 py-4 text-right">
                                    <span
                                        class="inline-block px-3 py-1.5 rounded-lg font-black text-[10px] uppercase tracking-wider {{ $tap->status == 'terlambat' ? 'bg-rose-500/10 text-rose-500 border border-rose-500/20' : 'bg-green-500/10 text-green-500 border border-green-500/20' }}">
                                        {{ $tap->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="py-20 text-center text-zinc-800 uppercase font-black tracking-widest opacity-30">
                                    Belum ada aktivitas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<script>
    function updateKiosTime() {
        const now = new Date();
        const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        const time = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
        const date = now.toLocaleDateString('id-ID', options).toUpperCase();
        document.getElementById('kios-clock').innerHTML = `${time} <span class="ml-2 text-[10px] text-zinc-500 font-bold">${date}</span>`;
    }
    setInterval(updateKiosTime, 1000);
    updateKiosTime();
</script>