<div class="p-6 max-w-4xl mx-auto space-y-6">
    <flux:heading size="xl">Manajemen Hari Libur</flux:heading>

    {{-- FORM INPUT --}}
    <flux:card class="p-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:input label="Tanggal Mulai" type="date" wire:model="startDate" />
            <flux:input label="Tanggal Selesai" type="date" wire:model="endDate" />
        </div>
        <flux:input label="Keterangan Libur" wire:model="description" placeholder="Contoh: Libur Semester Genap" />
        <flux:button variant="primary" wire:click="saveHoliday">Simpan Periode Libur</flux:button>
    </flux:card>

    {{-- LIST LIBUR --}}
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Tanggal</flux:table.column>
            <flux:table.column>Keterangan</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach($holidays as $h)
                <flux:table.row>
                    <flux:table.cell>{{ $h->date->format('d M Y') }}</flux:table.cell>
                    <flux:table.cell>{{ $h->description }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button variant="primary" color="red" wire:click="deleteHoliday({{ $h->id }})">Hapus
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
    <div class="mt-4">
        {{ $holidays->links() }}
    </div>
</div>