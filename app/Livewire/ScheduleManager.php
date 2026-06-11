<?php
namespace App\Livewire;

use App\Models\Schedule;
use Livewire\Component;
use Flux\Flux;

class ScheduleManager extends Component
{
    public $name, $start_time, $end_time, $is_active;
    public $editingId = null;

    public function save()
    {
        $this->validate(['name' => 'required', 'start_time' => 'required', 'end_time' => 'required']);

        Schedule::updateOrCreate(
            ['id' => $this->editingId],
            ['name' => $this->name, 'start_time' => $this->start_time, 'end_time' => $this->end_time, 'is_active' => $this->is_active ?? false]
        );

        $this->reset(['name', 'start_time', 'end_time', 'is_active', 'editingId']);
        Flux::toast('Jadwal berhasil disimpan.');
    }

    public function edit($id)
    {
        $schedule = Schedule::find($id);
        $this->editingId = $id;
        $this->name = $schedule->name;
        $this->start_time = $schedule->start_time;
        $this->end_time = $schedule->end_time;
        $this->is_active = $schedule->is_active;
    }

    public function render()
    {
        return view('livewire.schedule-manager', [
            'schedules' => Schedule::all()
        ]);
    }
}