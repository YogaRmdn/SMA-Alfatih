@php
    /**
     * Sumber tunggal untuk FAQ: teks yang terlihat di halaman DAN JSON-LD yang
     * dikirim ke Google. Keduanya wajib sama, kalau tidak Google menganggap
     * FAQ schema-nya spam.
     *
     * Jawaban sengaja mengambil angka dari settings (alamat, WhatsApp, jam
     * operasional, tahun ajaran, program) supaya tidak ada data yang kacau
     * kalau admin mengubahnya nanti.
     */
    $siteName = $settings['site_name'] ?? config('app.name');
    $city = $settings['meta_geo_placename'] ?? 'Pekanbaru';
    $academicYear = $settings['ppdb_tahun_ajaran'] ?? (now()->year.'/'.(now()->year + 1));
    $address = trim((string) ($settings['address'] ?? ''));
    $waNumber = preg_replace('/\D+/', '', (string) ($settings['whatsapp'] ?? ''));
    $phone = trim((string) ($settings['phone'] ?? ''));
    $hours = $settings['operational_hours'] ?? 'Senin - Jumat: 07.00 - 16.00 WIB';
    $founded = trim((string) ($settings['stat_1_value'] ?? ''));
    $students = trim((string) ($settings['stat_2_value'] ?? ''));
    $teachers = trim((string) ($settings['stat_3_value'] ?? ''));

    $contactSentence = $phone
        ? "telepon {$phone}"
        : 'nomor telepon sekolah';

    if ($waNumber) {
        $contactSentence .= ' atau WhatsApp +'.$waNumber;
    } else {
        $contactSentence .= ' atau WhatsApp sekolah';
    }

    $addressSentence = $address
        ? "Alamat {$siteName} ada di {$address}, Kota {$city}, Riau."
        : "Alamat lengkap {$siteName} di {$city} bisa dikonfirmasi langsung lewat WhatsApp sekolah.";

    // `site_name` sudah mengandung nama kota ("... Pekanbaru"), jadi jangan
    // ditambahkan dua kali. Pengulangan kata kunci seperti ini terbaca spam.
    $brandWithCity = str_contains($siteName, $city) ? $siteName : "{$siteName} {$city}";

    $profileSentence = $siteName.' adalah sekolah Islam terpadu dan tahfizh Al-Quran di '
        .$city.', Riau';

    if ($founded && is_numeric($founded)) {
        $profileSentence .= ', berdiri sejak '.$founded;
    }

    $profileSentence .= '.';

    if ($students || $teachers) {
        $profileSentence .= " Saat ini sekolah memiliki {$students} siswa dan {$teachers} guru serta staff.";
    }

    $programField = app(App\Services\PpdbFormDefinition::class)->fieldByKey('program');
    $programLabels = $programField?->optionList() ?: [];
    $programText = $programLabels
        ? implode(', ', $programLabels)
        : 'program belajar tahfizh dan pembiasaan akhlak';

    $faq = array_values(array_filter([
        [
            'question' => "PPDB {$brandWithCity} tahun ajaran {$academicYear} kapan dibuka?",
            'answer' => "Pendaftaran PPDB {$brandWithCity} untuk tahun ajaran {$academicYear} dibuka secara online lewat halaman PPDB di situs ini. Pendaftar mengisi formulir, mengunggah bukti pembayaran, lalu langsung mendapat No. Registrasi untuk memantau status pendaftaran.",
        ],
        [
            'question' => "Sekolah tahfizh di {$city} yang mana yang cocok untuk anak saya?",
            'answer' => $profileSentence.' Penekanan pembelajarannya adalah hafalan Al-Quran, pembiasaan ibadah, dan akhlak yang berjalan beriringan dengan penguatan akademik.',
        ],
        [
            'question' => "Berapa biaya sekolah di {$brandWithCity}?",
            'answer' => "Biaya {$siteName} tahun ajaran {$academicYear} menyesuaikan program yang dipilih, yaitu {$programText}. Rinciannya mencakup SPP, uang pangkal, seragam, dan biaya kegiatan. Untuk angka pastinya serta promo pendaftaran, hubungi {$contactSentence} agar panitia bisa memberikan rincian resmi.",
        ],
        [
            'question' => "Di mana lokasi {$siteName}?",
            'answer' => $addressSentence,
        ],
        [
            'question' => "Program belajar apa saja yang tersedia di {$siteName}?",
            'answer' => "{$siteName} menyediakan {$programText} di {$city}. Program ini bisa dipilih langsung saat mengisi formulir pendaftaran PPDB online.",
        ],
        [
            'question' => "Jam belajar dan operasional {$siteName} kapan?",
            'answer' => "Jam operasional {$siteName} adalah {$hours}. Peserta program Full Day belajar di sekolah pada jam tersebut, sedangkan peserta program Boarding tinggal di asrama yang disediakan sekolah.",
        ],
        [
            'question' => "Bagaimana cara mendaftar PPDB di {$brandWithCity}?",
            'answer' => "Cara mendaftar PPDB {$siteName} dilakukan sepenuhnya online: buka halaman PPDB, isi data calon siswa, pilih program, unggah bukti pembayaran, lalu simpan No. Registrasi yang muncul. Pendaftaran juga bisa dibantu secara langsung melalui {$contactSentence}.",
        ],
    ], fn ($item) => filled($item['question'] ?? null) && filled($item['answer'] ?? null)));
@endphp

@push('head')
    {!! faq_schema($faq) !!}
@endpush

@if (filled($faq))
    <section id="faq" class="relative scroll-mt-24 overflow-hidden bg-[linear-gradient(120deg,#022c1c,#065f46,#0f766e,#134e4a,#1a3a2e)] py-16 lg:py-24">
        <div class="aurora -left-16 top-10 h-72 w-72 bg-amber-400/20"></div>
        <div class="aurora bottom-0 right-0 h-80 w-80 bg-teal-300/20" style="animation-delay:-11s"></div>

        <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
            <x-reveal>
                <div class="mx-auto max-w-2xl text-center">
                    <span class="text-xs font-bold uppercase tracking-widest text-amber-400">{{ $settings['faq_eyebrow'] ?? 'Pertanyaan Umum' }}</span>
                    <h2 class="mt-2 text-3xl font-extrabold text-white lg:text-4xl">{{ $settings['faq_title'] ?? 'Pertanyaan yang Sering Ditanya Orang Tua' }}</h2>
                    <p class="mt-4 text-sm leading-relaxed text-emerald-100/90">
                        Belum menemukan jawabannya? Hubungi panitia sekolah lewat {{ $contactSentence }}.
                    </p>
                </div>
            </x-reveal>

            <div class="mx-auto mt-12 max-w-3xl space-y-4">
                @foreach ($faq as $item)
                    <x-reveal :delay="$loop->index * 80">
                        <details class="group rounded-2xl border border-white/15 bg-white/10 backdrop-blur transition open:border-amber-400/50 open:bg-white/15">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-5 text-left">
                                <h3 class="text-sm font-bold text-white sm:text-base">{{ $item['question'] }}</h3>
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-amber-400/20 text-amber-300 transition group-open:rotate-45">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14" /></svg>
                                </span>
                            </summary>
                            <div class="border-t border-white/10 px-6 py-5">
                                <p class="text-sm leading-relaxed text-emerald-50/90">{{ $item['answer'] }}</p>
                            </div>
                        </details>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>
@endif
