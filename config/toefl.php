<?php

/*
|--------------------------------------------------------------------------
| TOEFL ITP Level 1 — Sistem Ujian Online
|--------------------------------------------------------------------------
| Konfigurasi modul TOEFL ITP Level 1 (latihan ujian online).
|
| Durasi & jumlah pertanyaan tiap bagian bersifat KONFIGURABLE (bisa diubah
| per ujian lewat panel admin). Nilai di bawah adalah default baku TOEFL ITP
| Level 1 yang dipakai saat membuat ujian baru.
|
| CATATAN: skor scaled resmi TOEFL ITP memakai tabel konversi resmi ETS.
| Nilai di sini adalah aproksimasi linear untuk keperluan latihan — beri
| tanda "latihan" pada laporan.
*/

return [

    /*
    | Bagian ujian (section). Kunci array dipakai sebagai nilai kolom
    | `sections.section_type`. Urutan mengikuti `order`.
    */
    'sections' => [
        'listening' => [
            'label' => 'Listening Comprehension',
            'description' => 'Dengarkan rekaman lalu pilih jawaban yang tepat.',
            'default_questions' => 50,
            'default_duration' => 2100, // 35 menit
            'order' => 1,
        ],
        'structure' => [
            'label' => 'Structure and Written Expression',
            'description' => 'Lengkapi kalimat dan temukan kesalahan pada kalimat.',
            'default_questions' => 40,
            'default_duration' => 1500, // 25 menit
            'order' => 2,
        ],
        'reading' => [
            'label' => 'Reading Comprehension',
            'description' => 'Baca bacaan lalu jawab pertanyaan pemahaman isinya.',
            'default_questions' => 50,
            'default_duration' => 3300, // 55 menit
            'order' => 3,
        ],
    ],

    /* Jenis pertanyaan (semua pilihan ganda; label untuk kebutuhan soal). */
    'question_types' => [
        'multiple_choice' => 'Pilihan Ganda',
        'sentence_completion' => 'Kelengkapan Kalimat',
        'error_identification' => 'Identifikasi Kesalahan',
        'main_idea' => 'Ide Utama',
        'supporting_detail' => 'Rincian Pendukung',
        'vocabulary' => 'Kosakata',
        'inference' => 'Inferensi',
        'reference' => 'Referensi',
    ],

    /* Kode pilihan jawaban yang valid. */
    'answer_options' => ['A', 'B', 'C', 'D'],

    /* Batas pemutaran audio per aset per percobaan (aturan latihan). */
    'audio' => [
        'max_plays' => 2,
        'disk' => 'local', // disk privat — file TIDAK boleh berada di public/
        'directory' => 'toefl/audio',
    ],

    /*
    | Timer dihitung di server (`deadline_at` pada attempt). Grace period
    | adalah toleransi keterlambatan jaringan sebelum attempt otomatis
    | dinilai sebagai kedaluwarsa.
    */
    'timer' => [
        'grace_seconds' => 30,
    ],

    /* Konversi skor scaled (aproksimasi linear — tabel resmi ETS berbeda). */
    'scoring' => [
        'min_scaled' => 310,
        'max_scaled' => 680,
    ],

    /* Status ujian. */
    'exam_statuses' => [
        'draft' => 'Draf',
        'published' => 'Dibuka',
        'closed' => 'Ditutup',
    ],

    /* Status percobaan (attempt). */
    'attempt_statuses' => [
        'in_progress' => 'Sedang Berlangsung',
        'submitted' => 'Selesai',
        'expired' => 'Waktu Habis',
    ],

    /* Jenis peristiwa pemantauan browser (bukan bukti kecurangan mutlak). */
    'event_types' => [
        'tab_hidden' => 'Tab/berpindah aplikasi',
        'tab_visible' => 'Kembali ke tab ujian',
        'fullscreen_exit' => 'Keluar dari mode layar penuh',
        'fullscreen_enter' => 'Masuk mode layar penuh',
        'section_enter' => 'Masuk bagian ujian',
        'section_leave' => 'Pindah bagian ujian',
    ],

    /* Instruksi ujian yang ditampilkan sebelum memulai. */
    'instructions' => [
        'Ujian terdiri dari tiga bagian: Listening, Structure, dan Reading.',
        'Waktu dihitung di server dan tidak dapat diubah. Saat waktu habis, ujian otomatis dikumpulkan.',
        'Pada bagian Listening, setiap audio hanya boleh diputar maksimal {audio_limit} kali.',
        'Jawaban tersimpan otomatis setiap kali Anda memilih sebuah pilihan.',
        'Jangan membuka tab atau aplikasi lain selama ujian berlangsung — aktivitas tercatat.',
        'Pastikan koneksi internet stabil sebelum memulai. Bila terputus, kembali lagi dengan kode yang sama.',
    ],

    /* Catatan etika yang selalu ditampilkan pada laporan hasil. */
    'ethics_note' => 'Hasil ini merupakan hasil ujian latihan TOEFL ITP Level 1. Skor scaled adalah aproksimasi dan BUKAN skor resmi ETS — gunakan hanya untuk latihan dan pemetaan kemampuan. Website biasa tidak dapat menjamin siswa tidak membuka aplikasi lain atau memakai perangkat kedua; pemantauan tab/layar penuh hanya catatan pendukung, bukan bukti kecurangan.',
];
