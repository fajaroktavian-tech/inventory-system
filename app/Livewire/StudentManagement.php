<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\ClassModel;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class StudentManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $studentId, $name, $username, $rfid_uid, $class_id; // class_id ditambahkan
    public $isModalOpen = false;

    public function render()
    {
        return view('livewire.student-management', [
            // Eager load class agar tidak berat saat load data
            'students' => User::with('class')
                ->where('role', 'siswa')
                ->where(fn($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('rfid_uid', 'like', '%'.$this->search.'%'))
                ->latest()->paginate(10),
            'classes' => ClassModel::orderBy('name')->get()
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['studentId', 'name', 'username', 'rfid_uid', 'class_id']);
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,'.$this->studentId,
            'rfid_uid' => 'required|unique:users,rfid_uid,'.$this->studentId,
            'class_id' => 'required|exists:class_models,id', // Validasi kelas wajib diisi
        ]);

        User::updateOrCreate(['id' => $this->studentId], [
            'name' => $this->name,
            'username' => $this->username,
            'rfid_uid' => $this->rfid_uid,
            'class_id' => $this->class_id, // Simpan ID Kelas
            'password' => Hash::make('12345678'),
            'role' => 'siswa',
        ]);

        $this->isModalOpen = false;
        session()->flash('message', 'Data Siswa berhasil disimpan.');
    }

    public function edit($id)
    {
        $student = User::findOrFail($id);
        $this->studentId = $id;
        $this->name = $student->name;
        $this->username = $student->username;
        $this->rfid_uid = $student->rfid_uid;
        $this->class_id = $student->class_id; // Load ID kelas saat edit
        $this->isModalOpen = true;
    }
}