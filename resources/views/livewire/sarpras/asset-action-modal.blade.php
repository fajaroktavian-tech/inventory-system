<div>
    <!-- Modal Pindah Ruangan -->
    <flux:modal name="transfer-room-modal" class="md:w-96 space-y-6" wire:model="isTransferModalOpen">
        <div>
            <flux:heading size="lg">Mutasi / Pindah Ruangan</flux:heading>
            <flux:subheading>Pindahkan unit aset ke ruangan lain secara resmi.</flux:subheading>
        </div>

        <form wire:submit.prevent="saveTransfer" class="space-y-4">
            <div>
                <flux:label>Ruangan Saat Ini</flux:label>
                <div class="font-medium text-zinc-700 dark:text-zinc-300 py-1">
                    {{ $asset->room->name ?? '-' }}
                </div>
            </div>

            <flux:select wire:model="new_room_id" label="Pilih Ruangan Tujuan" required>
                <option value="">-- Pilih Ruangan --</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                @endforeach
            </flux:select>

            <flux:textarea wire:model="transfer_notes" label="Catatan / Alasan Pindah (Opsional)" placeholder="Contoh: Pindah karena perbaikan tata letak lab." />

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="subtle">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Simpan Mutasi</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Modal Perubahan Kondisi -->
    <flux:modal name="condition-modal" class="md:w-96 space-y-6" wire:model="isConditionModalOpen">
        <div>
            <flux:heading size="lg">Perbarui Kondisi Fisik</flux:heading>
            <flux:subheading>Catat perubahan status kondisi unit aset.</flux:subheading>
        </div>

        <form wire:submit.prevent="saveCondition" class="space-y-4">
            <flux:select wire:model="new_condition" label="Kondisi Terbaru" required>
                <option value="baik">Baik</option>
                <option value="rusak_ringan">Rusak Ringan</option>
                <option value="rusak_berat">Rusak Berat</option>
            </flux:select>

            <flux:textarea wire:model="condition_notes" label="Keterangan / Kerusakan (Opsional)" placeholder="Contoh: Port HDMI tidak berfungsi / Layar bergaris." />

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="subtle">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Simpan Kondisi</flux:button>
            </div>
        </form>
    </flux:modal>
    <!-- Modal Perubahan PIC -->
    <flux:modal name="pic-modal" class="md:w-96 space-y-6" wire:model="isPicModalOpen">
        <div>
            <flux:heading size="lg">Ganti Penanggung Jawab (PIC)</flux:heading>
            <flux:subheading>Alihkan kepemilikan/tanggung jawab unit aset ke petugas lain.</flux:subheading>
        </div>

        <form wire:submit.prevent="savePic" class="space-y-4">
            <div>
                <flux:label>PIC Saat Ini</flux:label>
                <div class="font-medium text-zinc-700 dark:text-zinc-300 py-1">
                    {{ $asset->pic->name ?? '-' }}
                </div>
            </div>

            <flux:select wire:model="new_pic_id" label="Pilih PIC Baru" required>
                <option value="">-- Pilih Penanggung Jawab --</option>
                @foreach(\App\Models\User::whereIn('role', ['guru', 'staff', 'admin'])->get() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </flux:select>

            <flux:textarea wire:model="pic_notes" label="Catatan (Opsional)"
                placeholder="Contoh: Serah terima tugas inventaris." />

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="subtle">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Simpan PIC</flux:button>
            </div>
        </form>
    </flux:modal>
</div>