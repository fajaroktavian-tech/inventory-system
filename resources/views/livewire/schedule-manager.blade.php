<div class="p-6 max-w-4xl mx-auto space-y-6">
    <flux:heading size="xl">Pengaturan Jadwal Sekolah</flux:heading>

    {{-- FORM INPUT --}}
    <flux:card class="p-4 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <flux:input label="Nama Jadwal" wire:model="name" />
        <flux:checkbox label="Aktifkan Jadwal" wire:model="is_active" />
        
        <div class="col-span-2">
            <flux:label>Pilih Hari Berlaku:</flux:label>
            <div class="flex gap-4 mt-2">
                @foreach($daysOptions as $key => $day)
                    <flux:checkbox label="{{ $day }}" wire:model="days" value="{{ $key }}" />
                @endforeach
            </div>
        </div>

        <flux:input label="Jam Masuk" type="time" wire:model="start_time" />
        <flux:input label="Jam Pulang" type="time" wire:model="end_time" />
    </div>
    <flux:button variant=primary wire:click="save">Simpan Jadwal</flux:button>
</flux:card>

    {{-- TABEL JADWAL --}}
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Nama</flux:table.column>
            <flux:table.column>Jam Masuk</flux:table.column>
            <flux:table.column>Jam Pulang</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach($schedules as $s)
            <flux:table.row>
                <flux:table.cell>{{ $s->name }}</flux:table.cell>
                <flux:table.cell>{{ $s->start_time }}</flux:table.cell>
                <flux:table.cell>{{ $s->end_time }}</flux:table.cell>
                <flux:table.cell>
                    <flux:badge color="{{ $s->is_active ? 'green' : 'zinc' }}">{{ $s->is_active ? 'Aktif' : 'Non-Aktif' }}</flux:badge>
                </flux:table.cell>
                <flux:table.cell>
                    <flux:button variant="ghost" size="sm" wire:click="edit({{ $s->id }})">Edit</flux:button>
                </flux:table.cell>
            </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>