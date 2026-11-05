<?php

namespace App\Livewire\Sarpras;

use App\Models\Asset;
use App\Models\AssetProcurement;
use App\Models\AssetMaintenance;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Image; 
use Spatie\Image\Enums\Fit;

class AssetRequestIndex extends Component
{
    use WithPagination, WithFileUploads;
    public $assetSearch = '';
    public $activeTab = 'procurement';
    public $search = '';
    public $filterStatus = '';

    // Properti Form Pengadaan
    public $procurementId, $type = 'aset', $item_name, $qty = 1, $estimated_price, $reason;
    public $photoProcurement;
    public $photoMaintenance;

    // Properti Form Perbaikan (Maintenance)
    public $maintenanceId, $maintenance_type = 'asset', $asset_id, $facility_name, $room_id, $damage_description;
    public $isModalOpen = false;
    public $isGuideOpen = false;
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedFilterStatus()
    {
        $this->resetPage();
    }
    public function render()
    {
        $user = Auth::user();

        // Query Pengadaan (Aset / BHP)
        $procurementsQuery = AssetProcurement::with('user')
            ->when(!$user->isAdmin() && !$user->isPetugas(), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where(function ($q) {
                $q->where('item_name', 'like', '%' . $this->search . '%')
                    ->orWhere('reason', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->latest();

        // Query Perbaikan diubah agar bisa mencari berdasarkan facility_name atau room
        $maintenancesQuery = AssetMaintenance::with(['user', 'asset.itemInfo', 'asset.room', 'room'])
            ->when(!$user->isAdmin() && !$user->isPetugas(), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where(function ($q) {
                $q->where('damage_description', 'like', '%' . $this->search . '%')
                    ->orWhere('facility_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('asset.itemInfo', function ($sub) {
                        $sub->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->latest();

        // Query untuk pencarian aset di modal perbaikan (Dibatasi untuk Guru/Staff)
        $assetsQuery = Asset::with('itemInfo', 'room')
            ->when(!$user->isAdmin() && !$user->isPetugas(), function ($q) use ($user) {
                // Guru/Staff hanya bisa memilih aset yang PIC-nya adalah diri mereka sendiri
                $q->where('pic_id', $user->id);
            })
            ->when($this->assetSearch, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('serial_number', 'like', '%' . $this->assetSearch . '%')
                        ->orWhereHas('itemInfo', function ($itemSub) {
                            $itemSub->where('name', 'like', '%' . $this->assetSearch . '%');
                        })
                        ->orWhereHas('room', function ($roomSub) {
                            $roomSub->where('name', 'like', '%' . $this->assetSearch . '%');
                        });
                });
            });

        return view('livewire.sarpras.asset-request-index', [
            'procurements' => $this->activeTab === 'procurement' ? $procurementsQuery->paginate(10) : [],
            'maintenances' => $this->activeTab === 'maintenance' ? $maintenancesQuery->paginate(10) : [],
            'assetsList' => $assetsQuery->limit(20)->get(),
            'roomsList' => Room::all(),
        ])->layout('layouts.app');
    }
    public function openModal($tab = null)
    {
        $allowedProcurementRoles = ['admin', 'petugas', 'kesiswaan', 'hubin','kurikulum', 'koordinator_guru', 'petugas_perpus', 'kaprog'];
        $canProcure = in_array(Auth::user()->role, $allowedProcurementRoles);
        $this->reset([
            'procurementId','maintenanceId','item_name','qty','estimated_price','reason',
            'asset_id','damage_description','assetSearch','photoProcurement','photoMaintenance'
        ]);

        $this->qty = 1;
        $this->type = 'aset';
        $this->maintenance_type = 'asset';

        if ($tab) {
            $this->activeTab = $tab;
        } else {
            // Jika user biasa membuka modal, paksa tab langsung ke maintenance
            $this->activeTab = $canProcure ? 'procurement' : 'maintenance';
        }
        $this->isModalOpen = true;
    }

    public function saveProcurement()
    {
        $allowedProcurementRoles = ['admin', 'petugas', 'kesiswaan', 'hubin', 'koordinator_guru', 'petugas_perpus', 'kaprog'];

        // Proteksi keamanan backend
        if (!in_array(Auth::user()->role, $allowedProcurementRoles)) {
            session()->flash('message', 'Akses ditolak. Anda tidak memiliki wewenang melakukan pengadaan.');
            $this->isModalOpen = false;
            return;
        }
        $this->validate([
            'type' => 'required|in:aset,bhp',
            'item_name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'estimated_price' => 'nullable|numeric',
            'reason' => 'required|string',
            'photoProcurement' => 'nullable|image|max:5120',
        ]);

        $photoPath = null;
        if ($this->photoProcurement) {
            $filename = time() . '_' . $this->photoProcurement->getClientOriginalName();
            $folder = 'procurements';
            
            // 2. Tentukan path lengkap penyimpanan (di dalam Named Volume container)
            $targetPath = storage_path('app/public/' . $folder . '/' . $filename);

            // 3. Pastikan direktori tujuan ada, jika tidak, buat
            if (!file_exists(storage_path('app/public/' . $folder))) {
                mkdir(storage_path('app/public/' . $folder), 0777, true);
            }

            // 4. Lakukan Kompresi & Resize dengan Spatie Image
            // Ini akan mengambil file sementara, resize maks HD, set kualitas 70%
            Image::load($this->photoProcurement->getRealPath())
                ->fit(Fit::Max, 1920, 1080) // Maksimal resolusi HD, pertahankan aspect ratio
                ->quality(70) // Set kualitas kompresi ke 70% (keseimbangan ukuran & kejelasan)
                ->save($targetPath); // Simpan hasil kompresi ke Drive D

            // 5. Set path relatif untuk disimpan di Database
            $photoPath = $folder . '/' . $filename;
        }

        AssetProcurement::create([
            'user_id' => Auth::id(),
            'type' => $this->type,
            'item_name' => $this->item_name,
            'qty' => $this->qty,
            'estimated_price' => $this->estimated_price ?? 0,
            'reason' => $this->reason,
            'status' => 'pending',
            'photo' => $photoPath,
        ]);

        $this->isModalOpen = false;
        session()->flash('message', 'Pengajuan pengadaan berhasil dikirim.');
    }

    public function saveMaintenance()
    {
        $this->validate([
            'maintenance_type' => 'required|in:asset,facility',
            'asset_id' => 'required_if:maintenance_type,asset|nullable|exists:assets,id',
            'facility_name' => 'required_if:maintenance_type,facility|nullable|string|max:255',
            'room_id' => 'required_if:maintenance_type,facility|nullable|exists:rooms,id',
            'damage_description' => 'required|string',
            'photoMaintenance' => 'nullable|image|max:5120',
        ]);

        $photoPath = null;
        if ($this->photoMaintenance) {
            // 1. Buat nama file unik
            $filename = time() . '_' . $this->photoMaintenance->getClientOriginalName();
            $folder = 'maintenances';
            
            // 2. Tentukan path lengkap
            $targetPath = storage_path('app/public/' . $folder . '/' . $filename);

            // 3. Pastikan direktori ada
            if (!file_exists(storage_path('app/public/' . $folder))) {
                mkdir(storage_path('app/public/' . $folder), 0777, true);
            }

            // 4. Lakukan Kompresi & Resize
            Image::load($this->photoMaintenance->getRealPath())
                ->fit(Fit::Max, 1920, 1080)
                ->quality(70)
                ->save($targetPath);

            // 5. Set path untuk DB
            $photoPath = $folder . '/' . $filename;
        }

        AssetMaintenance::create([
            'user_id' => Auth::id(),
            'asset_id' => $this->maintenance_type === 'asset' ? $this->asset_id : null,
            'facility_name' => $this->maintenance_type === 'facility' ? $this->facility_name : null,
            'room_id' => $this->maintenance_type === 'facility' ? $this->room_id : null,
            'damage_description' => $this->damage_description,
            'photo' => $photoPath,
            'status' => 'pending',
        ]);

        $this->isModalOpen = false;
        session()->flash('message', 'Laporan perbaikan/pemeliharaan berhasil dikirim.');
    }
    // Aksi untuk Admin / Petugas mengubah status
    public function updateStatus($id, $status, $type = 'procurement')
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isPetugas()) {
            return;
        }

        if ($type === 'procurement') {
            $item = AssetProcurement::findOrFail($id);
            $item->update(['status' => $status]);
        } else {
            $item = AssetMaintenance::findOrFail($id);
            $item->update(['status' => $status]);
        }

        session()->flash('message', 'Status pengajuan berhasil diperbarui.');
    }

    public function deleteRequest($id, $type = 'procurement')
    {
        // 1. Proteksi Keamanan: Hanya Admin/Petugas yang bisa hapus
        if (!Auth::user()->isAdmin() && !Auth::user()->isPetugas()) {
            session()->flash('message', 'Akses ditolak. Anda tidak wewenang menghapus data.');
            return;
        }

        if ($type === 'procurement') {
            $item = AssetProcurement::findOrFail($id);
        } else {
            $item = AssetMaintenance::findOrFail($id);
        }

        // 2. Proteksi Logika: Hanya data 'pending' yang boleh dihapus permanen
        if ($item->status !== 'pending') {
            session()->flash('message', 'Hanya data berstatus Pending yang dapat dihapus permanen.');
            return;
        }

        // 3. Hapus foto jika ada di Drive D
        if ($item->photo) {
            Storage::disk('public')->delete($item->photo);
        }

        // 4. Hapus data permanen dari Database
        $item->delete();

        session()->flash('message', 'Pengajuan/Laporan yang salah berhasil dihapus permanen.');
    }

    // --- FITUR BARU: BATALKAN UNTUK USER (HANYA PENDING) ---
    public function cancelRequest($id, $type = 'procurement')
    {
        if ($type === 'procurement') {
            $item = AssetProcurement::findOrFail($id);
        } else {
            $item = AssetMaintenance::findOrFail($id);
        }

        // 1. Proteksi Keamanan: Hanya pemohon asli yang bisa batalkan
        if ($item->user_id !== Auth::id()) {
            session()->flash('message', 'Akses ditolak. Anda hanya dapat membatalkan pengajuan Anda sendiri.');
            return;
        }

        // 2. Proteksi Logika: Hanya data 'pending' yang boleh dibatalkan
        if ($item->status !== 'pending') {
            session()->flash('message', 'Pengajuan sudah diproses, tidak dapat dibatalkan.');
            return;
        }

        // 3. Ubah status menjadi 'cancelled', JANGAN HAPUS data
        $item->update(['status' => 'cancelled']);

        session()->flash('message', 'Pengajuan/Laporan Anda berhasil dibatalkan.');
    }
    public function mount()
    {
        $user = Auth::user();
        $allowedProcurementRoles = ['admin', 'petugas', 'kesiswaan', 'hubin', 'koordinator_guru', 'petugas_perpus', 'kaprog'];
        
        if (!in_array($user->role, $allowedProcurementRoles)) {
            $this->activeTab = 'maintenance';
        }
    }
}