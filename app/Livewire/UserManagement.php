<?php

namespace App\Livewire;

use App\Models\User;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class UserManagement extends Component
{
    use WithPagination, WithFileUploads;
    

    public $search = '';
    public $userId, $name, $username, $email, $role, $password,$rfid_uid,$class_id;
    public $avatar, $new_avatar;
    public $selectedRole = '';
    public $isModalOpen = false;

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedRole, function ($query) {
                $query->where('role', $this->selectedRole);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.user-management', [
            'users' => $users
        ])->layout('layouts.app');
    }

    public function create()
    {
        // Reset termasuk variabel avatar
        $this->reset(['name', 'username', 'email', 'role', 'password', 'userId', 'rfid_uid', 'class_id', 'avatar', 'new_avatar']);
        $this->role = 'guru'; // Set default role
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->rfid_uid = $user->rfid_uid;
        $this->class_id = $user->class_id;
        $this->avatar = $user->avatar; // Simpan path avatar lama
        $this->new_avatar = null;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$this->userId,
            'email' => 'nullable|email|unique:users,email,'.$this->userId,
            'role' => 'required|in:admin,petugas,owner,guru,staff,siswa,kesiswaan,walikelas,piket',
            'password' => $this->userId ? 'nullable|min:6' : 'required|min:6',
            'class_id' => 'nullable|exists:class_models,id',
            'new_avatar' => 'nullable|image|max:1024', // Validasi file gambar maks 0,5MB
        ]);

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'rfid_uid' => $this->rfid_uid,
            'password' => $this->password ? Hash::make($this->password) : User::find($this->userId)->password,
            'class_id' => in_array($this->role, ['walikelas', 'siswa']) ? $this->class_id : null,
        ];

        // Handle upload avatar baru
        if ($this->new_avatar) {
            if ($this->avatar) {
                Storage::disk('public')->delete($this->avatar); // Hapus foto lama jika ada
            }
            $data['avatar'] = $this->new_avatar->store('avatars', 'public');
        }

        User::updateOrCreate(['id' => $this->userId], $data);

        $this->isModalOpen = false;
        session()->flash('message', 'Data Berhasil Disimpan.');
    }

    public function delete($id)
    {
        if($id !== auth()->id()){
            $user = User::find($id);
            if ($user && $user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            User::destroy($id);
        }
    }
}