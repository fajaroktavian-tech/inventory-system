<?php

namespace App\Livewire;

use App\Models\User;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $userId, $name, $username, $email, $role, $password,$rfid_uid;
    public $isModalOpen = false;

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => User::where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('username', 'like', '%'.$this->search.'%')
                        ->latest()
                        ->paginate(10)
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['name', 'username', 'email', 'role', 'password', 'userId', 'rfid_uid']);
        $this->role = 'petugas';
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
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$this->userId,
            'email' => 'nullable|email|unique:users,email,'.$this->userId,
            'role' => 'required|in:admin,petugas,owner,guru,staff,siswa',
            'password' => $this->userId ? 'nullable|min:6' : 'required|min:6',
        ]);

        User::updateOrCreate(['id' => $this->userId], [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'rfid_uid'=>$this->rfid_uid,
            'password' => $this->password ? Hash::make($this->password) : User::find($this->userId)->password,
        ]);

        $this->isModalOpen = false;
        session()->flash('message', 'Data Berhasil Disimpan.');
    }

    public function delete($id)
    {
        if($id !== auth()->id()){
            User::destroy($id);
        }
    }
}