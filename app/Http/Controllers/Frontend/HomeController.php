<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Album;
use App\Models\Announcement;
use App\Models\Contact;
use App\Models\Extracurricular;
use App\Models\Facility;
use App\Models\News;
use App\Models\Partner;
use App\Models\Program;
use App\Models\Slider;
use App\Models\Teacher;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $contact = Contact::first();

        $sliders = Slider::query()
            ->where('is_active', true)
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

        $achievements = Achievement::query()
            ->where('is_active', true)
            ->orderBy('year', 'desc')
            ->limit(8)
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

        $stats = [
            'teachers' => Teacher::query()->where('is_active', true)->count(),
            'achievements' => Achievement::query()->where('is_active', true)->count(),
            'extracurriculars' => Extracurricular::query()->where('is_active', true)->count(),
            'students' => 600,
        ];

        return view('frontend.home', compact(
            'contact',
            'sliders',
            'announcements',
            'programs',
            'facilities',
            'extracurriculars',
            'achievements',
            'testimonials',
            'partners',
            'news',
            'albums',
            'stats'
        ));
    }
}
