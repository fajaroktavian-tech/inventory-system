<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintController extends Controller
{
    public function printRoomDir(Room $room)
    {
        $room->load(['assets.itemInfo', 'assets.pic']);
        $totalValue = $room->assets->sum('price');

        // Menggunakan library dompdf
        $pdf = Pdf::loadView('pdf.room-dir', [
            'room' => $room,
            'totalValue' => $totalValue
        ])->setPaper('a4', 'portrait');

        // Opsi 1: Langsung download
        // return $pdf->download('DIR_' . $room->name . '.pdf');

        // Opsi 2: Tampilkan di browser (lebih disukai untuk cetak)
        return $pdf->stream('DIR_' . $room->name . '.pdf');
    }
}
