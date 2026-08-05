<?php

namespace App\Livewire\Sarpras;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Flux\Flux;

class AssetLoanManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $search_user = '';
    public $selectedUser = null;
    public $loanId, $asset_id, $user_id, $loan_date, $due_date, $notes;
    public $isModalOpen = false;
    public $search_asset = '';
    public $selectedAsset = null;

    public function selectUser($id)
{
    $user = User::find($id);
    $this->user_id = $user->id;
    $this->selectedUser = $user->name . ' (' . strtoupper($user->role) . ')';
    $this->search_user = ''; // Kosongkan pencarian
}

public function removeSelectedUser()
{
    $this->user_id = null;
    $this->selectedUser = null;
}
    protected $rules = [
        'asset_id' => 'required',
        'user_id' => 'required',
        'loan_date' => 'required|date',
        'due_date' => 'nullable|date|after_or_equal:loan_date',
    ];

    public function render()
    {
        // Logika pencarian aset di dalam modal (tetap sama)
        $availableAssets = [];
        if (strlen($this->search_asset) > 1) {
            $availableAssets = Asset::with('itemInfo')
                ->where('status', 'tersedia')
                ->where(function ($q) {
                    $q->whereHas('itemInfo', function ($query) {
                        $query->where('name', 'like', '%' . $this->search_asset . '%');
                    })
                        ->orWhere('serial_number', 'like', '%' . $this->search_asset . '%');
                })
                ->limit(5)->get();
        }

        $availableUsers = [];
    if (strlen($this->search_user) > 1) {
        $availableUsers = User::where('name', 'like', '%' . $this->search_user . '%')
            ->limit(5)->get();
    }

        return view('livewire.asset-loan-management', [
            'loans' => AssetLoan::with(['asset.itemInfo', 'user'])
                ->where(function ($query) {
                    // Cari berdasarkan Nama Peminjam
                    $query->whereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                        // ATAU Cari berdasarkan Nama Barang di Katalog Aset
                        ->orWhereHas('asset.itemInfo', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                        // ATAU Cari berdasarkan Nomor Seri Aset
                        ->orWhereHas('asset', function ($q) {
                        $q->where('serial_number', 'like', '%' . $this->search . '%');
                    });
                })
                ->latest()
                ->paginate(10),
            'availableAssets' => $availableAssets,
            'users' => User::orderBy('name')->get(),
            'availableUsers' => $availableUsers,
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['loanId', 'asset_id', 'user_id', 'loan_date', 'due_date', 'notes']);
        $this->loan_date = date('Y-m-d');
        $this->isModalOpen = true;
    }

    public function selectAsset($id)
    {
        $asset = Asset::with('itemInfo')->find($id);
        $this->selectedAsset = [
            'id' => $asset->id,
            'name' => $asset->itemInfo->name,
            'sn' => $asset->serial_number
        ];
        $this->search_asset = ''; // Kosongkan pencarian setelah pilih
    }

    public function removeSelectedAsset()
    {
        $this->selectedAsset = null;
    }

    public function store()
    {
        $this->validate([
            'selectedAsset' => 'required',
            'user_id' => 'required',
            'loan_date' => 'required|date',
        ]);

        DB::transaction(function () {
            AssetLoan::create([
                'asset_id' => $this->selectedAsset['id'],
                'user_id' => $this->user_id,
                'loan_date' => $this->loan_date,
                'due_date' => $this->due_date,
                'notes' => $this->notes,
                'status' => 'active',
            ]);

            Asset::find($this->selectedAsset['id'])->update(['status' => 'dipinjam']);
        });

        $this->isModalOpen = false;
        $this->reset(['selectedAsset', 'user_id', 'notes']);
        session()->flash('message', 'Peminjaman berhasil dicatat.');
    }
    public function returnAsset($id)
    {
        $loan = AssetLoan::findOrFail($id);

        // Update data peminjaman
        $loan->update([
            'return_date' => date('Y-m-d'),
            'status' => 'returned'
        ]);

        // Kembalikan status aset menjadi 'tersedia'
        Asset::find($loan->asset_id)->update(['status' => 'tersedia']);

        session()->flash('message', 'Aset telah dikembalikan.');
    }
    public function updatedAssetSearch()
    {
        // Reset asset_id setiap kali melakukan pencarian baru agar tidak terjadi bentrok data
        $this->reset('asset_id');
    }

    public function exportPdf()
    {
        return redirect()->route('asset-loans.export.pdf');
    }

    // Fungsi Export Excel
    public function exportExcel()
    {
        $fileName = 'rekap-peminjaman-aset-' . date('Y-m-d') . '.csv';
        
        $loans = AssetLoan::with(['asset.itemInfo', 'user'])->latest()->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($loans) {
            $file = fopen('php://output', 'w');
            // Header CSV
            fputcsv($file, ['No', 'Nama Peminjam', 'Role', 'Nama Aset', 'Serial Number', 'Tanggal Pinjam', 'Tenggat', 'Tanggal Kembali', 'Status', 'Catatan']);

            foreach ($loans as $index => $loan) {
                fputcsv($file, [
                    $index + 1,
                    $loan->user->name ?? '-',
                    strtoupper($loan->user->role ?? '-'),
                    $loan->asset->itemInfo->name ?? '-',
                    $loan->asset->serial_number ?? '-',
                    $loan->loan_date,
                    $loan->due_date ?? '-',
                    $loan->return_date ?? '-',
                    $loan->status,
                    $loan->notes ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}