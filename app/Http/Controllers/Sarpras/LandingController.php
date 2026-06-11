<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Room;

class LandingController extends Controller
{
    public function index()
    {
        // Mengambil data untuk statistik
        $totalAset = Asset::count();
        $barangRusak = Asset::where('condition', '!=', 'baik')->count();
        $totalRuangan = Room::count();

        return view('sarpras.landing', compact('totalAset', 'barangRusak', 'totalRuangan'));
    }
}
