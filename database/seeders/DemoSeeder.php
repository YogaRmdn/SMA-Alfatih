<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Album;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Extracurricular;
use App\Models\Facility;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Partner;
use App\Models\Program;
use App\Models\Setting;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    protected const ICON_BOOK = 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253';

    protected const ICON_ACADEMIC = 'M12 14l9-5-9-5-9 5 9 5zm0 0v6.969c0 .538-.224 1.052-.622 1.412A9.99 9.99 0 0112 23a9.99 9.99 0 01-6.378-2.619A1.98 1.98 0 015 18.97V14m9-6a2 2 0 10-2 2 2 2 0 002-2z';

    protected const ICON_SPARKLE = 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z';

    protected const ICON_GLOBE = 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z';

    protected const ICON_CODE = 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4';

    protected const ICON_MUSIC = 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm-9-13l12-3';

    protected const ICON_CAMERA = 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9zM15 13a3 3 0 11-6 0 3 3 0 016 0z';

    protected const ICON_BEAKER = 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z';

    protected const ICON_HEART = 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z';

    protected const ICON_BUILDING = 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4';

    protected const ICON_BOLT = 'M13 10V3L4 14h7v7l9-11h-7z';

    protected const ICON_TROPHY = 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6';

    protected const ICON_USER = 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z';

    protected const IMG_MAIN = '/img/Siang 3.0.png';
    protected const IMG_LEFT = '/img/Siang 3.0. Kiri.png';
    protected const IMG_RIGHT = '/img/Siang 3.0. Kanan.png';

    public function run(): void
    {
        $this->seedContact();
        $this->seedCategories();
        $this->seedPrograms();
        $this->seedFacilities();
        $this->seedExtracurriculars();
        $this->seedTeachers();
        $this->seedAchievements();
        $this->seedTestimonials();
        $this->seedPartners();
        $this->seedAnnouncements();
        $this->seedNews();
        $this->seedAlbums();
    }

    protected function seedContact(): void
    {
        Contact::updateOrCreate(['id' => 1], [
            'address' => Setting::get('address') ?? 'Jl. Garuda Sakti KM 3, Panam, Kec. Tampan, Kota Pekanbaru, Riau 28291',
            'phone' => Setting::get('phone') ?? '(0761) 562206',
            'whatsapp' => Setting::get('whatsapp') ?? '6281270000000',
            'email' => Setting::get('email') ?? 'info@smaitalfatih.sch.id',
            'maps_embed' => Setting::get('maps_embed'),
            'operational_hours' => Setting::get('operational_hours') ?? 'Senin - Jumat: 07.00 - 16.00 WIB',
        ]);
    }

    protected function seedCategories(): void
    {
        foreach (['Akademik', 'Tahfizh & Tahsin', 'Kegiatan', 'Prestasi', 'PPDB'] as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }

    protected function seedPrograms(): void
    {
        $programs = [
            ['Program Tahfizh Al-Qur\'an', self::ICON_BOOK, 'Pembinaan hafalan Al-Qur\'an bertarget 5-10 juz selama tiga tahun didampingi musyrifah/ustadzah.',
                'Program unggulan berupa pembinaan tahfizh Al-Qur\'an dengan target 5-10 juz selama tiga tahun. Metode tahsin yang benar, murajaah terjadwal, dan setoran rutin dipantau langsung oleh pembina tahfizh.'],
            ['Program Akademik Terpadu', self::ICON_ACADEMIC, 'Kurikulum nasional diperkaya nilai-nilai Islami dan pendekatan pembelajaran modern.',
                'Pembelajaran mengikuti kurikulum nasional yang diperkaya dengan nilai-nilai Islami. Kelas interaktif, proyek kolaboratif, dan bimbingan belajar intensif untuk menyiapkan peserta didik menuju perguruan tinggi ternama.'],
            ['Program Tahsin & Tartil', self::ICON_SPARKLE, 'Perbaikan bacaan Al-Qur\'an sesuai kaidah tajwid untuk seluruh peserta didik.',
                'Perbaikan bacaan Al-Qur\'an sesuai kaidah tajwid dan makhrajul huruf. Program ini menjadi fondasi utama sebelum peserta didik memasuki jenjang tahfizh yang lebih intensif.'],
            ['Program Bahasa Arab & Inggris', self::ICON_GLOBE, 'Penguasaan dua bahasa internasional melalui pembiasaan sehari-hari di lingkungan sekolah.',
                'Kemampuan berbahasa Arab dan Inggris dilatih melalui pembiasaan komunikasi harian, kamus santri, muhadatsah, serta program native speaker setiap pekan.'],
            ['Program IT & Robotik', self::ICON_CODE, 'Kelas coding, desain, dan robotik untuk membekali keterampilan abad 21.',
                'Membekali peserta didik dengan keterampilan teknologi digital, dasar-dasar pemrograman, desain grafis, dan robotik melalui praktik langsung di laboratorium komputer.'],
            ['Program Entrepreneurship', self::ICON_BOLT, 'Menumbuhkan jiwa wirausaha melalui praktik bisnis nyata siswa.',
                'Jiwa wirausaha ditumbuhkan melalui praktik bisnis nyata, pasar santri, dan kelas keterampilan. Peserta didik belajar mengelola usaha sederhana secara mandiri dan Islami.'],
        ];

        foreach ($programs as [$name, $icon, $excerpt, $content]) {
            Program::firstOrCreate(['name' => $name], [
                'icon' => $icon,
                'type' => 'unggulan',
                'excerpt' => $excerpt,
                'content' => $content,
                'is_active' => true,
            ]);
        }
    }

    protected function seedFacilities(): void
    {
        $facilities = [
            ['Masjid Dua Lantai', self::ICON_BUILDING, 'Tempat ibadah dan pusat kegiatan keagamaan.'],
            ['Laboratorium IPA', self::ICON_BEAKER, 'Lab fisika, kimia, dan biologi yang lengkap.'],
            ['Perpustakaan', self::ICON_BOOK, 'Koleksi buku lebih dari 5.000 judul.'],
            ['Lab Komputer & Robotik', self::ICON_CODE, '40 unit komputer modern untuk praktik IT.'],
            ['Lapangan Olahraga', self::ICON_BOLT, 'Lapangan futsal, basket, dan voli.'],
            ['Ruang Kelas Ber-AC', self::ICON_ACADEMIC, 'Kelas nyaman dengan multimedia learning.'],
            ['Asrama Putra & Putri', self::ICON_HEART, 'Hunian aman dan nyaman untuk santri mukim.'],
            ['Kantin Sehat', self::ICON_CAMERA, 'Menu makanan bergizi dan higienis.'],
        ];

        foreach ($facilities as [$name, $icon, $desc]) {
            Facility::firstOrCreate(['name' => $name], [
                'icon' => $icon,
                'description' => $desc,
                'is_active' => true,
            ]);
        }
    }

    protected function seedExtracurriculars(): void
    {
        $ekskuls = [
            ['Tahfizh Qur\'an', self::ICON_BOOK, 'Senin - Kamis, 16.00', 'Ust. Ahmad Fauzi, Lc.'],
            ['Futsal', self::ICON_BOLT, 'Jumat, 15.00', 'Yusuf Ramadhan, S.Or.'],
            ['Pramuka', self::ICON_HEART, 'Sabtu, 08.00', 'Rina Marlina, S.Pd.'],
            ['Paskibra', self::ICON_TROPHY, 'Sabtu, 13.00', 'Dimas Prasetyo, S.Pd.'],
            ['Hadrah & Nasyid', self::ICON_MUSIC, 'Jumat, 16.00', 'Ust. Muhammad Ilham'],
            ['Rohis', self::ICON_SPARKLE, 'Sabtu, 10.00', 'Ustd. Siti Rahma, Lc.'],
            ['Basket', self::ICON_BOLT, 'Rabu, 15.00', 'Andi Saputra, S.Pd.'],
            ['English Club', self::ICON_GLOBE, 'Kamis, 15.30', 'Lia Amalia, S.Pd.'],
            ['Robotik', self::ICON_CODE, 'Sabtu, 14.00', 'Taufik Hidayat, S.Kom.'],
            ['Seni Kaligrafi', self::ICON_CAMERA, 'Sabtu, 09.00', 'Ustd. Hana Yusuf'],
        ];

        foreach ($ekskuls as [$name, $icon, $schedule, $advisor]) {
            Extracurricular::firstOrCreate(['name' => $name], [
                'icon' => $icon,
                'schedule' => $schedule,
                'advisor' => $advisor,
                'is_active' => true,
            ]);
        }
    }

    protected function seedTeachers(): void
    {
        $teachers = [
            ['H. Abdul Aziz, S.Ag., M.Pd.', 'Kepala Sekolah', 'Tahfizh'],
            ['Ust. Ahmad Fauzi, Lc.', 'Wakil Kepala Bidang Kurikulum', 'Al-Qur\'an'],
            ['Rina Marlina, S.Pd.', 'Wakil Kepala Bidang Kesiswaan', 'Bahasa Indonesia'],
            ['Dimas Prasetyo, S.Pd.', 'Guru Matematika', 'Matematika'],
            ['Lia Amalia, S.Pd.', 'Guru Bahasa Inggris', 'Bahasa Inggris'],
            ['Ust. Muhammad Ilham', 'Pembina Tahfizh', 'Tahfizh'],
            ['Taufik Hidayat, S.Kom.', 'Guru TIK & Robotik', 'Informatika'],
            ['Yusuf Ramadhan, S.Or.', 'Guru PJOK', 'PJOK'],
            ['Andi Saputra, S.Pd.', 'Guru IPA', 'IPA'],
            ['Ustd. Hana Yusuf', 'Guru PAI', 'Pendidikan Agama Islam'],
        ];

        foreach ($teachers as [$name, $position, $subject]) {
            Teacher::firstOrCreate(['name' => $name], [
                'position' => $position,
                'subject' => $subject,
                'is_active' => true,
            ]);
        }
    }

    protected function seedAchievements(): void
    {
        $achievements = [
            ['Juara 1 MTQ Tingkat Provinsi Riau', 'Tahfizh', 'Juara 1', 2025],
            ['Juara Umum OSN Matematika Kota Pekanbaru', 'Akademik', 'Juara 1', 2025],
            ['Juara 2 Lomba Debat Bahasa Inggris', 'Akademik', 'Juara 2', 2024],
            ['Juara 1 Futsal Antar SMA Se-Pekanbaru', 'Olahraga', 'Juara 1', 2024],
            ['Medali Emas Kompetisi Robotik Nasional', 'Sains & Teknologi', 'Medali Emas', 2023],
            ['Juara Harapan 1 MHQ Antar Sekolah', 'Tahfizh', 'Harapan 1', 2023],
        ];

        foreach ($achievements as [$title, $category, $rank, $year]) {
            Achievement::firstOrCreate(['title' => $title], [
                'category' => $category,
                'rank' => $rank,
                'year' => $year,
                'is_active' => true,
            ]);
        }
    }

    protected function seedTestimonials(): void
    {
        $testimonials = [
            ['Muhammad Rizki', 'Mahasiswa Kedokteran UI', 2023, 'Alhamdulillah, pembinaan tahfizh dan akademik di Al-Fatih benar-benar membentuk saya. Kini saya hafal 12 juz dan diterima di fakultas kedokteran. Terima kasih Al-Fatih!'],
            ['Aisyah Putri', 'Wali Siswa Kelas X', null, 'Lingkungan sekolah yang Islami, guru yang sabar dan profesional, serta kegiatan yang positif membuat anak kami betah bersekolah. Kemampuan mengaji dan akhlaknya berkembang pesat.'],
            ['Ahmad Fadhil', 'Mahasiswa ITB', 2022, 'Fasilitasnya lengkap dan pembinaannya intensif. Saya sangat terbantu dengan program tahsin dan tahfizh sejak dini. Al-Fatih adalah pilihan terbaik untuk pendidikan anak.'],
        ];

        foreach ($testimonials as [$name, $position, $year, $content]) {
            Testimonial::firstOrCreate(['name' => $name], [
                'position' => $position,
                'alumni_year' => $year,
                'content' => $content,
                'is_active' => true,
            ]);
        }
    }

    protected function seedPartners(): void
    {
        $partners = [
            ['Kemendikbud Ristek', null, 'https://www.kemdikbud.go.id'],
            ['Kementerian Agama RI', null, 'https://www.kemenag.go.id'],
            ['LP Ma\'arif NU', null, null],
            ['Universitas Islam Riau', null, 'https://www.uir.ac.id'],
        ];

        foreach ($partners as [$name, $logo, $website]) {
            Partner::firstOrCreate(['name' => $name], [
                'logo' => $logo,
                'website' => $website,
                'is_active' => true,
            ]);
        }
    }

    protected function seedAnnouncements(): void
    {
        $items = [
            'Pendaftaran PPDB Tahun Ajaran 2026/2027 Telah Dibuka' => 'Pendaftaran Peserta Didik Baru tahun ajaran 2026/2027 telah resmi dibuka. Silakan hubungi panitia melalui WhatsApp resmi sekolah.',
            'Pelaksanaan Ujian Tahfizh Semester Ganjil' => 'Ujian tahfizh semester ganjil akan dilaksanakan pekan depan. Seluruh peserta didik wajib mengikuti sesuai jadwal yang telah ditentukan.',
            'Pembagian Rapor Tengah Semester' => 'Pembagian rapor tengah semester ganjil akan dilaksanakan pada akhir bulan ini. Wali murid diharapkan hadir pada acara tatap muka.',
        ];

        $user = User::query()->first();

        foreach ($items as $title => $content) {
            Announcement::firstOrCreate(['title' => $title], [
                'user_id' => $user?->id,
                'content' => $content,
                'is_published' => true,
                'published_at' => now()->subDays(random_int(1, 20)),
            ]);
        }
    }

    protected function seedNews(): void
    {
        $user = User::query()->first();
        $categories = Category::all()->keyBy('name');

        $items = [
            [
                'title' => 'Pembukaan MPLS Tahun Ajaran 2026/2027 Berlangsung Meriah',
                'category' => 'Kegiatan',
                'excerpt' => 'Ratusan siswa baru mengikuti Masa Pengenalan Lingkungan Sekolah (MPLS) dengan penuh antusias.',
                'content' => "Ratusan peserta didik baru mengikuti Masa Pengenalan Lingkungan Sekolah (MPLS) tahun ajaran 2026/2027 yang berlangsung meriah di lapangan utama. Kegiatan ini dibuka langsung oleh Kepala Sekolah didampingi jajaran dewan guru.\n\nSelama MPLS, para siswa diperkenalkan dengan lingkungan sekolah, budaya tahfizh, tata tertib, serta program-program unggulan. Berbagai games edukatif dan pentas seni juga mewarnai rangkaian kegiatan.\n\nKepala Sekolah berharap para siswa baru dapat beradaptasi dengan cepat dan menjadi bagian dari keluarga besar yang Qur'ani, berprestasi, dan berkarakter.",
            ],
            [
                'title' => 'Santri Al-Fatih Raih Juara 1 MTQ Tingkat Provinsi Riau',
                'category' => 'Prestasi',
                'excerpt' => 'Prestasi membanggakan kembali diukir oleh santri SMA IT Tahfizh Al-Fatih di ajang MTQ tingkat provinsi.',
                'content' => "Kabar membanggakan kembali datang dari siswa SMA IT Tahfizh Al-Fatih. Salah satu santri kami berhasil meraih Juara 1 Musabaqah Tilawatil Qur'an (MTQ) tingkat Provinsi Riau.\n\nKeberhasilan ini merupakan buah dari pembinaan tahfizh yang konsisten dan didukung penuh oleh para pembina. Prestasi ini diharapkan dapat memotivasi santri lain untuk terus meningkatkan kualitas hafalan dan bacaan Al-Qur'an.\n\nSemoga prestasi ini membawa berkah dan mengharumkan nama sekolah serta provinsi Riau di kancah nasional.",
            ],
            [
                'title' => 'Workshop Literasi Digital untuk Siswa dan Guru',
                'category' => 'Akademik',
                'excerpt' => 'Sekolah menggelar workshop literasi digital untuk membekali siswa dan guru menghadapi era teknologi.',
                'content' => "SMA IT Tahfizh Al-Fatih menggelar workshop literasi digital yang diikuti oleh seluruh siswa dan guru. Kegiatan ini bertujuan membekali peserta dengan kecakapan digital yang sehat dan bijak.\n\nMateri yang disampaikan meliputi etika bermedia sosial, keamanan siber, serta pemanfaatan teknologi untuk pembelajaran. Workshop menghadirkan narasumber berpengalaman di bidang teknologi pendidikan.\n\nMelalui kegiatan ini, sekolah berkomitmen mencetak generasi yang cakap teknologi namun tetap berpegang pada nilai-nilai Islami.",
            ],
            [
                'title' => 'Kunjungan Edukasi Santri ke Museum & Perpustakaan Provinsi',
                'category' => 'Kegiatan',
                'excerpt' => 'Para santri mengikuti kunjungan edukasi untuk menambah wawasan sejarah dan budaya.',
                'content' => "Sebanyak 120 santri mengikuti kegiatan kunjungan edukasi ke museum dan perpustakaan daerah. Kegiatan ini merupakan bagian dari program pembelajaran di luar kelas.\n\nPara santri diajak mengenal sejarah dan budaya Melayu Riau serta memperluas wawasan literasi melalui koleksi buku di perpustakaan. Kunjungan dipandu oleh guru pendamping setiap kelas.\n\nKegiatan semacam ini rutin diadakan agar pembelajaran tidak hanya berlangsung di dalam kelas, tetapi juga melalui pengalaman langsung di lapangan.",
            ],
            [
                'title' => 'Sosialisasi PPDB Tahun Ajaran 2026/2027 di Lingkungan Masyarakat',
                'category' => 'PPDB',
                'excerpt' => 'Panitia PPDB melakukan sosialisasi ke berbagai sekolah dan komunitas masyarakat.',
                'content' => "Panitia Penerimaan Peserta Didik Baru (PPDB) SMA IT Tahfizh Al-Fatih tahun ajaran 2026/2027 mulai gencar melakukan sosialisasi ke berbagai sekolah menengah pertama di sekitar Pekanbaru.\n\nSosialisasi bertujuan memperkenalkan program unggulan sekolah, fasilitas, serta keunggulan pendidikan tahfizh yang dimiliki. Masyarakat juga dapat memperoleh informasi melalui WhatsApp resmi dan media sosial sekolah.\n\nKepala Sekolah mengajak seluruh masyarakat untuk segera mendaftarkan putra-putrinya sebelum kuota terpenuhi.",
            ],
            [
                'title' => 'Peringatan Hari Santri Nasional di Lingkungan Sekolah',
                'category' => 'Kegiatan',
                'excerpt' => 'Peringatan Hari Santri diisi dengan berbagai lomba dan doa bersama.',
                'content' => "Seluruh civitas akademika SMA IT Tahfizh Al-Fatih memperingati Hari Santri Nasional dengan penuh khidmat. Rangkaian kegiatan diisi dengan upacara, pembacaan doa, dan berbagai perlombaan.\n\nLomba yang digelar antara lain lomba hafalan surat pendek, kaligrafi, dan cerdas cermat keagamaan. Kegiatan ditutup dengan doa bersama dan pembagian hadiah bagi para pemenang.\n\nPeringatan Hari Santri menjadi momentum untuk meneladani perjuangan para santri dalam menjaga nilai-nilai keislaman dan kebangsaan.",
            ],
        ];

        foreach ($items as $item) {
            News::firstOrCreate(['title' => $item['title']], [
                'category_id' => $categories->get($item['category'])?->id,
                'user_id' => $user?->id,
                'excerpt' => $item['excerpt'],
                'content' => $item['content'],
                'is_published' => true,
                'published_at' => now()->subDays(random_int(1, 60)),
            ]);
        }
    }

    protected function seedAlbums(): void
    {
        $images = [self::IMG_MAIN, self::IMG_LEFT, self::IMG_RIGHT];

        $albums = [
            'Gedung Sekolah' => 'Dokumentasi gedung SMA IT Tahfizh Al-Fatih dari berbagai sisi.',
            'Kegiatan Belajar Mengajar' => 'Suasana pembelajaran di dalam kelas.',
        ];

        $index = 0;
        foreach ($albums as $title => $desc) {
            $album = Album::firstOrCreate(['title' => $title], [
                'description' => $desc,
                'cover' => $images[0],
            ]);

            foreach ($images as $img) {
                $galleryTitle = $title === 'Gedung Sekolah' ? 'Tampak Gedung' : 'Suasana Kelas';
                Gallery::firstOrCreate(
                    ['album_id' => $album->id, 'image' => $img],
                    [
                        'title' => $galleryTitle,
                        'type' => 'photo',
                        'is_active' => true,
                        'sort_order' => ++$index,
                    ]
                );
            }
        }
    }
}
