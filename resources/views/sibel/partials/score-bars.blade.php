@php
    /**
     * Grafik batang skor (dirender server — selalu tampil, termasuk saat
     * dicetak atau bila Chart.js tidak termuat). Dipakai di halaman hasil
     * siswa, detail hasil guru BK, dan cetak laporan.
     *
     * Variabel: $result (App\Models\SibelResult), opsional $compact (bool).
     */
    $categories = config('sibel.categories');
    $maxScore = max(1, ...array_map(fn ($c) => $result->scoreOf($c), array_keys($categories)));
    $totalAnswers = $result->score_a + $result->score_b + $result->score_c;

    // Kuesioner Likert: tampilkan skor terhadap skor maksimum tiap kategori
    // (jumlah pernyataan × nilai maksimum opsi skala).
    $questionnaire = $result->attempt?->questionnaire;
    $isLikert = $questionnaire?->isLikert() ?? false;
    $categoryMax = fn (string $key) => $questionnaire?->maxLikertScore($key) ?: 0;
@endphp

<div class="space-y-3">
    @foreach ($categories as $key => $category)
        @php
            $score = $result->scoreOf($key);
            $percentage = $result->percentageOf($key);
            $isDominant = in_array($key, $result->dominant_categories ?? [], true);
            $maxForBar = $isLikert ? max(1, $categoryMax($key)) : $maxScore;
        @endphp
        <div class="flex items-center gap-3">
            <div class="w-24 shrink-0 text-sm font-semibold text-slate-700">
                {{ $category['label'] }}
                @if ($isDominant)
                    <span class="ml-1 align-middle text-[10px] font-bold uppercase text-emerald-600">dominan</span>
                @endif
            </div>
            <div class="h-4 flex-1 overflow-hidden rounded-full bg-slate-200">
                <div class="h-full rounded-full transition-all duration-500"
                     style="width: {{ min(100, ($score / $maxForBar) * 100) }}%; background: {{ $category['hex'] }}"></div>
            </div>
            <div class="w-40 shrink-0 text-right text-sm text-slate-600">
                @if ($isLikert)
                    <span class="font-bold text-slate-800">{{ $score }}</span>/{{ $categoryMax($key) }} poin
                @else
                    <span class="font-bold text-slate-800">{{ $score }}</span> jawaban
                @endif
                <span class="text-slate-400">•</span>
                {{ rtrim(rtrim(number_format($percentage, 1, ',', '.'), '0'), ',') }}%
            </div>
        </div>
    @endforeach

    @if (!($compact ?? false))
        <p class="pt-1 text-xs text-slate-500">
            @if ($isLikert)
                Total poin: <span class="font-semibold text-slate-700">{{ $totalAnswers }}</span>.
                Persentase = (jumlah poin kategori / skor maksimum kategori) &times; 100%,
                dengan skor maksimum = jumlah pernyataan kategori &times; nilai opsi tertinggi.
            @else
                Total jawaban valid: <span class="font-semibold text-slate-700">{{ $totalAnswers }}</span>.
                Persentase = (jumlah jawaban kategori / total jawaban valid) &times; 100%.
            @endif
        </p>
    @endif
</div>
