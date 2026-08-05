<?php

namespace App\Livewire\Sarpras;

use App\Models\User;
use App\Models\Item;
use App\Models\Room;
use App\Models\RequestModel;
use App\Models\RequestDetail;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class RfidRequest extends Component
{
    public $step = 1;
    public $rfid_uid = '';
    public $userId; 
    public $type = '';
    public $class_id, $room_id;
    public $search_item = '';
    public $notes = '';
    public $selectedItems = [];

    // Menggunakan Attribute Computed untuk efisiensi di Livewire 3
    #[Computed]
    public function userData()
    {
        if (!$this->userId) return null;
        return User::with('class')->find($this->userId);
    }

    public function updatedRfidUid()
    {
        $uid = trim($this->rfid_uid);
        if (empty($uid)) return;

        $user = User::where('rfid_uid', $uid)->where('is_active', true)->first();

        if ($user) {
            $this->userId = $user->id;
            // Reset rfid_uid agar input box kosong dan siap untuk scan berikutnya
            $this->rfid_uid = ''; 

            if ($user->role === 'siswa') {
                $this->type = 'class';
                $this->class_id = $user->class_id;
            } else {
                $this->type = 'room'; // atau sesuaikan jika tipe untuk guru berbeda di database Anda
                $this->class_id = null;
            }
            
            // Guru maupun Siswa langsung ke step 3 (Pilih Barang) karena tidak pakai pilih ruangan
            $this->step = 3;

        } else {
            session()->flash('error', 'Kartu RFID tidak terdaftar!');
            $this->rfid_uid = '';
        }
    }

    public function setLocation()
    {
        if ($this->type === 'room' && !$this->room_id) {
            $this->addError('room_id', 'Silakan pilih ruangan.');
            return;
        }
        $this->step = 3;
    }

    public function addItem($itemId)
    {
        $item = Item::find($itemId);
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

        // VALIDASI STOK
        foreach ($this->selectedItems as $itemData) {
            $dbItem = Item::find($itemData['id']);
            if (!$dbItem || $itemData['qty'] > $dbItem->stock) {
                session()->flash('error', "Stok '{$itemData['name']}' tidak mencukupi.");
                return;
            }
        }

        try {
            DB::transaction(function () {
                $user = $this->userData; // Mengambil dari computed property
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

                    // Jika guru yang meminta, stok langsung dikurangi (approved), jika siswa pending
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

            // RESET TOTAL UNTUK KIOS
            $this->reset(['rfid_uid', 'userId', 'type', 'class_id', 'room_id', 'selectedItems', 'notes', 'search_item']);
            $this->step = 1;

        } catch (\Exception $e) {
            // Tangkap error jika diperlukan untuk debugging (bisa dihapus/diganti log jika sudah normal)
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
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

        return view('livewire.rfid-request', [
            'availableItems' => $availableItems,
            'rooms' => Room::orderBy('name')->get(),
        ])->layout('layouts.guest');
    }

    public function mount()
    {
        $this->step = 1; 
    }
}