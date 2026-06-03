<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/MasterDataSeeder.php
    public function run(): void
    {
        // Isi Data Jurusan
        $prodis = [
            ['name' => 'Pengembangan Perangkat Lunak dan Gim', 'alias' => 'PPLG'],
            ['name' => 'Teknik Komputer dan Jaringan', 'alias' => 'TKJ'],
            ['name' => 'Desain Komunikasi Visual', 'alias' => 'DKV'],
        ];

        foreach ($prodis as $prodi) {
            \App\Models\Prodi::updateOrCreate(['name' => $prodi['name']], $prodi);
        }

        // Contoh Isi Data Kelas (Hubungkan ke PPLG)
        $pplg = \App\Models\Prodi::where('alias', 'PPLG')->first();
        $classes = ['X PPLG 1', 'XI PPLG 2', 'XII PPLG 3'];

        foreach ($classes as $class) {
            \App\Models\ClassModel::updateOrCreate(
                ['name' => $class],
                ['prodi_id' => $pplg->id]
            );
        }
    }
}
