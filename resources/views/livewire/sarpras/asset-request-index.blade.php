<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">Pengajuan & Pemeliharaan Aset</flux:heading>
            <flux:subheading>Kelola pengadaan barang baru, BHP, serta laporan perbaikan inventaris.</flux:subheading>
        </div>
        @php
            $allowedProcurementRoles = ['admin', 'petugas', 'kesiswaan', 'hubin', 'koordinator_guru', 'petugas_perpus', 'kaprog'];
            $canProcure = in_array(Auth::user()->role, $allowedProcurementRoles);
        @endphp

        @if($canProcure)
            <flux:button variant="primary" icon="plus" wire:click="openModal">Buat Pengajuan Baru</flux:button>
        @else
            <flux:button variant="primary" icon="plus" wire:click="openModal">Laporkan Kerusakan</flux:button>
        @endif
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success" class="mb-4">{{ session('message') }}</flux:callout>
    @endif

    <!-- Tab Navigasi -->
    <div class="flex gap-2 border-b pb-3 mb-4">
        @if($canProcure)
            <flux:button variant="{{ $activeTab === 'procurement' ? 'primary' : 'subtle' }}"
                wire:click="$set('activeTab', 'procurement')">
                Pengadaan Aset & BHP
            </flux:button>
        @endif
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
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-zinc-50 border-b text-xs uppercase text-zinc-600">
                        <tr>
                            <th class="p-3 w-12 text-center">No</th>
                            <th class="p-3">Bukti / Foto</th>
                            <th class="p-3">Pemohon</th>
                            <th class="p-3">Jenis</th>
                            <th class="p-3">Nama Barang</th>
                            <th class="p-3">Qty</th>
                            <th class="p-3">Estimasi Harga</th>
                            <th class="p-3">Alasan & Spesifikasi </th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm">
                        @forelse($procurements as $index => $item)
                            <tr>
                                <td class="p-3 text-center text-zinc-500">{{ $procurements->firstItem() + $index }}</td>
                                <td class="p-3">
                                    @if($item->photo)
                                        <a href="{{ Storage::url($item->photo) }}" target="_blank"
                                            class="block w-10 h-10 rounded overflow-hidden border hover:opacity-80 transition">
                                            <img src="{{ Storage::url($item->photo) }}" alt="Bukti"
                                                class="w-full h-full object-cover">
                                        </a>
                                    @else
                                        <span class="text-xs text-zinc-400 italic">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="p-3 font-medium">{{ $item->user->name ?? '-' }}</td>
                                <td class="p-3"><span
                                        class="px-2 py-1 text-xs rounded uppercase font-bold {{ $item->type == 'aset' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">{{ $item->type }}</span>
                                </td>
                                <td class="p-3 font-semibold">{{ $item->item_name }}</td>
                                <td class="p-3">{{ $item->qty }}</td>
                                <td class="p-3">Rp {{ number_format($item->estimated_price, 0, ',', '.') }}</td>
                                <td class="p-3 text-zinc-600 text-xs">{{ $item->reason }}</td>
                                <td class="p-3">
                                    <span
                                        class="px-2 py-1 text-xs rounded font-medium 
                                                                                @if($item->status == 'pending') bg-yellow-100 text-yellow-800
                                                                                @elseif($item->status == 'approved' || $item->status == 'completed') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <div class="flex justify-center flex-nowrap gap-1.5 min-w-[120px]">
                                        @if(Auth::user()->isAdmin() || Auth::user()->isPetugas())
                                            {{-- Aksi Admin --}}
                                            @if($item->status === 'pending')
                                                {{-- Ikon Setuju (Filled Hijau) --}}
                                                <flux:button variant="primary" size="sm" icon="check-circle"
                                                    class="bg-green-600 text-white hover:bg-green-700" title="Setujui Pengajuan"
                                                    wire:click="updateStatus({{ $item->id }}, 'approved', 'procurement')" />

                                                {{-- Ikon Tolak (Filled Merah/Amber) --}}
                                                <flux:button variant="primary" size="sm" icon="x-circle"
                                                    class="bg-amber-600 text-white hover:bg-amber-700" title="Tolak Pengajuan"
                                                    wire:click="updateStatus({{ $item->id }}, 'rejected', 'procurement')" />

                                                {{-- Ikon Hapus Admin (Filled Merah) --}}
                                                <flux:button variant="primary" size="sm" icon="trash"
                                                    class="bg-red-600 text-white hover:bg-red-700" title="Hapus Permanen (Data Salah)"
                                                    wire:click="deleteRequest({{ $item->id }}, 'procurement')"
                                                    wire:confirm="Yakin ingin menghapus permanen pengajuan yang salah ini?" />
                                            @elseif($item->status === 'cancelled')
                                                <span class="text-xs text-zinc-400 italic">User Batal</span>
                                            @else
                                                {{-- Tombol Ulangi (Filled Zinc) --}}
                                                <flux:button variant="filled" size="sm" icon="arrow-path"
                                                    class="bg-zinc-500 text-white hover:bg-zinc-600"
                                                    title="Batalkan / Kembalikan ke Pending"
                                                    wire:click="updateStatus({{ $item->id }}, 'pending', 'procurement')" />
                                            @endif
                                        @else
                                            {{-- Aksi User Pemohon --}}
                                            @if($item->status === 'pending')
                                                {{-- Ikon Batalkan User (Filled Amber) --}}
                                                <flux:button variant="filled" size="sm" icon="x-mark"
                                                    class="bg-amber-700 text-white hover:bg-amber-800" title="Batalkan Pengajuan Anda"
                                                    wire:click="cancelRequest({{ $item->id }}, 'procurement')"
                                                    wire:confirm="Yakin ingin membatalkan pengajuan ini? Data tidak akan dihapus, hanya dibatalkan." />
                                            @else
                                                <span class="text-xs text-zinc-400 italic">-</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
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
        </div>
    @endif

    <!-- TAB 2: MAINTENANCE / PERBAIKAN -->
    @if($activeTab === 'maintenance')
        <div class="bg-white rounded-lg shadow overflow-hidden border">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50 border-b text-xs uppercase text-zinc-600">
                    <tr>
                        <th class="p-3 w-12 text-center">No</th>
                        <th class="p-3">Bukti / Foto</th>
                        <th class="p-3">Pelapor</th>
                        <th class="p-3">Objek / Barang / Fasilitas</th>
                        <th class="p-3">Lokasi</th>
                        <th class="p-3">Kerusakan</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-center w-28">Aksi</th>

                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    @forelse($maintenances as $index => $item)
                        <tr>
                            <td class="p-3 text-center text-zinc-500">{{ $maintenances->firstItem() + $index }}</td>
                            <td class="p-3">
                                @if($item->photo)
                                    <a href="{{ Storage::url($item->photo) }}" target="_blank"
                                        class="block w-10 h-10 rounded overflow-hidden border hover:opacity-80 transition">
                                        <img src="{{ Storage::url($item->photo) }}" alt="Bukti" class="w-full h-full object-cover">
                                    </a>
                                @else
                                    <span class="text-xs text-zinc-400 italic">Tidak ada</span>
                                @endif
                            <td class="p-3 font-medium">{{ $item->user->name ?? '-' }}</td>
                            <td class="p-3 font-semibold">
                                @if($item->asset_id)
                                    {{ $item->asset->itemInfo->name ?? '-' }} <br>
                                    <span class="text-xs text-zinc-400 font-normal">S/N:
                                        {{ $item->asset->serial_number ?? '' }}</span>
                                @else
                                    <span class="text-blue-600">Fasilitas: {{ $item->facility_name }}</span>
                                @endif
                            </td>
                            <td class="p-3 text-xs uppercase">
                                {{ $item->asset->room->name ?? $item->room->name ?? '-' }}
                            </td>
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
                            <td class="p-3 text-center">
                                <div class="flex justify-center flex-nowrap gap-1.5 min-w-[120px]">
                                    @if(Auth::user()->isAdmin() || Auth::user()->isPetugas())
                                        {{-- Aksi Admin --}}
                                        @if($item->status === 'pending')
                                            {{-- Ikon Proses (Filled Biru) --}}
                                            <flux:button variant="primary" size="sm" icon="arrow-right-circle"
                                                class="bg-blue-600 text-white hover:bg-blue-700" title="Proses Laporan Ini"
                                                wire:click="updateStatus({{ $item->id }}, 'process', 'maintenance')" />

                                            {{-- Ikon Diganti (Filled Amber) --}}
                                            <flux:button variant="primary" size="sm" icon="wrench-screwdriver"
                                                class="bg-amber-600 text-white hover:bg-amber-700" title="Objek Harus Diganti"
                                                wire:click="updateStatus({{ $item->id }}, 'replaced', 'maintenance')" />

                                            {{-- Ikon Hapus Admin (Filled Merah) --}}
                                            <flux:button variant="primary" size="sm" icon="trash"
                                                class="bg-red-600 text-white hover:bg-red-700" title="Hapus Permanen (Data Salah)"
                                                wire:click="deleteRequest({{ $item->id }}, 'maintenance')"
                                                wire:confirm="Yakin ingin menghapus permanen laporan yang salah ini?" />
                                        @elseif($item->status === 'process')
                                            {{-- Ikon Selesai (Filled Hijau) --}}
                                            <flux:button variant="filled" size="sm" icon="check-badge"
                                                class="bg-green-600 text-white hover:bg-green-700" title="Tandai Selesai Diperbaiki"
                                                wire:click="updateStatus({{ $item->id }}, 'repaired', 'maintenance')" />

                                            {{-- Ikon Ulangi (Filled Zinc) --}}
                                            <flux:button variant="filled" size="sm" icon="arrow-path"
                                                class="bg-zinc-500 text-white hover:bg-zinc-600" title="Kembalikan ke Pending"
                                                wire:click="updateStatus({{ $item->id }}, 'pending', 'maintenance')" />
                                        @elseif($item->status === 'cancelled')
                                            <span class="text-xs text-zinc-400 italic">User Batal</span>
                                        @else
                                            {{-- Tombol Ulangi (Filled Zinc) --}}
                                            <flux:button variant="filled" size="sm" icon="arrow-path"
                                                class="bg-zinc-500 text-white hover:bg-zinc-600"
                                                title="Batalkan / Kembalikan ke Pending"
                                                wire:click="updateStatus({{ $item->id }}, 'pending', 'maintenance')" />
                                        @endif
                                    @else
                                        {{-- Aksi User Pemohon --}}
                                        @if($item->status === 'pending')
                                            {{-- Ikon Batalkan User (Filled Amber) --}}
                                            <flux:button variant="filled" size="sm" icon="x-mark"
                                                class="bg-amber-700 text-white hover:bg-amber-800" title="Batalkan Laporan Anda"
                                                wire:click="cancelRequest({{ $item->id }}, 'maintenance')"
                                                wire:confirm="Yakin ingin membatalkan laporan ini? Data tidak akan dihapus, hanya dibatalkan." />
                                        @else
                                            <span class="text-xs text-zinc-400 italic">-</span>
                                        @endif
                                    @endif
                                </div>
                            </td>
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
    <flux:modal wire:model="isModalOpen" class="md:w-[500px]" wire:key="request-modal">
        <div class="space-y-4 p-2" wire:key="modal-content">
            <flux:heading size="lg">Formulir Pengajuan / Laporan</flux:heading>

            <div class="flex gap-2 mb-2">
                @if($canProcure)
                    <flux:button variant="{{ $activeTab === 'procurement' ? 'primary' : 'subtle' }}"
                        wire:click="$set('activeTab', 'procurement')">Pengadaan Barang</flux:button>
                @endif
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
                    <flux:textarea wire:model="reason" label="Alasan Pengajuan & Spesifikasi Barang" rows="3"
                        placeholder="Jelaskan untuk keperluan KBM dan Spesifikasi..." required />

                    <!-- TAMBAHAN: Input Upload Foto untuk Pengadaan -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Foto / Brosur / Dokumen
                            Pendukung (Opsional)</label>
                        <input type="file" wire:model="photoProcurement" accept="image/*" class="block w-full text-xs text-zinc-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-md file:border-0
                                                file:text-xs file:font-semibold
                                                file:bg-zinc-100 file:text-zinc-700
                                                hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:file:text-zinc-200
                                            " />
                        <span class="text-[11px] text-zinc-400">Format: JPG, PNG, JPEG. Maksimal ukuran: 2MB.</span>
                        @error('photoProcurement') <span class="text-xs text-red-600 block">{{ $message }}</span> @enderror

                        <!-- Preview Foto -->
                        @if ($photoProcurement)
                            <div class="mt-2 relative w-24 h-24 rounded-lg overflow-hidden border border-zinc-200">
                                <img src="{{ $photoProcurement->temporaryUrl() }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <flux:modal.close>
                            <flux:button variant="subtle">Batal</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">Kirim Pengajuan</flux:button>
                    </div>
                </form>
            @else
                <form wire:submit.prevent="saveMaintenance" class="space-y-3">
                    <flux:select wire:model.live="maintenance_type" label="Jenis Kerusakan yang Dilaporkan">
                        <option value="asset">Aset Inventaris Alat (Contoh: Proyektor, PC, Printer)</option>
                        <option value="facility">Fasilitas / Infrastruktur Ruangan (Contoh: Pipa, Listrik, Pintu)</option>
                    </flux:select>

                    @if($maintenance_type === 'asset')
                        <!-- Input Pencarian Aset Rusak -->
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

                            @if(!$asset_id && !empty($assetSearch))
                                <div
                                    class="absolute z-50 w-full bg-white border border-zinc-200 rounded-lg shadow-lg max-h-48 overflow-y-auto mt-1 divide-y">
                                    @forelse($assetsList as $ast)
                                        <div wire:click="$set('asset_id', {{ $ast->id }})"
                                            class="p-2.5 text-xs hover:bg-zinc-100 cursor-pointer transition">
                                            <div class="font-semibold text-zinc-800">{{ $ast->itemInfo->name ?? '-' }}</div>
                                            <div class="text-zinc-500">
                                                S/N: {{ $ast->serial_number ?? 'N/A' }} | Ruang: {{ $ast->room->name ?? '-' }}
                                                <span
                                                    class="font-medium 
                                                                                                                                                                                            @if($ast->condition == 'baik') text-green-600 
                                                                                                                                                                                            @else text-amber-600 @endif">
                                                    ({{ ucwords(str_replace('_', ' ', $ast->condition)) }})
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-3 text-xs text-center text-zinc-500">Tidak ada aset rusak yang cocok.</div>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Input untuk Fasilitas Umum / Non-Aset -->
                        <flux:input wire:model="facility_name" label="Nama Fasilitas / Bagian yang Rusak"
                            placeholder="Misal: Pipa Air Toilet / Saklar Listrik Kelas X" required />

                        <flux:select wire:model="room_id" label="Lokasi Ruangan">
                            <option value="">Pilih Ruangan...</option>
                            @foreach($roomsList as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </flux:select>
                    @endif

                    <flux:textarea wire:model="damage_description" label="Deskripsi Kerusakan"
                        placeholder="Jelaskan kendala atau kerusakannya secara detail..." required />

                    <!-- Input Upload Foto & Preview -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Foto Bukti / Dokumen Pendukung
                            (Opsional)</label>
                        <input type="file" wire:model="photoMaintenance" accept="image/*" class="block w-full text-xs text-zinc-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-xs file:font-semibold
                                        file:bg-zinc-100 file:text-zinc-700
                                        hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:file:text-zinc-200
                                    " />
                        <span class="text-[11px] text-zinc-400">Format: JPG, PNG, JPEG. Maksimal ukuran: 2MB.</span>
                        @error('photoMaintenance') <span class="text-xs text-red-600 block">{{ $message }}</span> @enderror

                        <!-- Preview Foto -->
                        @if ($photoMaintenance)
                            <div class="mt-2 relative w-24 h-24 rounded-lg overflow-hidden border border-zinc-200">
                                <img src="{{ $photoMaintenance->temporaryUrl() }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                    </div>

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