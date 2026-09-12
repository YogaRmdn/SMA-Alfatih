<?php

use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\AlbumController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DownloadController;
use App\Http\Controllers\Admin\ExtracurricularController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\PpdbController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:super_admin,admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('dashboard', function () {
            return redirect()->route('admin.dashboard');
        })->name('dashboard.redirect');

        Route::resource('news', NewsController::class)->except('show');
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('announcements', AnnouncementController::class)->except('show');
        Route::resource('teachers', TeacherController::class)->except('show');
        Route::resource('staffs', StaffController::class)->except('show');
        Route::resource('albums', AlbumController::class)->except('show');
        Route::resource('galleries', GalleryController::class)->except('show');
        Route::resource('achievements', AchievementController::class)->except('show');
        Route::resource('facilities', FacilityController::class)->except('show');
        Route::resource('extracurriculars', ExtracurricularController::class)->except('show');
        Route::resource('programs', ProgramController::class)->except('show');
        Route::resource('sliders', SliderController::class)->except('show');
        Route::resource('banners', BannerController::class)->except('show');
        Route::resource('downloads', DownloadController::class)->except('show');
        Route::resource('testimonials', TestimonialController::class)->except('show');
        Route::resource('partners', PartnerController::class)->except('show');
        Route::resource('faqs', FaqController::class)->except('show');
        Route::resource('pages', PageController::class)->except('show');
        Route::resource('comments', CommentController::class)->only(['index', 'destroy']);
        Route::post('comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');

        Route::get('ppdb', [PpdbController::class, 'index'])->name('ppdb.index');
        Route::get('ppdb/{ppdb}', [PpdbController::class, 'show'])->name('ppdb.show');
        Route::put('ppdb/{ppdb}/status', [PpdbController::class, 'updateStatus'])->name('ppdb.status');
        Route::delete('ppdb/{ppdb}', [PpdbController::class, 'destroy'])->name('ppdb.destroy');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('contact', [ContactController::class, 'edit'])->name('contact.edit');
        Route::put('contact/{contact}', [ContactController::class, 'update'])->name('contact.update');

        Route::middleware('role:super_admin')->group(function () {
            Route::resource('users', UserController::class)->except('show');
        });
    });
