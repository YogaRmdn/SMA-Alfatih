<?php

use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\NewsController;
use App\Http\Controllers\Frontend\PpdbController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('berita', [NewsController::class, 'index'])->name('news.index');
Route::get('berita/{news:slug}', [NewsController::class, 'show'])->name('news.show');
Route::post('berita/{news:slug}/komentar', [NewsController::class, 'comment'])
    ->middleware('throttle:10,1')
    ->name('news.comment');

Route::middleware('throttle:20,1')->group(function () {
    Route::get('ppdb', [PpdbController::class, 'create'])->name('ppdb.register');
    Route::post('ppdb', [PpdbController::class, 'store'])->name('ppdb.store');
    Route::get('ppdb/success/{ppdb}', [PpdbController::class, 'success'])->name('ppdb.success');
    Route::get('ppdb/status', [PpdbController::class, 'status'])->name('ppdb.status');
    Route::post('ppdb/status', [PpdbController::class, 'checkStatus'])->name('ppdb.status.check');
});

Route::middleware('throttle:30,1')->group(function () {
    Route::get('ppdb/dokumen/{ppdb}/{type}', [PpdbController::class, 'document'])
        ->where('type', 'photo|kk|birth_certificate|diploma|report_card')
        ->name('ppdb.document');
});

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/password/change', [PasswordController::class, 'edit'])->name('password.change');
    Route::put('/password/change', [PasswordController::class, 'update'])->name('password.update.custom');
});

require __DIR__.'/auth.php';
