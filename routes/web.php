<?php

use App\Http\Controllers\CounselingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeVisitController;
use App\Http\Controllers\LateRecordController;
use App\Http\Controllers\ParentMeetingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuickEntryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentSearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViolationCategoryController;
use App\Http\Controllers\ViolationRecordController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/theme', function(\Illuminate\Http\Request $req) {
        session(['theme' => $req->theme === 'dark' ? 'dark' : 'light']);
        return response()->json(['ok' => true]);
    });

    // Pencarian Global
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // API Autocomplete Siswa
    Route::get('/api/students/search', StudentSearchController::class)->name('api.students.search');

    // Input Cepat
    Route::get('/quick-entry',  [QuickEntryController::class, 'create'])->name('quick-entry.create');
    Route::post('/quick-entry', [QuickEntryController::class, 'store'])->name('quick-entry.store');

    // Data Siswa
    Route::get('/students/template', [StudentController::class, 'downloadTemplate'])->name('students.template');
    Route::post('/students/import',  [StudentController::class, 'import'])->name('students.import');
    Route::resource('students', StudentController::class);

    // Transaksi
    Route::resource('late-records',      LateRecordController::class);
    Route::resource('violation-records', ViolationRecordController::class);
    Route::resource('counselings',       CounselingController::class);
    Route::resource('parent-meetings',   ParentMeetingController::class);
    Route::resource('home-visits',       HomeVisitController::class);

    // Laporan
    Route::get('/reports',         [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf',     [ReportController::class, 'printPdf'])->name('reports.pdf');
    Route::get('/reports/excel',   [ReportController::class, 'exportExcel'])->name('reports.excel');

    // Master Data (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users',                UserController::class);
        Route::resource('school-years',         SchoolYearController::class);
        Route::resource('violation-categories', ViolationCategoryController::class);
        Route::resource('school-classes',       SchoolClassController::class);
    });
});

require __DIR__.'/auth.php';
