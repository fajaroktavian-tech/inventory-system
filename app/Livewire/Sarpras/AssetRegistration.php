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

        DB::transaction(function () {
            if ($this->assetId) {
                // MODE EDIT
                $asset = Asset::findOrFail($this->assetId);
                $asset->update($this->getAssetData($this->serial_number));
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
                        $sn = $prefix . '-' . now()->format('Ymd') . '-' . $rand . '-' . ($i + 1);
                    }

                    Asset::create($this->getAssetData($sn));
                }
            }
        });
        $this->isModalOpen = false;
        session()->flash('message', 'Unit Aset berhasil diregistrasi.');
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
            'barcode_token' => 'AST-' . strtoupper(bin2hex(random_bytes(4))),
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

}