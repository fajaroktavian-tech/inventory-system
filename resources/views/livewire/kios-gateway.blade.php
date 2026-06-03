<div class="min-h-screen bg-zinc-50 flex flex-col items-center justify-center p-6">
    <div class="max-w-4xl w-full text-center space-y-12">
        <div>
            <h1 class="text-4xl font-black text-zinc-900 tracking-tight uppercase">Layanan Mandiri Sarpras</h1>
            <p class="text-zinc-500 mt-2">SMK Negeri 7 Baleendah - Silakan pilih layanan di bawah ini</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            {{-- TOMBOL KIOS BHP --}}
            <a href="{{ route('rfid.request') }}" 
                class="group bg-white p-10 rounded-[3rem] shadow-xl border-b-8 border-red-600 hover:scale-105 transition-all flex flex-col items-center">
                <div class="bg-red-50 p-6 rounded-full mb-6 group-hover:bg-red-100 transition-colors">
                    <flux:icon name="archive-box" class="size-20 text-red-600" />
                </div>
                <h2 class="text-2xl font-black text-zinc-900 uppercase">Barang Habis Pakai</h2>
                <p class="text-zinc-500 text-sm mt-2 font-medium italic">(Spidol, Kertas, Tinta, dll)</p>
            </a>

            {{-- TOMBOL KIOS ASET --}}
            <a href="{{ route('kios-aset') }}" 
                class="group bg-white p-10 rounded-[3rem] shadow-xl border-b-8 border-blue-600 hover:scale-105 transition-all flex flex-col items-center">
                <div class="bg-blue-50 p-6 rounded-full mb-6 group-hover:bg-blue-100 transition-colors">
                    <flux:icon name="computer-desktop" class="size-20 text-blue-600" />
                </div>
                <h2 class="text-2xl font-black text-zinc-900 uppercase">Peminjaman Aset</h2>
                <p class="text-zinc-500 text-sm mt-2 font-medium italic">(Infocus, Laptop, Kamera, dll)</p>
            </a>
        </div>

        <div class="pt-10">
            <a href="{{ route('home') }}" class="text-zinc-400 hover:text-zinc-600 font-bold text-xs uppercase tracking-widest">
                ← Kembali ke Beranda
            </a>
        </div>
    </div>
</div>