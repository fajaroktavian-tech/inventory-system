<?php

namespace App\Livewire;

use App\Models\ClassModel;
use Livewire\Component;
use App\Models\Prodi;
use App\Models\User;
use Livewire\WithPagination;

class ClassManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $classId, $name, $prodi_id;
    public $isModalOpen = false;
    public $targetClassId;
    public $isPromotionModalOpen = false; // Pastikan property ini ada
    public $sourceClassId; // Menyimpan ID kelas yang akan dinaikkan
    public $studentsInClass = [];

    public function render()
    {
        return view('livewire.class-management', [
            'classes' => ClassModel::with('prodi', 'walikelas') // Eager load prodi untuk performa
                ->withCount([
                    'users' => function ($query) {
                        $query->where('role', 'siswa')->where('is_active', true);
                    }
                ])
                ->where('name', 'like', '%' . $this->search . '%')
                ->latest()->paginate(10),
            'prodis' => Prodi::all() // Ambil semua data prodi untuk dropdown
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['name', 'classId']);
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $class = ClassModel::findOrFail($id);
        $this->classId = $id;
        $this->name = $class->name;
        $this->prodi_id = $class->prodi_id;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|unique:class_models,name,' . $this->classId,
            'prodi_id' => 'required|exists:prodis,id'
        ]);

        ClassModel::updateOrCreate(['id' => $this->classId], ['name' => $this->name, 'prodi_id' => $this->prodi_id]);

        $this->isModalOpen = false;
        session()->flash('message', 'Data Kelas berhasil disimpan.');
    }

    public function delete($id)
    {
        ClassModel::destroy($id);
        session()->flash('message', 'Data Kelas berhasil dihapus.');
    }

    public function promoteClass()
    {
        $this->validate([
            'targetClassId' => 'required|exists:class_models,id'
        ]);

        // AMBIL HANYA ID DARI ARRAY $studentsInClass
        // Ini memastikan hanya siswa yang ada di modal (yang belum dihapus tombol X) yang diproses
        $studentIds = collect($this->studentsInClass)->pluck('id');

        // LAKUKAN UPDATE HANYA UNTUK ID TERSEBUT
        User::whereIn('id', $studentIds)
            ->update(['class_id' => $this->targetClassId]);

        // RESET STATE
        $this->isPromotionModalOpen = false;
        $this->reset(['targetClassId', 'sourceClassId', 'studentsInClass']);

        session()->flash('message', 'Siswa berhasil dinaikkan ke kelas tujuan.');
    }

    public function openPromotionModal($id)
    {
        $this->sourceClassId = $id; // Simpan ID kelas asal
        $this->studentsInClass = User::where('class_id', $id)
            ->where('role', 'siswa')
            ->get()
            ->toArray();
        $this->isPromotionModalOpen = true; // Buka modal
    }

    public function graduateClass($classId)
    {
        User::where('class_id', $classId)
            ->where('role', 'siswa')
            ->update(['is_active' => false]); // Set non-aktif

        session()->flash('message', 'Siswa kelas ini telah diluluskan/dinonaktifkan.');
    }

    public function promoteClassWithFilter()
    {
        // $this->studentsInClass berisi daftar siswa yang tampil di modal.
        // Admin bisa hapus siswa dari array ini sebelum klik "Proses"

        $studentIds = collect($this->studentsInClass)->pluck('id');

        User::whereIn('id', $studentIds)
            ->update(['class_id' => $this->targetClassId]);

        $this->isPromotionModalOpen = false;
        session()->flash('message', 'Proses kenaikan kelas berhasil.');
    }

    public function removeStudentFromList($index)
    {
        // Pastikan index ada sebelum dihapus
        if (isset($this->studentsInClass[$index])) {
            unset($this->studentsInClass[$index]);
            // Sekarang ini akan bekerja karena datanya berupa array
            $this->studentsInClass = array_values($this->studentsInClass);
        }
    }
}