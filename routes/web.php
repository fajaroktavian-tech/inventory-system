<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\RfidRequest;
use App\Livewire\UserRequest;
use App\Livewire\UserManagement;
use App\Livewire\ItemManagement;
use App\Livewire\CategoryManagement;
use App\Livewire\ClassManagement;
use App\Livewire\RoomManagement;
use App\Livewire\ItemIncomingManagement;
use App\Livewire\StudentManagement;
use App\Livewire\RequestApproval;
use App\Livewire\InventoryReport;
use App\Livewire\RequestHistory;
use App\Livewire\ActivityMonitor;
use App\Models\Item;
use App\Models\Room;
use App\Models\Category;
use App\Models\RequestModel;

Route::get('/', function () {
    // 1. Total Item (Menjumlahkan semua kolom stock)
    $totalItems = Item::sum('stock');

    // 2. Permintaan Hari Ini (Status apa saja yang masuk hari ini)
    $todayRequests = RequestModel::whereDate('created_at', today())->count();

    // 3. Ruang Lab
    $totalRooms = Room::count();

    // 4. Jumlah Kategori
    $totalCategories = Category::count();

    // 5. Terakhir Diperbarui (Dari permintaan terbaru)
    $lastActivity = RequestModel::latest()->first();
    $lastUpdate = $lastActivity 
        ? $lastActivity->created_at->diffForHumans() 
        : 'Tidak ada aktivitas';

    return view('welcome', compact(
        'totalItems', 
        'todayRequests', 
        'totalRooms', 
        'totalCategories', 
        'lastUpdate'
    ));
})->name('home');
// Route::view('/', 'welcome')->name('home');
//Route::get('/monitor-sarpras', Dashboard::class)->name('public.display');

Route::get('/kios-permintaan', RfidRequest::class)->name('rfid.request');

Route::get('/monitor-aktivitas', ActivityMonitor::class)->name('monitor.aktivitas');


// --- AKSES TERPROTEKSI (HARUS LOGIN) ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Utama
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::middleware(['can:create-request'])->group(function () {
        Route::get('/buat-permintaan', UserRequest::class)->name('user.request');
        Route::get('/riwayat-permintaan', RequestHistory::class)->name('request.history');
    });

    // --- GRUP KHUSUS ADMIN & PETUGAS ---
    Route::middleware(['can:manage-inventory'])->group(function () {
        Route::get('/items', ItemManagement::class)->name('items.index');
        Route::get('/categories', CategoryManagement::class)->name('categories.index');
        Route::get('/items-in', ItemIncomingManagement::class)->name('items-in.index');
        Route::get('/admin/approval', RequestApproval::class)->name('request.approval');
        Route::get('/admin/laporan', InventoryReport::class)->name('report');
        
        // Grup Manajemen Data Master (Admin Saja)
        Route::prefix('admin')->group(function () {
            Route::get('/users', UserManagement::class)->name('users.index');
            Route::get('/classes', ClassManagement::class)->name('classes.index');
            Route::get('/rooms', RoomManagement::class)->name('rooms.index');
            Route::get('/students', StudentManagement::class)->name('students.index');
        });
    });
});

require __DIR__.'/settings.php';