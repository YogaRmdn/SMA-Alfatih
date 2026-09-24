<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Album;
use App\Models\Announcement;
use App\Models\Banner;
use App\Models\Contact;
use App\Models\Extracurricular;
use App\Models\Facility;
use App\Models\News;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Program;
use App\Models\Teacher;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $contact = Contact::first();

        $heroBanners = Banner::query()
            ->where('is_active', true)
            ->where('position', 'hero')
            ->orderBy('sort_order')
            ->get();

        $announcements = Announcement::published()
            ->latest('published_at')
            ->limit(6)
            ->get();

        $programs = Program::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $facilities = Facility::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $extracurriculars = Extracurricular::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $testimonials = Testimonial::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $partners = Partner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $news = News::published()
            ->with(['category', 'author'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        $albums = Album::with(['galleries' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->latest()
            ->limit(4)
            ->get();

        $prestasiPhotos = Achievement::query()
            ->where('is_active', true)
            ->whereNotNull('photo')
            ->orderByDesc('year')
            ->orderBy('sort_order')
            ->limit(12)
            ->get()
            ->map(fn (Achievement $achievement): array => [
                'url' => asset('storage/'.$achievement->photo),
                'caption' => $achievement->title,
            ])
            ->values()
            ->all();

        $headmaster = Teacher::query()
            ->where('is_active', true)
            ->where('position', 'LIKE', '%Kepala Sekolah%')
            ->latest()
            ->first();

        if (! $headmaster) {
            $headmaster = (object) ['name' => 'Ilham Dwitama Haeba, Ph.D.', 'photo' => null];
        }

        $teachers = Teacher::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $profilPages = Page::query()
            ->whereIn('slug', ['tentang-sejarah', 'visi-misi', 'struktur-organisasi', 'sambutan-kepala-sekolah'])
            ->where('is_active', true)
            ->get()
            ->keyBy('slug');

        return view('frontend.home', compact(
            'contact',
            'heroBanners',
            'announcements',
            'programs',
            'facilities',
            'extracurriculars',
            'testimonials',
            'partners',
            'news',
            'albums',
            'prestasiPhotos',
            'headmaster',
            'teachers',
            'profilPages'
        ));
    }
}
