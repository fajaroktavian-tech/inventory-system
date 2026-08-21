<?php

namespace App\Livewire\Sarpras;

use App\Models\Asset;
use App\Models\AssetItem;
use App\Models\Room;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Exports\AssetExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetRegistration extends Component
{
    use WithPagination;
    public $isAssetGuideOpen = false;
    public $selectedAsset;
    public $filterRoom = '';
    public $filterCondition = '';
    public $bast_date;

    public $search = '';
    public $assetId, $asset_item_id, $room_id, $pic_id, $serial_number, $source_fund, $acquisition_year, $price, $condition = 'baik', $status = 'tersedia';
    public $qty = 1;
    public $isModalOpen = false;
    public $search_catalog = '';
    public $search_room = '';
    public $search_pic = '';
    public $search_filter_room = ''; // Teks yang diketik di input filter
    public $filterRoomName = null;

    // Properti untuk menyimpan pilihan yang sudah diklik
    public $selectedCatalogData = null;
    public $selectedRoomData = null;
    public $selectedPicData = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedBastDate($value)
    {
        if ($value) {
            $this->acquisition_year = \Carbon\Carbon::parse($value)->format('Y');
        }
    }
    public function updatedFilterRoom()
    {
        $this->resetPage();
    }
    public function updatedFilterCondition()
    {
        $this->resetPage();
    }
    protected $rules = [
        'asset_item_id' => 'required',
        'room_id' => 'required',
        'pic_id' => 'required',
        'serial_number' => 'nullable|unique:assets,serial_number',
        'source_fund' => 'required',
        'acquisition_year' => 'required|numeric',
        'price' => 'nullable|numeric',
        'status' => 'required|in:tersedia,dipinjam,hilang,diserahkan',
        'bast_date' => 'required|date',
    ];

    public function render()
    {
        return view('livewire.asset-registration', [
            'assets' => Asset::with(['itemInfo', 'room', 'pic'])
                ->where(function ($query) {
                    $query->whereHas('itemInfo', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                        ->orWhere('serial_number', 'like', '%' . $this->search . '%');
                })
                // Filter berdasarkan Ruangan (jika dipilih)
                ->when($this->filterRoom, function ($query) {
                    $query->where('room_id', $this->filterRoom);
                })
                // Filter berdasarkan Kondisi (jika dipilih)
                ->when($this->filterCondition, function ($query) {
                    $query->where('condition', $this->filterCondition);
                })
                ->latest()->paginate(10),

            'rooms' => Room::all(),
            'assetItems' => AssetItem::all(),

            // Hasil pencarian katalog
            'filteredCatalogs' => strlen($this->search_catalog) > 1
                ? AssetItem::where('name', 'like', '%' . $this->search_catalog . '%')
                    ->orWhere('brand', 'like', '%' . $this->search_catalog . '%')->get()
                : [],

            // Hasil pencarian ruangan
            'filteredRooms' => strlen($this->search_room) > 1
                ? Room::where('name', 'like', '%' . $this->search_room . '%')->get()
                : [],

            // Hasil pencarian PIC
            'filteredPics' => strlen($this->search_pic) > 1
                ? User::whereIn('role', ['guru', 'staff', 'admin'])
                    ->where('name', 'like', '%' . $this->search_pic . '%')->get()
                : [],

            'filteredFilterRooms' => strlen($this->search_filter_room) > 1
                ? Room::where('name', 'like', '%' . $this->search_filter_room . '%')->get()
                : [],
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['assetId', 'asset_item_id', 'room_id', 'pic_id', 'serial_number', 'source_fund', 'acquisition_year', 'price', 'qty', 'selectedCatalogData', 'selectedRoomData', 'selectedPicData', 'bast_date']);
        $this->status = 'tersedia';
        $this->qty = 1;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'asset_item_id' => 'required',
            'room_id' => 'required',
            'pic_id' => 'required',
            'source_fund' => 'required',
            'bast_date' => 'required|date',
            'acquisition_year' => 'required|numeric',
            'qty' => 'required|numeric|min:1',
            // SN hanya unique jika diisi dan bukan sedang edit
            'serial_number' => $this->assetId ? 'nullable' : 'nullable|unique:assets,serial_number',
        ]);

        try {

            DB::transaction(function () {
                if ($this->assetId) {
                    // MODE EDIT
                    $asset = Asset::findOrFail($this->assetId);
                    $asset->update($this->getAssetData($this->serial_number ?: $asset->serial_number));
                } else {
                    // MODE TAMBAH (Mendukung Qty Banyak)
                    for ($i = 0; $i < $this->qty; $i++) {
                        $sn = $this->serial_number;

                        // Logika SN Otomatis: Jika SN kosong ATAU qty lebih dari 1
                        if (empty($sn) || $this->qty > 1) {
                            $item = AssetItem::find($this->asset_item_id);
                            $prefix = strtoupper(substr(str_replace(' ', '', $item->name), 0, 3));
                            $rand = strtoupper(bin2hex(random_bytes(2)));
                            // Format: KURS-20260531-RAND-1
                            $rand = strtoupper(substr(md5(uniqid()), 0, 4));
                            $sn = $prefix . '-' . now()->format('Ymd') . '-' . $rand . '-' . ($i + 1);
                        }

                        Asset::create($this->getAssetData($sn));
                    }
                }
            });
            $this->isModalOpen = false;
            session()->flash('message', 'Unit Aset berhasil diregistrasi.');
            $this->reset(['assetId', 'serial_number', 'qty']);
        } catch (\Exception $e) {
            // Log error ke storage/logs/laravel.log untuk tahu penyebab pastinya
            \Log::error("Error saat simpan aset: " . $e->getMessage());
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    private function getAssetData($sn)
    {
        return [
            'asset_item_id' => $this->asset_item_id,
            'room_id' => $this->room_id,
            'pic_id' => $this->pic_id,
            'serial_number' => $sn,
            'source_fund' => $this->source_fund,
            'acquisition_year' => $this->acquisition_year,
            'price' => $this->price ?? 0,
            'condition' => $this->condition,
            'status' => $this->status,
            'bast_date' => $this->bast_date,
            'barcode_token' => $this->assetId ? Asset::find($this->assetId)->barcode_token : 'AST-' . strtoupper(bin2hex(random_bytes(4))),
        ];
    }
    public function edit($id)
    {
        $asset = Asset::with(['itemInfo', 'room', 'pic'])->findOrFail($id);
        $this->assetId = $id;
        $this->asset_item_id = $asset->asset_item_id;
        $this->room_id = $asset->room_id;
        $this->pic_id = $asset->pic_id;
        $this->serial_number = $asset->serial_number;
        $this->source_fund = $asset->source_fund;
        $this->acquisition_year = $asset->acquisition_year;
        $this->price = $asset->price;
        $this->condition = $asset->condition;
        $this->selectedCatalogData = $asset->itemInfo->name;
        $this->selectedRoomData = $asset->room->name;
        $this->selectedPicData = $asset->pic->name;
        $this->qty = 1;
        $this->status = $asset->status;
        $this->isModalOpen = true;
        $this->bast_date = $asset->bast_date;
    }

    public function showDetail($id)
    {
        // Cari data aset berdasarkan ID
        $this->selectedAsset = Asset::with(['itemInfo', 'room', 'pic'])->find($id);

        // Buka modal secara programmatically
        $this->js("\$flux.modal('detail-asset').show()");
    }

    public function selectCatalog($id, $name)
    {
        $this->asset_item_id = $id;
        $this->selectedCatalogData = $name;
        $this->search_catalog = '';
    }

    public function selectRoom($id, $name)
    {
        $this->room_id = $id;
        $this->selectedRoomData = $name;
        $this->search_room = '';
    }

    public function selectPic($id, $name)
    {
        $this->pic_id = $id;
        $this->selectedPicData = $name;
        $this->search_pic = '';
    }

    public function selectFilterRoom($id, $name)
    {
        $this->filterRoom = $id;
        $this->filterRoomName = $name;
        $this->search_filter_room = '';
        $this->resetPage();
    }

    public function exportExcel()
    {
        // Ambil data dengan query yang sama seperti render() tapi tanpa pagination
        $assets = Asset::with(['itemInfo', 'room', 'pic'])
            ->where(function ($query) {
                $query->whereHas('itemInfo', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('serial_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterRoom, function ($query) {
                $query->where('room_id', $this->filterRoom);
            })
            ->when($this->filterCondition, function ($query) {
                $query->where('condition', $this->filterCondition);
            })
            ->latest()
            ->get();

        return Excel::download(new AssetExport($assets), 'laporan-aset-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf()
    {
        // 1. Ambil data dengan query yang sama (mengikuti filter)
        $assets = Asset::with(['itemInfo', 'room', 'pic'])
            ->where(function ($query) {
                $query->whereHas('itemInfo', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('serial_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterRoom, function ($query) {
                $query->where('room_id', $this->filterRoom);
            })
            ->when($this->filterCondition, function ($query) {
                $query->where('condition', $this->filterCondition);
            })
            ->latest()
            ->get();

        // 2. Siapkan data untuk view
        $data = [
            'assets' => $assets,
            'roomName' => $this->filterRoomName,
            'condition' => $this->filterCondition
        ];

        // 3. Load view dan download
        $pdf = Pdf::loadView('pdf.assets-pdf', $data)->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Laporan-Aset-' . now()->format('Ymd') . '.pdf');
    }

    public function downloadLabel($id)
    {
        $asset = Asset::with(['itemInfo', 'room', 'pic'])->findOrFail($id);

        // 1. Ambil URL QR Code
        $qrData = $asset->serial_number ?? $asset->barcode_token;
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($qrData);

        $qrImageContent = @file_get_contents($qrApiUrl);
        $qrImage = $qrImageContent ? imagecreatefromstring($qrImageContent) : null;

        // 2. Buat Kanvas Gambar PNG (Resolusi Tajam 800x420)
        $width = 800;
        $height = 420;
        $image = imagecreatetruecolor($width, $height);

        // Definisi Warna
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $gray = imagecolorallocate($image, 240, 240, 240);
        $borderGray = imagecolorallocate($image, 200, 200, 200);
        $darkGray = imagecolorallocate($image, 100, 100, 100);

        // Background Putih & Border Luar
        imagefilledrectangle($image, 0, 0, $width, $height, $white);
        imagerectangle($image, 12, 12, $width - 12, $height - 12, $black);
        imagerectangle($image, 13, 13, $width - 13, $height - 13, $black);

        // Garis Pemisah Vertikal Putus-putus (Antara QR & Teks Detail)
        for ($y = 35; $y < $height - 35; $y += 8) {
            imageline($image, 280, $y, 280, $y + 4, $borderGray);
        }

        // 3. Tempel QR Code di Sisi Kiri
        if ($qrImage) {
            $qrResized = imagescale($qrImage, 210, 210);
            imagecopy($image, $qrResized, 35, 60, 0, 0, 210, 210);
            imagedestroy($qrResized);
            imagedestroy($qrImage);
        }

        // Path Font TrueType Universal (Bisa gunakan Arial/Helvetica TTF jika ada, atau fallback bawaan GD)
        // Jalur font umum bawaan OS/PHP
        $fontPath = public_path('fonts/arial.ttf');
        $useTtf = file_exists($fontPath);

        // 4. Tulis Teks Serial Number di Bawah QR
        $snText = $asset->serial_number ?? '-';
        if ($useTtf) {
            imagettftext($image, 11, 0, 45, 300, $black, $fontPath, $snText);
        } else {
            imagestring($image, 4, 45, 290, $snText, $black);
        }

        // 5. Informasi Utama di Sisi Kanan
        $startX = 305;

        // A. Nama Instansi / Sekolah
        if ($useTtf) {
            imagettftext($image, 16, 0, $startX, 55, $black, $fontPath, "SMKN 7 BALEENDAH");
        } else {
            imagestring($image, 5, $startX, 40, "SMKN 7 BALEENDAH", $black);
        }
        imageline($image, $startX, 68, $width - 35, 68, $black); // Garis bawah judul

        // B. Nama Barang
        $itemName = strtoupper($asset->itemInfo->name ?? '-');
        if ($useTtf) {
            imagettftext($image, 13, 0, $startX, 100, $black, $fontPath, substr($itemName, 0, 30));
        } else {
            imagestring($image, 4, $startX, 85, substr($itemName, 0, 32), $black);
        }

        // C. Merk / Spesifikasi
        $brand = "Merk  : " . ($asset->itemInfo->brand ?? '-');
        if ($useTtf) {
            imagettftext($image, 11, 0, $startX, 130, $darkGray, $fontPath, substr($brand, 0, 38));
        } else {
            imagestring($image, 3, $startX, 118, substr($brand, 0, 40), $black);
        }

        // D. Serial Number Detail
        $snDetail = "S/N   : " . ($asset->serial_number ?? '-');
        if ($useTtf) {
            imagettftext($image, 11, 0, $startX, 155, $darkGray, $fontPath, substr($snDetail, 0, 38));
        } else {
            imagestring($image, 3, $startX, 142, substr($snDetail, 0, 40), $black);
        }

        // E. Badge Lokasi / Ruangan (Kotak Abu-abu)
        $roomName = "RUANG : " . strtoupper($asset->room->name ?? '-');
        imagefilledrectangle($image, $startX, 175, $startX + 450, 215, $gray);
        imagerectangle($image, $startX, 175, $startX + 450, 215, $borderGray);

        if ($useTtf) {
            imagettftext($image, 11, 0, $startX + 15, 202, $black, $fontPath, substr($roomName, 0, 35));
        } else {
            imagestring($image, 4, $startX + 15, 188, substr($roomName, 0, 35), $black);
        }

        // 6. TEMPILKAN LOGO SEKOLAH di Space Kosong Bawah (Kanan Bawah)
        $logoPath = public_path('images/LogoSMKN7BE.png');
        if (file_exists($logoPath)) {
            $logoImage = @imagecreatefrompng($logoPath);
            if ($logoImage) {
                // Aktifkan alpha blending untuk gambar PNG transparan
                imagealphablending($image, true);
                imagesavealpha($image, true);

                // Ukuran target logo yang diinginkan (misal: tinggi 120px)
                $origW = imagesx($logoImage);
                $origH = imagesy($logoImage);
                $targetH = 120;
                $targetW = (int) (($origW / $origH) * $targetH);

                // Posisi di space kosong kanan bawah
                $logoX = $width - $targetW - 45;
                $logoY = 245;

                // Tempel logo berskala presisi ke kanvas
                imagecopyresampled($image, $logoImage, $logoX, $logoY, 0, 0, $targetW, $targetH, $origW, $origH);
                imagedestroy($logoImage);
            }
        }

        // 7. Output Gambar PNG
        return response()->streamDownload(function () use ($image) {
            imagepng($image);
            imagedestroy($image);
        }, 'Label-Aset-' . ($asset->serial_number ?? $asset->id) . '.png');
    }

}