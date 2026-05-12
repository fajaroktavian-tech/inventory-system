<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Item;
use App\Models\Room;
use App\Models\RequestModel;
use App\Models\RequestDetail;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class UserRequest extends Component
{
    public $step;
    public $userId;
    public $type = '';
    public $class_id, $room_id;
    public $search_item = '';
    public $notes = '';
    public $selectedItems = [];

    /**
     * Mengambil data user yang sedang login
     */
    #[Computed]
    public function userData()
    {
        return auth()->user();
    }

    /**
     * Inisialisasi data saat halaman dimuat
     */
    public function mount()
    {
        $user = $this->userData;
        $this->userId = $user->id;

        if ($user->role === 'siswa') {
            $this->type = 'class';
            $this->class_id = $user->class_id;
            $this->step = 3; // Siswa langsung ke pilih barang
        } else {
            $this->type = 'room';
            $this->step = 2; // Guru/Staff wajib pilih ruangan dulu
        }
    }

    public function setLocation()
    {
        if ($this->type === 'room' && !$this->room_id) {
            $this->addError('room_id', 'Silakan pilih ruangan tujuan.');
            return;
        }
        $this->step = 3;
    }

    public function addItem($itemId)
    {
        $item = Item::find($itemId);
        
        // Cek jika barang sudah ada di list
        foreach ($this->selectedItems as $selected) {
            if ($selected['id'] == $itemId) return;
        }

        if ($item && $item->stock > 0) {
            $this->selectedItems[] = [
                'id' => $item->id,
                'name' => $item->name,
                'unit' => $item->unit,
                'stock' => $item->stock,
                'qty' => 1
            ];
        }
        $this->search_item = '';
    }

    public function removeItem($index)
    {
        unset($this->selectedItems[$index]);
        $this->selectedItems = array_values($this->selectedItems);
    }

    public function submitRequest()
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', 'Pilih minimal satu barang!');
            return;
        }

        // Validasi stok akhir
        foreach ($this->selectedItems as $itemData) {
            $dbItem = Item::find($itemData['id']);
            if (!$dbItem || $itemData['qty'] > $dbItem->stock) {
                session()->flash('error', "Stok '{$itemData['name']}' tidak mencukupi.");
                return;
            }
        }

        try {
            DB::transaction(function () {
                $user = $this->userData;
                $isSiswa = $user->role === 'siswa';

                $request = RequestModel::create([
                    'user_id' => $user->id,
                    'type' => $this->type,
                    'class_id' => $this->class_id ?: null,
                    'room_id' => $this->room_id ?: null,
                    'status' => $isSiswa ? 'pending' : 'approved',
                    'request_date' => now()->format('Y-m-d'),
                    'notes' => $this->notes,
                    'approved_by' => $isSiswa ? null : $user->id,
                    'approved_at' => $isSiswa ? null : now(),
                ]);

                foreach ($this->selectedItems as $itemData) {
                    $item = Item::lockForUpdate()->find($itemData['id']);

                    // Jika Guru/Staff (bukan siswa), stok langsung berkurang
                    if (!$isSiswa) {
                        $item->decrement('stock', $itemData['qty']);
                    }

                    RequestDetail::create([
                        'request_id' => $request->id,
                        'item_id' => $item->id,
                        'quantity_requested' => $itemData['qty'],
                        'quantity_approved' => $isSiswa ? null : $itemData['qty'],
                    ]);
                }
            });

            session()->flash('success', ($this->userData->role === 'siswa') 
            ? 'Permintaan terkirim!' 
            : 'Pengambilan berhasil dicatat!');

    // Reset form
    $this->reset(['selectedItems', 'notes', 'search_item', 'room_id']);
    $this->step = ($this->userData->role === 'siswa') ? 3 : 2;

        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'danger', heading: 'Gagal', text: 'Terjadi kesalahan sistem.');
        }
    }

    public function render()
    {
        $availableItems = [];
        if (strlen($this->search_item) > 1) {
            $availableItems = Item::where('stock', '>', 0)
                ->where('name', 'like', '%' . $this->search_item . '%')
                ->limit(5)->get();
        }

        return view('livewire.user-request', [
            'availableItems' => $availableItems,
            'rooms' => Room::orderBy('name')->get(),
        ])->layout('layouts.app'); // Menggunakan layout internal (dengan sidebar/navbar admin)
    }

    // Tambahkan fungsi ini di dalam class UserRequest

public function updatedSelectedItems($value, $key)
{
    // Kita mencari index dan field yang diupdate (contoh key: "0.qty")
    if (str_contains($key, '.qty')) {
        $parts = explode('.', $key);
        $index = $parts[0];
        $newQty = (int)$value;

        $item = $this->selectedItems[$index];
        $maxStock = (int)$item['stock'];

        // 1. Jika angka negatif atau kosong, kembalikan ke 1
        if ($newQty < 1) {
            $this->selectedItems[$index]['qty'] = 1;
        }

        // 2. Jika melebihi stok, paksa ke angka stok maksimal
        if ($newQty > $maxStock) {
            $this->selectedItems[$index]['qty'] = $maxStock;
            
            // Opsional: Beri peringatan toast/flash
            session()->flash('error', "Jumlah {$item['name']} tidak boleh melebihi stok ({$maxStock}).");
        }
    }
}
}