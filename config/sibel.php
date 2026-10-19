<?php

/*
|--------------------------------------------------------------------------
| SIBEL — Sistem Informasi Gaya Belajar Siswa
|--------------------------------------------------------------------------
| Konfigurasi pemetaan kategori gaya belajar.
|
| PENTING (Tahap 1 — Analisis Kebutuhan): pemetaan opsi A/B/C di bawah ini
| adalah KONFIGURASI AWAL sesuai rancangan dan BELUM diverifikasi terhadap
| dokumen kuesioner asli. Jangan mengubah bunyi pertanyaan atau pilihan
| jawaban tanpa persetujuan. Setelah diverifikasi, tandai kuesioner sebagai
| "Terverifikasi" lewat panel admin.
*/

return [

    /*
    | Kategori hasil. Kunci array dipakai sebagai nilai pada kolom
    | `results.dominant_categories` dan `options.category_code`.
    */
    'categories' => [
        'visual' => [
            'code' => 'A',
            'label' => 'Visual',
            'score' => 'score_a',
            'percentage' => 'percentage_a',
            'hex' => '#0284c7',
            'description' => 'Siswa cenderung terbantu oleh informasi visual seperti gambar, diagram, peta konsep, dan catatan terstruktur.',
            'strategies' => [
                'Gunakan peta konsep dan diagram untuk merangkai materi baru.',
                'Buat ringkasan materi yang terstruktur dengan poin-poin jelas.',
                'Tandai gagasan utama pada catatan belajar menggunakan warna atau sorotan.',
                'Manfaatkan ilustrasi, gambar, dan contoh visual.',
            ],
        ],
        'auditori' => [
            'code' => 'B',
            'label' => 'Auditori',
            'score' => 'score_b',
            'percentage' => 'percentage_b',
            'hex' => '#7c3aed',
            'description' => 'Siswa cenderung terbantu oleh penjelasan lisan, diskusi, mendengarkan, dan menjelaskan kembali materi.',
            'strategies' => [
                'Gunakan diskusi dan tanya jawab saat mempelajari materi.',
                'Jelaskan materi dengan kata-kata sendiri untuk menguji pemahaman.',
                'Gunakan latihan mengingat melalui penjelasan lisan atau cerita.',
                'Dengarkan penjelasan atau materi audio bila tersedia.',
            ],
        ],
        'kinestetik' => [
            'code' => 'C',
            'label' => 'Kinestetik',
            'score' => 'score_c',
            'percentage' => 'percentage_c',
            'hex' => '#059669',
            'description' => 'Siswa cenderung terbantu oleh praktik, simulasi, eksperimen, aktivitas langsung, dan penggunaan benda konkret.',
            'strategies' => [
                'Gunakan praktik langsung dan eksperimen untuk memahami konsep.',
                'Manfaatkan simulasi, alat peraga, atau model fisik bila ada.',
                'Kerjakan latihan berbasis aktivitas, bukan hanya membaca.',
                'Bagi waktu belajar menjadi sesi singkat dengan jeda gerak.',
            ],
        ],
    ],

    /* Pemetaan kode opsi -> kategori (fallback bila `category_code` kosong). */
    'code_category' => [
        'A' => 'visual',
        'B' => 'auditori',
        'C' => 'kinestetik',
    ],

    /* Label untuk hasil seri. */
    'tie_label' => 'Seri',

    /* Catatan etika yang selalu ditampilkan pada laporan. */
    'ethics_note' => 'Hasil ini merupakan ringkasan kecenderungan berdasarkan jawaban kuesioner, bukan diagnosis psikologis. Kategori digunakan untuk refleksi dan eksplorasi strategi belajar — bukan label permanen atau pembatas kemampuan siswa. Guru BK tetap berperan dalam interpretasi, pendampingan, dan tindak lanjut.',
];
