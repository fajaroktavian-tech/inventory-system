<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StaffManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $staffId, $name, $username, $nip, $position, $role, $rfid_uid, $phone, $address, $avatar, $new_avatar;
    public $isModalOpen = false;
    public $isDetailModalOpen = false;
    public $selectedStaff;

    public function render()
    {
        $totalGuru = User::where('role', 'guru')->count();
    $totalStaff = User::where('role', 'staff')->count();

        return view('livewire.staff-management', [
            'totalGuru' => $totalGuru,
        'totalStaff' => $totalStaff,
            'staffs' => User::whereIn('role', ['guru', 'staff'])
                ->where(
                    fn($q) =>
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('rfid_uid', 'like', '%' . $this->search . '%')
                )
                ->latest()->paginate(10)
        ])->layout('layouts.app');
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        // Reset spesifik hanya untuk field form
        $this->reset([
            'staffId',
            'name',
            'username',
            'nip',
            'position',
            'role',
            'rfid_uid',
            'phone',
            'address',
            'avatar',
            'new_avatar'
        ]);

        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $this->staffId,
            'nip' => 'nullable|unique:users,nip,' . $this->staffId,
            'role' => 'required',
            'rfid_uid' => 'nullable|unique:users,rfid_uid,' . $this->staffId,
            'new_avatar' => 'nullable|image|max:1024',
        ]);

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'nip' => $this->nip,
            'role' => $this->role,
            'position' => $this->position,
            'phone' => $this->phone,
            'address' => $this->address,
            'rfid_uid' => $this->rfid_uid,
        ];

        if (!$this->staffId) {
            $data['password'] = Hash::make('password123');
        }

        if ($this->new_avatar) {
            if ($this->avatar)
                Storage::disk('public')->delete($this->avatar);
            $data['avatar'] = $this->new_avatar->store('avatars', 'public');
        }

        User::updateOrCreate(['id' => $this->staffId], $data);

        $this->isModalOpen = false;
        session()->flash('message', 'Data Guru/Staff berhasil disimpan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->staffId = $id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->nip = $user->nip;
        $this->role = $user->role;
        $this->position = $user->position;
        $this->phone = $user->phone;
        $this->address = $user->address;
        $this->rfid_uid = $user->rfid_uid;
        $this->avatar = $user->avatar;
        $this->isModalOpen = true;
    }

    public function showDetail($id)
    {
        $this->selectedStaff = User::findOrFail($id);
        $this->isDetailModalOpen = true;
    }

    public function delete($id)
    {
        User::destroy($id);
        session()->flash('message', 'Data berhasil dihapus.');
    }
}