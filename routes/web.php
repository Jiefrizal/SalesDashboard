<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\UserController;
use App\Services\SpreadsheetService;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Authentication Routes (public)
|--------------------------------------------------------------------------
*/
Route::get('/login',        [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',       [AuthController::class, 'login'])->name('login.post');
Route::post('/login/viewer', [AuthController::class, 'loginViewer'])->name('login.viewer');
Route::post('/logout',      [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Protected Routes (require login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::middleware('menu:dashboard')->get('/', function () {
        $cabangs = \App\Models\Cabang::all();
        $isValidUrl = $cabangs->contains(fn($c) => !empty($c->spreadsheet_url));
        $dashboardNote = \App\Models\DashboardNote::getNote();

        return view('dashboard.index', compact('cabangs', 'isValidUrl', 'dashboardNote'));
    });

    // STU UNIT Page
    Route::middleware('menu:stu_unit')->get('/stu-unit', [\App\Http\Controllers\StuUnitController::class, 'index'])->name('stu.index');

    // STOK UNIT Page
    Route::middleware('menu:stok_unit')->get('/stok-unit', [\App\Http\Controllers\StokUnitController::class, 'index'])->name('stok.index');

    // Digital Marketing Executive Dashboard
    Route::middleware('menu:digital_marketing')->get('/digital-marketing', [\App\Http\Controllers\DigitalMarketingController::class, 'index'])->name('digital-marketing.index');



    // Permission-controlled Actions
    // Sync spreadsheet
    Route::middleware('menu:cabang')->get('/sync-spreadsheet', function (SpreadsheetService $spreadsheetService) {
        if (!auth()->user()->canEdit()) {
            return redirect('/')->with('error', 'Anda tidak memiliki hak akses untuk menyinkronkan data.');
        }

        $cabangs = \App\Models\Cabang::all();
        $isValidUrl = $cabangs->contains(fn($c) => !empty($c->spreadsheet_url));

        if (!$isValidUrl) {
            return redirect('/')->with('error', 'Tidak ada URL Spreadsheet cabang yang diatur. Silakan atur URL Spreadsheet untuk masing-masing cabang di menu Cabang.');
        }

        $success = $spreadsheetService->syncAll();

        if ($success) {
            Cache::forget('spreadsheet_last_sync');
            return redirect('/')->with('success', 'Data seluruh cabang berhasil disinkronisasi dari spreadsheet masing-masing!');
        } else {
            return redirect('/')->with('error', 'Gagal menyinkronkan data. Pastikan link spreadsheet benar dan telah dipublikasikan ke web sebagai CSV.');
        }
    })->name('sync.spreadsheet');

    // User management resource
    Route::middleware('menu:users')->resource('users', UserController::class);

    // Cabang resource & YTD update & Monthly Sales update
    Route::middleware('menu:cabang')->resource('cabang', CabangController::class);
    Route::middleware('menu:cabang')->post('/cabang/{cabang}/update-ytd', [CabangController::class, 'updateYtd'])->name('cabang.updateYtd');
    Route::middleware('menu:cabang')->post('/cabang/{cabang}/update-monthly-sales', [CabangController::class, 'updateMonthlySales'])->name('cabang.updateMonthlySales');

    // Dashboard catatan — save
    Route::middleware('menu:dashboard')->post('/dashboard/notes', function (\Illuminate\Http\Request $request) {
        if (!auth()->user()->canEdit()) {
            return redirect('/')->with('error', 'Anda tidak memiliki hak akses untuk mengubah catatan dashboard.');
        }

        $request->validate(['content' => 'nullable|string|max:2000']);

        \App\Models\DashboardNote::updateOrCreate(
            ['id' => 1],
            ['content' => $request->input('content'), 'updated_by' => auth()->id()]
        );

        return back()->with('notes_success', 'Catatan berhasil disimpan.');
    })->name('dashboard.notes.save');

});
