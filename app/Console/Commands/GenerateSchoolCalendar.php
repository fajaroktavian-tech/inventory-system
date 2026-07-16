<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:generate-school-calendar')]
#[Description('Command description')]
class GenerateSchoolCalendar extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDate = \Carbon\Carbon::create(2026, 7, 17); // Awal tahun ajaran
        $endDate = \Carbon\Carbon::create(2027, 6, 30);

        while ($startDate <= $endDate) {
            \App\Models\SchoolCalendar::updateOrCreate(
                ['date' => $startDate->format('Y-m-d')],
                [
                    'is_holiday' => $startDate->isWeekend(), // Sabtu-Minggu otomatis libur
                    'description' => $startDate->isWeekend() ? 'Akhir Pekan' : 'Hari Sekolah'
                ]
            );
            $startDate->addDay();
        }
        $this->info('Kalender sekolah berhasil di-generate!');
    }
}
