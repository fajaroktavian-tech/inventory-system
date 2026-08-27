<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Pengajuan & Pemeliharaan Aset</flux:heading>
            <flux:subheading>Kelola pengadaan barang baru, BHP, serta laporan perbaikan inventaris.</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="openModal">Buat Pengajuan Baru</flux:button>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success" class="mb-4">{{ session('message') }}</flux:callout>
    @endif

    <!-- Tab Navigasi -->
    <div class="flex gap-2 border-b pb-3 mb-4">
        <flux:button variant="{{ $activeTab === 'procurement' ? 'primary' : 'subtle' }}"
            wire:click="$set('activeTab', 'procurement')">
            Pengadaan Aset & BHP
        </flux:button>
        <flux:button variant="{{ $activeTab === 'maintenance' ? 'primary' : 'subtle' }}"
            wire:click="$set('activeTab', 'maintenance')">
            Laporan Perbaikan (Maintenance)
        </flux:button>
    </div>

    <!-- Filter & Pencarian -->
    <div class="flex gap-4 mb-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama barang atau keterangan..."
            icon="magnifying-glass" class="max-w-xs" />
        <flux:select wire:model.live="filterStatus" class="max-w-xs">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Disetujui / Diperbaiki</option>
            <option value="rejected">Ditolak</option>
            <option value="completed">Selesai</option>
        </flux:select>
    </div>

    <!-- TAB 1: PENGADAAN -->
    @if($activeTab === 'procurement')
        <div class="bg-white rounded-lg shadow overflow-hidden border">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50 border-b text-xs uppercase text-zinc-600">
                    <tr>
                        <th class="p-3 w-12 text-center">No</th>
                        <th class="p-3">Pemohon</th>
                        <th class="p-3">Jenis</th>
                        <th class="p-3">Nama Barang</th>
                        <th class="p-3">Qty</th>
                        <th class="p-3">Estimasi Harga</th>
                        <th class="p-3">Alasan / Keterangan</th>
                        <th class="p-3">Status</th>
                        @if(Auth::user()->isAdmin() || Auth::user()->isPetugas())
                            <th class="p-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    @forelse($procurements as $index => $item)
                        <tr>
                            <td class="p-3 text-center text-zinc-500">{{ $procurements->firstItem() + $index }}</td>
                            <td class="p-3 font-medium">{{ $item->user->name ?? '-' }}</td>
                            <td class="p-3"><span
                                    class="px-2 py-1 text-xs rounded uppercase font-bold {{ $item->type == 'aset' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">{{ $item->type }}</span>
                            </td>
                            <td class="p-3 font-semibold">{{ $item->item_name }}</td>
                            <td class="p-3">{{ $item->qty }}</td>
                            <td class="p-3">Rp {{ number_format($item->estimated_price, 0, ',', '.') }}</td>
                            <td class="p-3 text-zinc-600 text-xs">{{ $item->reason }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 text-xs rounded font-medium 
                                                                    @if($item->status == 'pending') bg-yellow-100 text-yellow-800
                                                                    @elseif($item->status == 'approved' || $item->status == 'completed') bg-green-100 text-green-800
                                                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            @if(Auth::user()->isAdmin() || Auth::user()->isPetugas())
                                <td class="p-3 text-center">
                                    @if($item->status === 'pending')
                                        <div class="flex justify-center gap-1">
                                            <flux:button size="sm" variant="primary"
                                                wire:click="updateStatus({{ $item->id }}, 'approved', 'procurement')">Setuju
                                            </flux:button>
                                            <flux:button size="sm" class="bg-red-600 text-white hover:bg-red-700"
                                                wire:click="updateStatus({{ $item->id }}, 'rejected', 'procurement')">Tolak
                                            </flux:button>
                                        </div>
                                    @else
                                        {{-- Tombol koreksi jika salah klik / ingin dikembalikan ke pending --}}
                                        <div class="flex justify-center items-center gap-2">
                                            <span class="text-xs text-zinc-400 italic">Selesai</span>
                                            <flux:button size="sm" variant="subtle" title="Batalkan / Kembalikan ke Pending"
                                                wire:click="updateStatus({{ $item->id }}, 'pending', 'procurement')">
                                                Ulangi
                                            </flux:button>
                                        </div>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-4 text-center text-zinc-500">Belum ada pengajuan pengadaan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $procurements->links() }}</div>
        </div>
    @endif

    <!-- TAB 2: MAINTENANCE / PERBAIKAN -->
    @if($activeTab === 'maintenance')
        <div class="bg-white rounded-lg shadow overflow-hidden border">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50 border-b text-xs uppercase text-zinc-600">
                    <tr>
                        <th class="p-3 w-12 text-center">No</th>
                        <th class="p-3">Pelapor</th>
                        <th class="p-3">Nama Aset / S/N</th>
                        <th class="p-3">Lokasi Aset</th>
                        <th class="p-3">Kerusakan</th>
                        <th class="p-3">Status</th>
                        @if(Auth::user()->isAdmin() || Auth::user()->isPetugas())
                            <th class="p-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    @forelse($maintenances as $index => $item)
                        <tr>
                            <td class="p-3 text-center text-zinc-500">{{ $maintenances->firstItem() + $index }}</td>
                            <td class="p-3 font-medium">{{ $item->user->name ?? '-' }}</td>
                            <td class="p-3 font-semibold">{{ $item->asset->itemInfo->name ?? '-' }} <br><span
                                    class="text-xs text-zinc-400 font-normal">{{ $item->asset->serial_number ?? '' }}</span>
                            </td>
                            <td class="p-3 text-xs uppercase">{{ $item->asset->room->name ?? '-' }}</td>
                            <td class="p-3 text-zinc-600 text-xs">{{ $item->damage_description }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 text-xs rounded font-medium 
                                                                    @if($item->status == 'pending') bg-yellow-100 text-yellow-800
                                                                    @elseif($item->status == 'process') bg-blue-100 text-blue-800
                                                                    @elseif($item->status == 'repaired') bg-green-100 text-green-800
                                                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            @if(Auth::user()->isAdmin() || Auth::user()->isPetugas())
                                <td class="p-3 text-center">
                                    @if($item->status === 'pending')
                                        <div class="flex justify-center gap-1">
                                            <flux:button size="sm" variant="primary"
                                                wire:click="updateStatus({{ $item->id }}, 'process', 'maintenance')">Proses
                                            </flux:button>
                                            <flux:button size="sm" class="bg-red-600 text-white hover:bg-red-700"
                                                wire:click="updateStatus({{ $item->id }}, 'rejected', 'maintenance')">Tolak
                                            </flux:button>
                                        </div>
                                    @elseif($item->status === 'process')
                                        <div class="flex justify-center gap-1">
                                            <flux:button size="sm" class="bg-green-600 text-white hover:bg-green-700"
                                                wire:click="updateStatus({{ $item->id }}, 'repaired', 'maintenance')">Selesai
                                            </flux:button>
                                            <flux:button size="sm" variant="subtle" title="Kembalikan ke Pending"
                                                wire:click="updateStatus({{ $item->id }}, 'pending', 'maintenance')">
                                                Ulangi
                                            </flux:button>
                                        </div>
                                    @else
                                        {{-- Tombol koreksi untuk status selesai / ditolak / repaired --}}
                                        <div class="flex justify-center items-center gap-2">
                                            <span class="text-xs text-zinc-400 italic">Selesai</span>
                                            <flux:button size="sm" variant="subtle" title="Batalkan / Kembalikan ke Pending"
                                                wire:click="updateStatus({{ $item->id }}, 'pending', 'maintenance')">
                                                Ulangi
                                            </flux:button>
                                        </div>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-zinc-500">Belum ada laporan perbaikan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $maintenances->links() }}</div>
        </div>
    @endif

    <!-- MODAL FORM PENGAJUAN -->
    <flux:modal wire:model="isModalOpen" class="md:w-[500px]">
        <div class="space-y-4 p-2">
            <flux:heading size="lg">Formulir Pengajuan / Laporan</flux:heading>

            <div class="flex gap-2 mb-2">
                <flux:button variant="{{ $activeTab === 'procurement' ? 'primary' : 'subtle' }}"
                    wire:click="$set('activeTab', 'procurement')">Pengadaan Barang</flux:button>
                <flux:button variant="{{ $activeTab === 'maintenance' ? 'primary' : 'subtle' }}"
                    wire:click="$set('activeTab', 'maintenance')">Laporkan Kerusakan</flux:button>
            </div>

            @if($activeTab === 'procurement')
                <form wire:submit.prevent="saveProcurement" class="space-y-3">
                    <flux:select wire:model="type" label="Kategori Pengajuan">
                        <option value="aset">Aset Inventaris (Contoh: Proyektor, Meja, dll)</option>
                        <option value="bhp">Barang Habis Pakai / BHP (Contoh: Tinta, Spidol, Kertas)</option>
                    </flux:select>
                    <flux:input wire:model="item_name" label="Nama Barang" placeholder="Masukkan nama barang yang diajukan"
                        required />
                    <div class="grid grid-cols-2 gap-2">
                        <flux:input wire:model="qty" type="number" label="Jumlah (Qty)" min="1" required />
                        <flux:input wire:model="estimated_price" type="number" label="Estimasi Harga Satuan (Rp)"
                            placeholder="Opsional" />
                    </div>
                    <flux:textarea wire:model="reason" label="Alasan / Kebutuhan"
                        placeholder="Jelaskan untuk keperluan KBM / Kegiatan apa..." required />
                    <div class="flex justify-end gap-2 mt-4">
                        <flux:modal.close>
                            <flux:button variant="subtle">Batal</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">Kirim Pengajuan</flux:button>
                    </div>
                </form>
            @else
                <form wire:submit.prevent="saveMaintenance" class="space-y-3">
                    <!-- Input Pencarian Aset Rusak & Hasil Interaktif -->
                    <div class="relative space-y-1">
                        <label class="text-sm font-medium text-zinc-700">Cari Aset yang Rusak (Nama / S/N / Ruangan)</label>

                        <div class="relative">
                            <flux:input wire:model.live.debounce.300ms="assetSearch"
                                placeholder="Ketik nama barang, nomor seri, atau ruangan..." icon="magnifying-glass" />

                            @if($asset_id)
                                <button type="button" wire:click="$set('asset_id', null)"
                                    class="absolute right-3 top-2.5 text-xs text-red-600 font-semibold hover:underline">
                                    Ganti Aset
                                </button>
                            @endif
                        </div>

                        <!-- Tampilkan Nama Aset yang Sedang Dipilih -->
                        @if($asset_id)
                            @php
                                $selectedAsset = \App\Models\Asset::with('itemInfo', 'room')->find($asset_id);
                            @endphp
                            @if($selectedAsset)
                                <div
                                    class="p-2 bg-green-50 border border-green-200 rounded text-xs text-green-800 flex justify-between items-center">
                                    <div>
                                        <span class="font-bold">Terpilih:</span>
                                        {{ $selectedAsset->itemInfo->name ?? '-' }} | S/N:
                                        {{ $selectedAsset->serial_number ?? 'N/A' }} | Ruang:
                                        {{ $selectedAsset->room->name ?? '-' }}
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Daftar Rekomendasi Hasil Pencarian (Hanya muncul jika belum dipilih & ada keyword ketikan) -->
                        @if(!$asset_id && !empty($assetSearch))
                            <div
                                class="absolute z-50 w-full bg-white border border-zinc-200 rounded-lg shadow-lg max-h-48 overflow-y-auto mt-1 divide-y">
                                @forelse($assetsList as $ast)
                                    <div wire:click="$set('asset_id', {{ $ast->id }})"
                                        class="p-2.5 text-xs hover:bg-zinc-100 cursor-pointer transition">
                                        <div class="font-semibold text-zinc-800">{{ $ast->itemInfo->name ?? '-' }}</div>
                                        <div class="text-zinc-500">S/N: {{ $ast->serial_number ?? 'N/A' }} | Ruang:
                                            {{ $ast->room->name ?? '-' }} <span
                                                class="font-medium text-amber-650">({{ ucwords(str_replace('_', ' ', $ast->condition)) }})</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-3 text-xs text-center text-zinc-500">Tidak ada aset rusak yang cocok dengan kata
                                        kunci "{{ $assetSearch }}".</div>
                                @endforelse
                            </div>
                        @endif
                    </div>

                    <flux:textarea wire:model="damage_description" label="Deskripsi Kerusakan"
                        placeholder="Jelaskan bagian apa yang rusak atau kendalanya..." required />
                    <div class="flex justify-end gap-2 mt-4">
                        <flux:modal.close>
                            <flux:button variant="subtle">Batal</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">Kirim Laporan</flux:button>
                    </div>
                </form>
            @endif
        </div>
    </flux:modal>
</div>