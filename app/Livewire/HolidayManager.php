<?php

namespace App\Livewire;

use App\Models\SchoolCalendar;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;
use Flux\Flux;
use Livewire\WithPagination;

class HolidayManager extends Component
{
    public $startDate, $endDate, $description;
    use WithPagination;

    public function saveHoliday()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'description' => 'required|string',
        ]);

        // Menggunakan CarbonPeriod untuk loop tanggal
        $period = CarbonPeriod::create($this->startDate, $this->endDate);

        foreach ($period as $date) {
            SchoolCalendar::updateOrCreate(
                ['date' => $date->format('Y-m-d')],
                ['is_holiday' => true, 'description' => $this->description]
            );
        }

        Flux::toast('Libur berhasil ditambahkan ke kalender!');
        $this->reset(['startDate', 'endDate', 'description']);

        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.holiday-manager', [
            'holidays' => SchoolCalendar::where('is_holiday', true)
                ->orderBy('date', 'desc')
                ->paginate(10)
        ]);
    }

    // Di dalam HolidayManager.php
    public function deleteHoliday($id)
    {
        $holiday = SchoolCalendar::find($id);
        if ($holiday) {
            // Mengembalikan status ke "Bukan Libur" atau menghapus record
            $holiday->update(['is_holiday' => false, 'description' => 'Hari Sekolah']);
            Flux::toast('Status libur berhasil dibatalkan.');
        }
    }
}