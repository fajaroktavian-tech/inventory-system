<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\ClassModel;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class StudentManagement extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $studentId, $name, $username, $rfid_uid, $class_id; // class_id ditambahkan
    public $nis, $phone, $address, $avatar, $new_avatar;
    public $selectedStudent;
    public $isDetailModalOpen = false;
    public $isModalOpen = false;

    public function showDetail($id)
    {
        $this->selectedStudent = User::with(['class.prodi'])->findOrFail($id);
        $this->isDetailModalOpen = true;
    }
    public function render()
    {
        return view('livewire.student-management', [
            'students' => User::with(['class.prodi']) // Eager load sampai ke prodi
                ->where('role', 'siswa')
                ->where(
                    fn($q) =>
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('rfid_uid', 'like', '%' . $this->search . '%')
                        ->orWhere('nis', 'like', '%' . $this->search . '%')
                )
                ->latest()->paginate(10),
            'classes' => ClassModel::with('prodi')
            ->orderBy('name', 'asc') 
            ->get()
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['studentId', 'name', 'username', 'rfid_uid', 'class_id', 'nis', 'phone', 'address', 'avatar', 'new_avatar']);
        $this->isModalOpen = true;
    }

    public function store()
    {
        $rules = [
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $this->studentId,
            'nis' => 'nullable|unique:users,nis,' . $this->studentId,
            'rfid_uid' => 'required|unique:users,rfid_uid,' . $this->studentId,
            'class_id' => 'required|exists:class_models,id',
            'new_avatar' => 'nullable|image|max:500', // Max 1MB
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'nis' => $this->nis,
            'phone' => $this->phone,
            'address' => $this->address,
            'rfid_uid' => $this->rfid_uid,
            'class_id' => $this->class_id,
            'role' => 'siswa',
        ];

        // Set password default jika user baru
        if (!$this->studentId) {
            $data['password'] = Hash::make('12345678');
        }

        // Handle Upload Foto
        if ($this->new_avatar) {
            // Hapus foto lama jika ada
            if ($this->avatar) {
                Storage::disk('public')->delete($this->avatar);
            }
            $data['avatar'] = $this->new_avatar->store('avatars', 'public');
        }

        User::updateOrCreate(['id' => $this->studentId], $data);

        $this->isModalOpen = false;
        session()->flash('message', 'Data Siswa berhasil diperbarui.');
    }

    public function edit($id)
    {
        $student = User::findOrFail($id);
        $this->studentId = $id;
        $this->name = $student->name;
        $this->username = $student->username;
        $this->nis = $student->nis;
        $this->phone = $student->phone;
        $this->address = $student->address;
        $this->rfid_uid = $student->rfid_uid;
        $this->class_id = $student->class_id;
        $this->avatar = $student->avatar;
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        $student = User::find($id);
        if ($student->avatar) {
            Storage::disk('public')->delete($student->avatar);
        }
        $student->delete();
        session()->flash('message', 'Siswa berhasil dihapus.');
    }
    public function toggleStatus($id)
    {
        $student = User::findOrFail($id);
        // Jika true jadi false, jika false jadi true
        $student->is_active = !$student->is_active;
        $student->save();

        session()->flash('message', 'Status siswa berhasil diperbarui.');
    }
}