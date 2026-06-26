<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use App\Models\ClassModel;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cari ID kelas berdasarkan nama kelas yang ditulis di Excel
        $class = ClassModel::where('name', $row['kelas'])->first();

        // Jika kelas tidak ditemukan, set null atau beri error (sesuai kebutuhan)
        $classId = $class ? $class->id : null;

        return User::updateOrCreate(
            ['nis' => $row['nis']], 
            [
                'name'      => $row['nama'],
                'class_id'  => $classId, 
                'username'  => $row['username'],
                'phone'     => $row['no_telepon'],
                'address'   => $row['address'],
                'rfid_uid'  => $row['rfid_uid'],
                'role'      => 'siswa',
                'password'  => Hash::make('12345678'),
                'is_active' => true,
            ]
        );
    }
}
