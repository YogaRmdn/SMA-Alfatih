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
use App\Http\Controllers\Admin\SchoolProfileController;
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

        Route::delete('news/delete-all', [NewsController::class, 'deleteAll'])->name('news.delete-all');
        Route::delete('categories/delete-all', [CategoryController::class, 'deleteAll'])->name('categories.delete-all');
        Route::delete('announcements/delete-all', [AnnouncementController::class, 'deleteAll'])->name('announcements.delete-all');
        Route::delete('albums/delete-all', [AlbumController::class, 'deleteAll'])->name('albums.delete-all');
        Route::delete('galleries/delete-all', [GalleryController::class, 'deleteAll'])->name('galleries.delete-all');
        Route::delete('achievements/delete-all', [AchievementController::class, 'deleteAll'])->name('achievements.delete-all');
        Route::delete('teachers/delete-all', [TeacherController::class, 'deleteAll'])->name('teachers.delete-all');
        Route::delete('staffs/delete-all', [StaffController::class, 'deleteAll'])->name('staffs.delete-all');
        Route::delete('facilities/delete-all', [FacilityController::class, 'deleteAll'])->name('facilities.delete-all');
        Route::delete('extracurriculars/delete-all', [ExtracurricularController::class, 'deleteAll'])->name('extracurriculars.delete-all');
        Route::delete('programs/delete-all', [ProgramController::class, 'deleteAll'])->name('programs.delete-all');
        Route::delete('sliders/delete-all', [SliderController::class, 'deleteAll'])->name('sliders.delete-all');
        Route::delete('banners/delete-all', [BannerController::class, 'deleteAll'])->name('banners.delete-all');
        Route::delete('downloads/delete-all', [DownloadController::class, 'deleteAll'])->name('downloads.delete-all');
        Route::delete('testimonials/delete-all', [TestimonialController::class, 'deleteAll'])->name('testimonials.delete-all');
        Route::delete('partners/delete-all', [PartnerController::class, 'deleteAll'])->name('partners.delete-all');
        Route::delete('faqs/delete-all', [FaqController::class, 'deleteAll'])->name('faqs.delete-all');
        Route::delete('pages/delete-all', [PageController::class, 'deleteAll'])->name('pages.delete-all');
        Route::delete('comments/delete-all', [CommentController::class, 'deleteAll'])->name('comments.delete-all');
        Route::delete('ppdb/delete-all', [PpdbController::class, 'deleteAll'])->name('ppdb.delete-all');

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
        Route::get('ppdb/{ppdb}/document/{type}', [PpdbController::class, 'document'])
            ->where('type', 'photo|kk|birth_certificate|diploma|report_card')
            ->name('ppdb.document');
        Route::put('ppdb/{ppdb}/status', [PpdbController::class, 'updateStatus'])->name('ppdb.status');
        Route::delete('ppdb/{ppdb}', [PpdbController::class, 'destroy'])->name('ppdb.destroy');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('contact', [ContactController::class, 'edit'])->name('contact.edit');
        Route::put('contact/{contact}', [ContactController::class, 'update'])->name('contact.update');

        Route::get('profil/tentang-sejarah', [SchoolProfileController::class, 'edit'])->defaults('section', 'tentang-sejarah')->name('profile.edit');
        Route::put('profil/tentang-sejarah', [SchoolProfileController::class, 'update'])->defaults('section', 'tentang-sejarah')->name('profile.update');
        Route::get('profil/visi-misi', [SchoolProfileController::class, 'edit'])->defaults('section', 'visi-misi')->name('visi.edit');
        Route::put('profil/visi-misi', [SchoolProfileController::class, 'update'])->defaults('section', 'visi-misi')->name('visi.update');
        Route::get('profil/struktur-organisasi', [SchoolProfileController::class, 'edit'])->defaults('section', 'struktur-organisasi')->name('structure.edit');
        Route::put('profil/struktur-organisasi', [SchoolProfileController::class, 'update'])->defaults('section', 'struktur-organisasi')->name('structure.update');
        Route::get('profil/sambutan', [SchoolProfileController::class, 'edit'])->defaults('section', 'sambutan')->name('welcome.edit');
        Route::put('profil/sambutan', [SchoolProfileController::class, 'update'])->defaults('section', 'sambutan')->name('welcome.update');

        Route::middleware('role:super_admin')->group(function () {
            Route::resource('users', UserController::class)->except('show');
        });
    });
