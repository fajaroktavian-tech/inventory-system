<?php

use App\Livewire\Absensi\ClassAttendance;
use Illuminate\Support\Facades\Route;
use App\Livewire\Sarpras\Dashboard;
use App\Livewire\Sarpras\RfidRequest;
use App\Livewire\Sarpras\UserRequest;
use App\Livewire\UserManagement;
use App\Livewire\Sarpras\ItemManagement;
use App\Livewire\Sarpras\CategoryManagement;
use App\Livewire\ClassManagement;
use App\Livewire\RoomManagement;
use App\Livewire\Sarpras\ItemIncomingManagement;
use App\Livewire\StudentManagement;
use App\Livewire\Sarpras\RequestApproval;
use App\Livewire\Sarpras\InventoryReport;
use App\Livewire\Sarpras\RequestHistory;
use App\Livewire\Sarpras\ActivityMonitor;
use App\Livewire\StaffManagement;
use App\Livewire\Sarpras\AssetItemManagement;
use App\Livewire\Sarpras\AssetRegistration;
use App\Livewire\Sarpras\AssetLoanManagement;
use App\Livewire\Absensi\DashboardAbsen;
use App\Livewire\ScheduleManager;
use App\Livewire\Sarpras\KiosGateway;
use App\Livewire\Sarpras\AssetRfidLoan;
use App\Livewire\Sarpras\AssetReport;
use App\Livewire\Sarpras\DashboardAsset;
use App\Livewire\Absensi\AttendanceGateway;
use App\Livewire\Absensi\AttendanceMonitor;
use App\Livewire\Absensi\AttendanceReport;
use App\Livewire\ClassAttendanceRecap;
use App\Livewire\Absensi\PiketEntry;
use App\Models\Item;
use App\Models\Room;
use App\Models\Category;
use App\Models\RequestModel;
use App\Models\AssetLoan;
use App\Livewire\ProdiManagement;
use App\Http\Controllers\Sarpras\LandingController;
use App\Http\Controllers\PrintController;
use App\Livewire\HolidayManager;
use App\Livewire\Sarpras\AssetIndex;

Route::get('/', function () {
        return view('welcome');
})->name('home');

Route::get('/sarpras', function () {
    // Ambil data yang diperlukan
    $totalItems = \App\Models\Item::sum('stock');
    $todayRequests = \App\Models\RequestModel::whereDate('created_at', today())->count();
    $totalRooms = \App\Models\Room::count();
    $totalCategories = \App\Models\Category::count();
    $lastActivity = \App\Models\RequestModel::latest()->first();
    $lastUpdate = $lastActivity ? $lastActivity->created_at->diffForHumans() : 'Tidak ada aktivitas';

    return view('sarpras.landing', compact(
        'totalItems', 'todayRequests', 'totalRooms', 'totalCategories', 'lastUpdate'
    ));
})->name('sarpras.landing');

Route::get('/kios-permintaan', RfidRequest::class)->name('rfid.request');

Route::get('/monitor-aktivitas', ActivityMonitor::class)->name('monitor.aktivitas');

Route::get('/kios-aset', AssetRfidLoan::class)->name('kios-aset');

Route::get('/kios', KiosGateway::class)->name('kios.gateway');

Route::get('/attendance-gateway', AttendanceGateway::class)->name('attendance.gateway');


// --- AKSES TERPROTEKSI (HARUS LOGIN) ---
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Utama
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('/dashboard-asset', DashboardAsset::class)->name('dashboard-asset');
    Route::get('/dashboard-absen', DashboardAbsen::class)->name('dashboard.absen');
    Route::get('/absensi/export-alpa', function () {
        $today = Carbon\Carbon::today();
        $attendedIds = \App\Models\Attendance::where('date', $today)->pluck('user_id');
        
        $absentStudents = \App\Models\User::where('role', 'siswa')
            ->where('is_active', true)
            ->whereNotIn('id', $attendedIds)
            ->with('class')
            ->get();
    
        return view('pdf.absent-students', compact('absentStudents', 'today'));
    })->name('attendance.export-alpa')->middleware('auth');
    
    Route::middleware(['can:create-request'])->group(function () {
        Route::get('/buat-permintaan', UserRequest::class)->name('user.request');
        Route::get('/riwayat-permintaan', RequestHistory::class)->name('request.history');
    });

    Route::middleware(['auth', 'role:admin,kesiswaan'])->name('attendance.')->group(function () {
        Route::get('/absensi/monitoring', AttendanceMonitor::class)->name('monitor');
        Route::get('/absensi/rekap', AttendanceReport::class)->name('report');
    });

    // 2. Akses Wali Kelas (Hanya untuk kelasnya sendiri)
    Route::middleware(['auth', 'role:walikelas,admin'])->name('attendance.')->group(function () {
        Route::get('/absensi/kelas', ClassAttendance::class)->name('class');
        Route::get('/absensi/rekap-kelas', ClassAttendanceRecap::class)->name('recap.class');
    });

    // 3. Akses Guru Piket (Input Manual & Dispensasi)
    Route::middleware(['auth', 'role:piket,admin'])->name('attendance.')->group(function () {
        Route::get('/absensi/piket', PiketEntry::class)->name('piket');
    });
    // --- GRUP KHUSUS ADMIN & PETUGAS ---
    Route::middleware(['can:manage-inventory'])->group(function () {
        Route::get('/items', ItemManagement::class)->name('items.index');
        Route::get('/categories', CategoryManagement::class)->name('categories.index');
        Route::get('/items-in', ItemIncomingManagement::class)->name('items-in.index');
        Route::get('/admin/approval', RequestApproval::class)->name('request.approval');
        Route::get('/admin/laporan', InventoryReport::class)->name('report');
        Route::get('/asset-master', AssetItemManagement::class)->name('asset-master.index');
        Route::get('/asset-registration', AssetRegistration::class)->name('asset-registration.index');
        Route::get('/asset-loans', AssetLoanManagement::class)->name('asset-loans.index');
        Route::get('/rooms', RoomManagement::class)->name('rooms.index');
        Route::get('/asset-report', AssetReport::class)->name('asset-report');
        Route::get('/pdf/room-dir/{room}', [PrintController::class, 'printRoomDir'])->name('print.room.dir');
        Route::get('/admin/assets', AssetIndex::class)->name('admin.assets');

        Route::get('/admin/asset-loans/export-pdf', function () {
            $loans = AssetLoan::with(['asset.itemInfo', 'user'])->latest()->get();
            return view('pdf.asset-loans-pdf', compact('loans'));
        })->name('asset-loans.export.pdf');


        // Grup Manajemen Data Master (Admin Saja)
        Route::prefix('admin')->group(function () {
            Route::get('/users', UserManagement::class)->name('users.index');
            Route::get('/classes', ClassManagement::class)->name('classes.index');
            Route::get('/students', StudentManagement::class)->name('students.index');
            Route::get('/prodis', ProdiManagement::class)->name('prodis.index');
            Route::get('/staff', StaffManagement::class)->name('staff.index');
            Route::get('/schedules', ScheduleManager::class)->name('schedules.index');
            Route::get('/holiday', HolidayManager::class)->name('holiday.index');
        });
    });
});

require __DIR__ . '/settings.php';