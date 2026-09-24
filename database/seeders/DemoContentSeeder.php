<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Album;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Extracurricular;
use App\Models\Facility;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Partner;
use App\Models\Program;
use App\Models\Teacher;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCategories();
        $this->seedNews();
        $this->seedAnnouncements();
        $this->seedPrograms();
        $this->seedFacilities();
        $this->seedExtracurriculars();
        $this->seedAchievements();
        $this->seedTestimonials();
        $this->seedPartners();
        $this->seedAlbums();
        $this->seedTeachers();
    }

    protected function seedCategories(): void
    {
        foreach (['Kegiatan', 'Prestasi', 'Pengumuman', 'PPDB'] as $name) {
            Category::firstOrCreate(['name' => $name], ['description' => "Berita kategori {$name} SMA IT Tahfizh Al-Fatih."]);
        }
    }

    protected function seedNews(): void
    {
        $now = now();
        $items = [
            ['Kegiatan', 'Pesantren Ramadhan 1447 H Berlangsung Meriah dan Khidmat', 'img/1.jpeg'],
            ['Prestasi', 'Siswa Al-Fatih Raih Juara 1 MTQ Tingkat Kota Pekanbaru', 'img/2.jpeg'],
            ['PPDB', 'Gelombang 2 Pendaftaran PPDB Tahun Ajaran 2026/2027 Telah Dibuka', 'img/3.jpeg'],
            ['Pengumuman', 'Jadwal Libur Sekolah dan Kegiatan Muharram', 'img/4.jpeg'],
            ['Kegiatan', 'Siswa Menyantuni Anak Yatim dalam Program Peduli Sesama', 'img/5.jpeg'],
            ['Prestasi', 'Tim Futsal Al-Fatih Melaju ke Final Antar Sekolah Se-Riau', 'img/6.jpeg'],
        ];

        foreach ($items as $index => [$categoryName, $title, $thumbnail]) {
            News::updateOrCreate(
                ['title' => $title],
                [
                    'category_id' => Category::where('name', $categoryName)->value('id'),
                    'excerpt' => "Ringkasan singkat: {$title} di lingkungan SMA IT Tahfizh Al-Fatih Pekanbaru.",
                    'content' => '<p>'.e($title).'.</p><p>Alhamdulillah, kegiatan ini berjalan dengan lancar dan diikuti dengan penuh antusias oleh seluruh warga sekolah, guru, dan peserta didik SMA IT Tahfizh Al-Fatih Pekanbaru.</p>',
                    'thumbnail' => $thumbnail,
                    'is_published' => true,
                    'published_at' => $now->subDays($index * 3),
                    'views' => rand(50, 300),
                ]
            );
        }
    }

    protected function seedAnnouncements(): void
    {
        $now = now();
        $items = [
            'Pendaftaran PPDB Gelombang 2 Telah Dibuka hingga 30 Juni 2026',
            'Rapat Wali Murid Dilaksanakan Sabtu Pekan Kedua Setiap Bulan',
            'Pembayaran Uang Sekolah Dapat Dilakukan Melalui Rekening Resmi BSI',
            'Kegiatan Ekstrakurikuler Dimulai Pukul 15.00 WIB Setiap Hari',
            'Jadwal Tahfizh Malam (Bina) Diumumkan Setiap Ahad',
        ];

        foreach ($items as $index => $title) {
            Announcement::updateOrCreate(
                ['title' => $title],
                [
                    'content' => $title.'. Mohon perhatikan informasi ini dengan saksama. Terima kasih.',
                    'is_published' => true,
                    'published_at' => $now->subDays($index * 2),
                ]
            );
        }
    }

    protected function seedPrograms(): void
    {
        $items = [
            ['Tahfizh Al-Qur\'an', 'tahfizh', 'M4 6h16M4 12h16M4 18h16', 'Program unggulan tahfizh 30 juz dengan target hafalan terjadwal dan setoran harian.', 'target_hafalan'],
            ['Akademik Terpadu', 'akademik', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'Kurikulum nasional terpadu dengan pendalaman ilmu-ilmu Islam.', 'kurikulum'],
            ['Tahsin & Tahfizh Intensif', 'tahfizh', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'Pembinaan bacaan Al-Qur\'an (tahsin) dan program hafalan intensif.', 'tahsin'],
            ['Sains & Teknologi', 'it', 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'Pengembangan literasi digital, coding, dan riset sains bagi siswa.', 'sains'],
            ['Bahasa & Komunikasi', 'akademik', 'M3 5h18M3 12h18M3 19h18', 'Program penguatan bahasa Arab dan Inggris sebagai bahasa komunikasi.', 'bahasa'],
            ['Kepemimpinan Islami', 'unggulan', 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125', 'Pembinaan karakter kepemimpinan, kemandirian, dan organisasi siswa.', 'leadership'],
        ];

        foreach ($items as $sort => [$name, $type, $icon, $excerpt, $slug]) {
            Program::updateOrCreate(
                ['name' => $name],
                [
                    'type' => $type,
                    'icon' => $icon,
                    'excerpt' => $excerpt,
                    'content' => '<p>'.e($excerpt).'</p><p>Program ini dirancang untuk mendukung tumbuh kembang peserta didik secara holistik.</p>',
                    'sort_order' => $sort,
                    'is_active' => true,
                ]
            );
        }
    }

    protected function seedFacilities(): void
    {
        $items = [
            ['Masjid', 'M6 21v-9l-1.5.75M18 21v-9l1.5.75M8 21V12h8v9M7 12l5-5 5 5M10.5 6.5a1.5 1.5 0 103 0 1.5 1.5 0 00-3 0zM12 4v2.5M12 12v9', 'Masjid megah untuk shalat berjamaah dan kegiatan tahfizh.'],
            ['Perpustakaan & Rumah Qur\'an', 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25', 'Koleksi ribuan buku dan mushaf bagi siswa.'],
            ['Gedung Kelas Ber-AC', 'M4 6h16M4 6v12h16V6M8 10h8', 'Ruang belajar nyaman dan representatif.'],
            ['Aula Pertemuan', 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z', 'Aula serbaguna untuk acara sekolah dan seminar.'],
            ['Lapangan Olahraga', 'M8.25 15l.75 1.5m-.75-6L15.75 7.5 13.5 18m0-.75l.75 1.5M16.5 15.75l.75-1.5m-9 0l-.75 1.5', 'Lapangan untuk futsal, basket, dan upacara.'],
            ['Kolam Renang', 'M3 7h18v12H3zM3 7l2-3h14l2 3M6 12c2-1.5 4-1.5 6 0s4 1.5 6 0M6 16c2-1.5 4-1.5 6 0s4 1.5 6 0', 'Kolam renang untuk pembelajaran olahraga dan ekstrakurikuler renang.'],
            ['Asrama Putra & Putri', 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25', 'Asrama nyaman bagi siswa yang mengikuti program bina/tahfizh.'],
            ['Kantin Sehat', 'M13.5 21v-7.5M15.75 21v-7.5M15.75 13.5c.621 0 1.125-.504 1.125-1.125V8.25c0-.621-.504-1.125-1.125-1.125M13.5 8.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H9.375c-.621 0-1.125.504-1.125 1.125v3.375c0 .621-.504 1.125-1.125 1.125M3 21V6.375c0-.621.504-1.125 1.125-1.125h9.75C14.619 5.25 15 5.631 15 6.125V21', 'Menyediakan makanan bergizi dengan standar kebersihan halal.'],
        ];

        foreach ($items as $sort => [$name, $icon, $description]) {
            Facility::updateOrCreate(
                ['name' => $name],
                [
                    'icon' => $icon,
                    'description' => $description,
                    'sort_order' => $sort,
                    'is_active' => true,
                ]
            );
        }
    }

    protected function seedExtracurriculars(): void
    {
        $items = [
            ['Hadroh', 'Jumat, 16.00 - 17.30 WIB', 'Ust. Muhammad Ilham', 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm-9-13l12-3'],
            ['Silat', 'Sabtu, 14.00 WIB', 'Kang Deden', 'M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z'],
            ['Arabic Club', 'Selasa & Kamis, 15.30 WIB', 'Ustd. Hana Yusuf', 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Futsal', 'Selasa & Kamis, 16.00 WIB', 'Kang Dedi', 'M15 7.5v3m1.5-1.5h-3M3 13l5.493-5.493a1.75 1.75 0 012.475 0L15.5 12m-9-3a3 3 0 100-6 3 3 0 000 6zM8.25 21v-2.25a1.5 1.5 0 011.5-1.5h4.5a1.5 1.5 0 011.5 1.5V21'],
            ['Tenis Meja', 'Rabu, 15.00 WIB', 'Taufik Hidayat, S.Kom.', 'M7.5 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25h1.5m9-16.5h1.5a2.25 2.25 0 012.25 2.25v12a2.25 2.25 0 01-2.25 2.25h-1.5M12 3.75v16.5'],
            ['Renang', 'Jumat, 15.00 WIB', 'Yusuf Ramadhan, S.Or.', 'M13 10V3L4 14h7v7l9-11h-7z'],
            ['Voli', 'Senin & Rabu, 16.00 WIB', 'Andi Saputra, S.Pd.', 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ['Matematika', 'Kamis, 15.30 WIB', 'Rina Marlina, M.Pd.', 'M12 14l9-5-9-5-9 5 9 5zm0 0v6.969c0 .538-.224 1.052-.622 1.412A9.99 9.99 0 0112 23a9.99 9.99 0 01-6.378-2.619A1.98 1.98 0 015 18.97V14m9-6a2 2 0 10-2 2 2 2 0 002-2z'],
            ['MHQ & MTQ', 'Sabtu, 09.00 WIB', 'Ust. Ahmad Fauzi, Lc.', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ];

        foreach ($items as $sort => [$name, $schedule, $advisor, $icon]) {
            Extracurricular::updateOrCreate(
                ['name' => $name],
                [
                    'schedule' => $schedule,
                    'advisor' => $advisor,
                    'icon' => $icon,
                    'description' => "Ekstrakurikuler {$name} SMA IT Tahfizh Al-Fatih.",
                    'sort_order' => $sort,
                    'is_active' => true,
                ]
            );
        }
    }

    protected function seedAchievements(): void
    {
        $items = [
            ['Juara 1 MTQ Pelajar', 'Tahfizh', 'Juara 1', 2025],
            ['Juara 2 Lomba Tahfizh 5 Juz', 'Tahfizh', 'Juara 2', 2025],
            ['Juara Favorit Olimpiade Sains Nasional', 'Akademik', 'Juara Favorit', 2024],
            ['Juara 1 Futsal Pelajar', 'Olahraga', 'Juara 1', 2024],
            ['Juara 3 Karya Ilmiah Remaja', 'Akademik', 'Juara 3', 2024],
            ['Raih Medali Emas Cerdas Cermat Agama', 'Agama', 'Medali Emas', 2023],
            ['Juara 2 Kaligrafi', 'Seni', 'Juara 2', 2023],
            ['Peserta Terbaik Kemah Pramuka', 'Pramuka', 'Terbaik', 2023],
        ];

        foreach ($items as $sort => [$title, $category, $rank, $year]) {
            Achievement::updateOrCreate(
                ['title' => $title],
                [
                    'category' => $category,
                    'rank' => $rank,
                    'year' => $year,
                    'description' => "Prestasi {$title} yang membanggakan bagi SMA IT Tahfizh Al-Fatih.",
                    'sort_order' => $sort,
                    'is_active' => true,
                ]
            );
        }
    }

    protected function seedTestimonials(): void
    {
        $items = [
            ['H. Abdullah Firmansyah', 'Wali Murid', 2024, 'Anak saya sangat betah di Al-Fatih. Bacaan Al-Qur\'annya jauh lebih baik dan akhlaknya berkembang pesat.'],
            ['Ustadz Rizky Hidayat', 'Alumni & Pembina', 2019, 'Pengalaman belajar di Al-Fatih membentuk disiplin dan kecintaan terhadap Al-Qur\'an yang bertahan sampai sekarang.'],
            ['Bunda Sari Wulandari', 'Wali Murid', 2025, 'Gurunya ramah dan sabar. Fasilitas lengkap, program tahfizhnya terstruktur dengan jelas. Sangat direkomendasikan!'],
        ];

        foreach ($items as $sort => [$name, $position, $year, $content]) {
            Testimonial::updateOrCreate(
                ['name' => $name],
                [
                    'position' => $position,
                    'alumni_year' => $year,
                    'content' => $content,
                    'sort_order' => $sort,
                    'is_active' => true,
                ]
            );
        }
    }

    protected function seedPartners(): void
    {
        $partners = [
            ['Universitas Prima Indonesia', 'partners/unpri.png'],
            ['Universitas Islam Madinah', 'partners/uim.png'],
            ['Universitas Riau Indonesia', 'partners/uri.png'],
            ['Erlangga Buku', 'partners/erlangga.png'],
            ['Bimbel Brawijaya', 'partners/bimbel.png'],
            ['Tring Pegadaian', 'partners/tring.png'],
            ['BRK', 'partners/brk.png'],
        ];

        foreach ($partners as $sort => [$name, $logo]) {
            Partner::updateOrCreate(
                ['name' => $name],
                ['logo' => $logo, 'description' => "Mitra kerja sama {$name}.", 'sort_order' => $sort, 'is_active' => true]
            );
        }
    }

    protected function seedAlbums(): void
    {
        $albums = [
            ['Kegiatan Siswa', 'Dokumentasi keseharian kegiatan belajar dan ibadah siswa.'],
            ['Prestasi & Perlombaan', 'Momen membanggakan siswa dalam berbagai ajang perlombaan.'],
        ];

        foreach ($albums as $sort => [$title, $description]) {
            $album = Album::firstOrCreate(
                ['title' => $title],
                ['description' => $description, 'cover' => 'img/'.($sort * 4 + 1).'.jpeg']
            );

            $album->galleries()->delete();
            $album->galleries()->createMany(
                collect(range(1, 8))->map(fn ($n) => [
                    'title' => "Dokumentasi {$title}",
                    'type' => 'photo',
                    'image' => 'img/'.(($sort * 4 + $n - 1) % 11 + 1).'.jpeg',
                    'description' => null,
                    'sort_order' => $n,
                    'is_active' => true,
                ])->all()
            );
        }
    }

    protected function seedTeachers(): void
    {
        $items = [
            ['Ilham Dwitama Haeba, Ph.D.', 'Kepala Sekolah', 'Tahfizh Al-Qur\'an'],
            ['Hj. Siti Aminah, S.Pd.', 'Wakil Kepala Kurikulum', 'Matematika'],
            ['Dedi Firmansyah, S.Pd.', 'Wakil Kepala Kesiswaan', 'Bahasa Indonesia'],
            ['Rina Marlina, M.Pd.', 'Guru', 'Bahasa Inggris'],
            ['Ahmad Fauzi, S.Si.', 'Guru', 'Fisika'],
            ['Rahma Yanti, S.Si.', 'Guru', 'Biologi'],
            ['Budi Santoso, S.Ag.', 'Guru', 'Pendidikan Agama Islam'],
            ['Zainal Abidin, S.Pd.', 'Guru', 'Seni Budaya / Kaligrafi'],
            ['Nurul Hidayah, S.Pd.', 'Guru', 'IPS'],
            ['Hendra Gunawan, S.Kom.', 'Guru', 'Informatika'],
            ['Dewi Anggraini, S.Psi.', 'Guru BK', 'Bimbingan Konseling'],
            ['Abdul Aziz, M.Pd.', 'Guru', 'Tahsin & Tahfizh'],
        ];

        foreach ($items as $sort => [$name, $position, $subject]) {
            Teacher::updateOrCreate(
                ['name' => $name],
                [
                    'position' => $position,
                    'subject' => $subject,
                    'education' => 'S1/S2',
                    'sort_order' => $sort,
                    'is_active' => true,
                ]
            );
        }
    }
}