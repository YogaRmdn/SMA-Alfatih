<?php

namespace App\Exports;

use App\Models\Setting;
use App\Models\SibelAttempt;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\AutoFilter;
use OpenSpout\Writer\Common\Entity\Sheet;
use OpenSpout\Writer\XLSX\Entity\SheetView;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Unduhan hasil SIBEL (XLSX) dengan desain profesional — 3 sheet:
 *
 *  1. "Rekap Hasil"     : identitas + skor/persentase VAK + dominan per siswa.
 *  2. "Distribusi Kelas": sebaran kategori dominan per kelas (+ total).
 *  3. "Detail Jawaban"  : jawaban tiap butir per siswa (nilai Likert bila ada).
 *
 * Tata letak tiap sheet: kop laporan (nama sekolah, judul, meta cetak/filter),
 * baris header emerald, border tipis, selang-seling (zebra), lebar kolom,
 * freeze baris header, autofilter, dan judul baris untuk cetak.
 *
 * Penulisan memakai OpenSpout (murni XLSX, tanpa dependensi ekstensi gd).
 * Catatan: baris XLSX harus ditulis berurutan — nomor baris di setiap
 * method hanya untuk koordinat merge/freeze, bukan posisi tulis.
 */
class SibelResultsExport
{
    /*
    | Palet warna laporan (selaras dengan nuansa emerald/slate panel SIBEL).
    */
    protected const COLORS = [
        'brand' => '047857',      // emerald-700 — kop & header tabel
        'brand_dark' => '064E3B', // emerald-900 — judul
        'brand_soft' => 'ECFDF5', // emerald-50 — baris total
        'ink' => '0F172A',        // slate-900 — teks judul
        'muted' => '64748B',      // slate-500 — teks meta
        'line' => 'CBD5E1',       // slate-300 — border
        'zebra' => 'F8FAFC',      // slate-50 — baris selang-seling
        'white' => 'FFFFFF',
        'seri' => '6D28D9',       // violet-700 — penanda hasil seri
    ];

    protected Writer $writer;

    protected int $sheetIndex = 0;

    /**
     * @param  Collection<int, SibelAttempt>  $attempts  sesi selesai terakhir per siswa
     * @param  array<int, string>  $metaLines  baris filter/informasi pada kop laporan
     */
    public function __construct(
        protected Collection $attempts,
        protected array $metaLines = [],
    ) {}

    /**
     * Bangun file ke path sementara, lalu kirim sebagai unduhan.
     */
    public function download(string $filename): BinaryFileResponse
    {
        $dir = is_dir('/tmp/opencode') ? '/tmp/opencode' : sys_get_temp_dir();
        $path = $dir.'/sibel-export-'.Str::random(10).'.xlsx';

        $this->build($path);

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Susun seluruh workbook (3 sheet) ke file XLSX.
     */
    public function build(string $path): void
    {
        $this->writer = new Writer;
        $this->writer->openToFile($path);

        $this->attempts->loadMissing([
            'result',
            'student.schoolClass',
            'questionnaire',
            'answers.question',
            'answers.option',
        ]);

        $this->sheetIndex = 0;
        $recapSheet = $this->writer->getCurrentSheet();
        $recapSheet->setName('Rekap Hasil');
        $this->buildRecapSheet($recapSheet);

        $this->sheetIndex = 1;
        $distributionSheet = $this->writer->addNewSheetAndMakeItCurrent();
        $distributionSheet->setName('Distribusi Kelas');
        $this->buildDistributionSheet($distributionSheet);

        $this->sheetIndex = 2;
        $detailSheet = $this->writer->addNewSheetAndMakeItCurrent();
        $detailSheet->setName('Detail Jawaban');
        $this->buildDetailSheet($detailSheet);

        $this->writer->close();
    }

    /*
    |--------------------------------------------------------------------------
    | Sheet 1 — Rekap Hasil
    |--------------------------------------------------------------------------
    */

    protected function buildRecapSheet(Sheet $sheet): void
    {
        $headers = [
            'No', 'Tanggal', 'No. Induk', 'Nama Siswa', 'Kelas',
            'Skor Visual', 'Skor Auditori', 'Skor Kinestetik',
            'Visual (%)', 'Auditori (%)', 'Kinestetik (%)',
            'Kategori Dominan', 'Status', 'Catatan Guru BK',
        ];
        $widths = [5, 12, 12, 30, 10, 10, 10, 10, 10, 10, 10, 18, 16, 34];

        $isLikert = $this->attempts->first()?->questionnaire?->isLikert() ?? false;

        $row = $this->writeTitleBlock(
            $sheet,
            count($headers),
            'Laporan Hasil Penilaian Gaya Belajar (SIBEL)',
            $isLikert
                ? 'Metode penilaian: Skala Likert 4 tingkat — skor = jumlah poin per kategori; persentase = skor / skor maksimum.'
                : 'Metode penilaian: Pilihan Tunggal A/B/C — skor = jumlah jawaban per kategori.'
        );

        $headerRow = $row;
        $row = $this->writeTableHeader($sheet, $row, $headers);

        $firstDataRow = $row;
        $totals = ['a' => 0, 'b' => 0, 'c' => 0];
        $written = 0;

        foreach ($this->attempts->values() as $index => $attempt) {
            $result = $attempt->result;
            $student = $attempt->student;

            if ($result === null) {
                continue;
            }

            $totals['a'] += $result->score_a;
            $totals['b'] += $result->score_b;
            $totals['c'] += $result->score_c;

            $isTie = $result->isTie();

            $this->sheetRow($sheet, [
                Cell::fromValue($index + 1, $this->bodyStyle(align: CellAlignment::CENTER)),
                Cell::fromValue($attempt->completed_at?->format('d/m/Y') ?? '—', $this->bodyStyle(align: CellAlignment::CENTER)),
                Cell::fromValue($student?->student_number ?? '—', $this->bodyStyle(align: CellAlignment::CENTER)),
                Cell::fromValue($student?->full_name ?? 'Siswa terhapus', $this->bodyStyle(bold: true)),
                Cell::fromValue($student?->schoolClass?->name ?? '—', $this->bodyStyle(align: CellAlignment::CENTER)),
                Cell::fromValue($result->score_a, $this->bodyStyle(align: CellAlignment::CENTER)),
                Cell::fromValue($result->score_b, $this->bodyStyle(align: CellAlignment::CENTER)),
                Cell::fromValue($result->score_c, $this->bodyStyle(align: CellAlignment::CENTER)),
                Cell::fromValue($result->percentage_a, $this->bodyStyle(align: CellAlignment::CENTER)),
                Cell::fromValue($result->percentage_b, $this->bodyStyle(align: CellAlignment::CENTER)),
                Cell::fromValue($result->percentage_c, $this->bodyStyle(align: CellAlignment::CENTER)),
                Cell::fromValue(
                    $isTie ? 'Seri: '.$result->dominantLabel() : $result->dominantLabel(),
                    $this->bodyStyle(align: CellAlignment::CENTER, bold: true, fontColor: $isTie ? self::COLORS['seri'] : null)
                ),
                Cell::fromValue($result->is_published ? 'Dipublikasikan' : 'Belum dipublikasikan', $this->bodyStyle(align: CellAlignment::CENTER)),
                Cell::fromValue($result->teacher_note ?? '—', $this->bodyStyle(wrap: true)),
            ], zebra: $written % 2 === 1);

            $written++;
            $row++;
        }

        $lastDataRow = max($firstDataRow, $row - 1);

        // Baris total (bila ada data).
        if ($written > 0) {
            $this->sheetRow($sheet, [
                Cell::fromValue(null, $this->totalStyle()),
                Cell::fromValue('JUMLAH', $this->totalStyle()),
                Cell::fromValue($written.' siswa', $this->totalStyle()),
                Cell::fromValue(null, $this->totalStyle()),
                Cell::fromValue(null, $this->totalStyle()),
                Cell::fromValue($totals['a'], $this->totalStyle(align: CellAlignment::CENTER)),
                Cell::fromValue($totals['b'], $this->totalStyle(align: CellAlignment::CENTER)),
                Cell::fromValue($totals['c'], $this->totalStyle(align: CellAlignment::CENTER)),
                Cell::fromValue(null, $this->totalStyle()),
                Cell::fromValue(null, $this->totalStyle()),
                Cell::fromValue(null, $this->totalStyle()),
                Cell::fromValue(null, $this->totalStyle()),
                Cell::fromValue(null, $this->totalStyle()),
                Cell::fromValue(null, $this->totalStyle()),
            ]);
        } else {
            $this->writeEmptyRow($sheet, count($headers), 'Belum ada hasil untuk diekspor.');
        }

        $this->finalizeSheet($sheet, count($headers), $widths, $headerRow, $lastDataRow, 'A:N');
    }

    /*
    |--------------------------------------------------------------------------
    | Sheet 2 — Distribusi Kelas
    |--------------------------------------------------------------------------
    */

    protected function buildDistributionSheet(Sheet $sheet): void
    {
        $categoryKeys = array_keys(config('sibel.categories'));

        $headers = ['Kelas', 'Jumlah Siswa'];
        foreach ($categoryKeys as $key) {
            $label = config("sibel.categories.{$key}.label");
            $headers[] = $label.' (n)';
            $headers[] = $label.' (%)';
        }
        $headers[] = 'Seri (n)';
        $headers[] = 'Seri (%)';

        $widths = [16, 12, 11, 11, 11, 11, 11, 11, 9, 9];

        $row = $this->writeTitleBlock($sheet, count($headers), 'Distribusi Kategori Dominan per Kelas');
        $headerRow = $row;
        $row = $this->writeTableHeader($sheet, $row, $headers);

        $firstDataRow = $row;

        // Kelompokkan per kelas; hitung dominan (seri dihitung terpisah).
        $grouped = $this->attempts->groupBy(
            fn (SibelAttempt $attempt) => $attempt->student?->schoolClass?->name ?? 'Tanpa Kelas'
        )->sortKeys();

        $grandTotal = array_fill_keys($categoryKeys, 0);
        $grandTies = 0;
        $grandCount = 0;
        $written = 0;

        foreach ($grouped as $className => $attempts) {
            $counts = array_fill_keys($categoryKeys, 0);
            $ties = 0;

            foreach ($attempts as $attempt) {
                $result = $attempt->result;
                $dominant = $result?->dominant_categories ?? [];

                if ($result === null || $result->isTie() || count($dominant) !== 1) {
                    $ties++;

                    continue;
                }

                if (array_key_exists($dominant[0], $counts)) {
                    $counts[$dominant[0]]++;
                }
            }

            $count = $attempts->count();
            $grandCount += $count;
            $grandTies += $ties;
            foreach ($categoryKeys as $key) {
                $grandTotal[$key] += $counts[$key];
            }

            $cells = [
                Cell::fromValue($className, $this->bodyStyle(bold: true)),
                Cell::fromValue($count, $this->bodyStyle(align: CellAlignment::CENTER)),
            ];

            foreach ($categoryKeys as $key) {
                $pct = $count > 0 ? round(($counts[$key] / $count) * 100, 1) : 0.0;
                $cells[] = Cell::fromValue($counts[$key], $this->bodyStyle(align: CellAlignment::CENTER));
                $cells[] = Cell::fromValue($pct, $this->bodyStyle(align: CellAlignment::CENTER));
            }

            $cells[] = Cell::fromValue($ties, $this->bodyStyle(align: CellAlignment::CENTER, fontColor: $ties > 0 ? self::COLORS['seri'] : null));
            $cells[] = Cell::fromValue($count > 0 ? round(($ties / $count) * 100, 1) : 0.0, $this->bodyStyle(align: CellAlignment::CENTER));

            $this->sheetRow($sheet, $cells, zebra: $written % 2 === 1);

            $written++;
            $row++;
        }

        $lastDataRow = max($firstDataRow, $row - 1);

        if ($written === 0) {
            $this->writeEmptyRow($sheet, count($headers), 'Belum ada data hasil untuk ditampilkan.');
        } else {
            $cells = [
                Cell::fromValue('TOTAL SEKOLAH', $this->totalStyle()),
                Cell::fromValue($grandCount, $this->totalStyle(align: CellAlignment::CENTER)),
            ];
            foreach ($categoryKeys as $key) {
                $pct = $grandCount > 0 ? round(($grandTotal[$key] / $grandCount) * 100, 1) : 0.0;
                $cells[] = Cell::fromValue($grandTotal[$key], $this->totalStyle(align: CellAlignment::CENTER));
                $cells[] = Cell::fromValue($pct, $this->totalStyle(align: CellAlignment::CENTER));
            }
            $cells[] = Cell::fromValue($grandTies, $this->totalStyle(align: CellAlignment::CENTER));
            $cells[] = Cell::fromValue($grandCount > 0 ? round(($grandTies / $grandCount) * 100, 1) : 0.0, $this->totalStyle(align: CellAlignment::CENTER));

            $this->sheetRow($sheet, $cells);
        }

        $this->finalizeSheet($sheet, count($headers), $widths, $headerRow, $lastDataRow);
    }

    /*
    |--------------------------------------------------------------------------
    | Sheet 3 — Detail Jawaban
    |--------------------------------------------------------------------------
    */

    protected function buildDetailSheet(Sheet $sheet): void
    {
        $headers = ['No', 'Nama Siswa', 'Kelas', 'No. Butir', 'Pernyataan / Pertanyaan', 'Jawaban', 'Nilai', 'Kategori'];
        $widths = [5, 28, 10, 9, 60, 26, 7, 14];

        $row = $this->writeTitleBlock(
            $sheet,
            count($headers),
            'Detail Jawaban Tiap Butir per Siswa',
            'Kolom "Nilai" terisi pada kuesioner Skala Likert (1-4); pada pilihan tunggal ditampilkan "-".'
        );
        $headerRow = $row;
        $row = $this->writeTableHeader($sheet, $row, $headers);

        $firstDataRow = $row;
        $number = 0;
        $studentGroup = -1;

        foreach ($this->attempts->values() as $attempt) {
            $answers = $attempt->answers
                ->sortBy(fn ($answer) => $answer->question?->order_number ?? PHP_INT_MAX)
                ->values();

            foreach ($answers as $answer) {
                $studentGroup++;
                $number++;

                $nilai = $answer->option?->value;
                $isLikert = $answer->question?->category_code !== null;
                $category = $isLikert
                    ? ($answer->question?->category_code ?? null)
                    : $answer->option?->category_code;

                $this->sheetRow($sheet, [
                    Cell::fromValue($number, $this->bodyStyle(align: CellAlignment::CENTER)),
                    Cell::fromValue($attempt->student?->full_name ?? 'Siswa terhapus', $this->bodyStyle(bold: true)),
                    Cell::fromValue($attempt->student?->schoolClass?->name ?? '—', $this->bodyStyle(align: CellAlignment::CENTER)),
                    Cell::fromValue($answer->question?->order_number ?? '—', $this->bodyStyle(align: CellAlignment::CENTER)),
                    Cell::fromValue($answer->question?->question_text ?? '—', $this->bodyStyle(wrap: true)),
                    Cell::fromValue($answer->option?->option_text ?? '—', $this->bodyStyle(wrap: true)),
                    Cell::fromValue($nilai ?? '—', $this->bodyStyle(align: CellAlignment::CENTER, bold: $nilai !== null)),
                    Cell::fromValue($category !== null ? (config("sibel.categories.{$category}.label") ?? ucfirst($category)) : '—', $this->bodyStyle(align: CellAlignment::CENTER)),
                ], zebra: $studentGroup % 2 === 1);

                $row++;
            }
        }

        $lastDataRow = max($firstDataRow, $row - 1);

        if ($number === 0) {
            $this->writeEmptyRow($sheet, count($headers), 'Belum ada jawaban untuk ditampilkan.');
        }

        $this->finalizeSheet($sheet, count($headers), $widths, $headerRow, $lastDataRow, 'A:H');
    }

    /*
    |--------------------------------------------------------------------------
    | Bantuan tata letak & gaya
    |--------------------------------------------------------------------------
    */

    /**
     * Kop laporan: nama sekolah, judul laporan, meta (filter & waktu cetak),
     * baris catatan metode (opsional), lalu satu baris jarak.
     * Mengembalikan nomor baris pertama untuk tabel (baris header).
     */
    protected function writeTitleBlock(Sheet $sheet, int $columnCount, string $reportTitle, ?string $footnote = null): int
    {
        $siteName = (string) Setting::get('site_name', 'SMA IT Tahfizh Al-Fatih');
        $lastCol = max($columnCount, 2) - 1;

        // 1) Nama sekolah.
        $this->sheetRow($sheet, [Cell::fromValue(strtoupper($siteName), $this->headingStyle(size: 15, color: self::COLORS['brand_dark']))]);
        $this->mergeRow($sheet, 1, $lastCol);

        // 2) Judul laporan.
        $this->sheetRow($sheet, [Cell::fromValue($reportTitle, $this->headingStyle(size: 12, color: self::COLORS['ink']))]);
        $this->mergeRow($sheet, 2, $lastCol);

        // 3) Meta: filter aktif + waktu cetak + operator.
        $meta = implode('  •  ', array_merge($this->metaLines, [
            'Dicetak '.now()->format('d/m/Y H:i').' WIB oleh '.(auth()->user()?->name ?? 'Sistem'),
        ]));
        $this->sheetRow($sheet, [Cell::fromValue($meta, $this->headingStyle(size: 9, color: self::COLORS['muted'], italic: true))]);
        $this->mergeRow($sheet, 3, $lastCol);

        $row = 4;

        // 4) Catatan metode penilaian (opsional).
        if ($footnote !== null) {
            $this->sheetRow($sheet, [Cell::fromValue($footnote, $this->headingStyle(size: 9, color: self::COLORS['muted'], italic: true, align: CellAlignment::LEFT))]);
            $this->mergeRow($sheet, 4, $lastCol);
            $row++;
        }

        // Baris jarak.
        $this->sheetRow($sheet, [Cell::fromValue(null, new Style)]);
        $row++;

        return $row;
    }

    /**
     * Baris header tabel: sel emerald putih, tebal, wrap, border.
     *
     * @param  array<int, string>  $headers
     * @return int nomor baris berikutnya (baris data pertama)
     */
    protected function writeTableHeader(Sheet $sheet, int $row, array $headers): int
    {
        $this->sheetRow($sheet, array_map(
            fn (string $header) => Cell::fromValue($header, $this->headerStyle()),
            $headers
        ));

        return $row + 1;
    }

    /**
     * Baris pesan "data kosong" (digabung melebar) — hanya dipanggil bila
     * tidak ada baris data yang ditulis.
     */
    protected function writeEmptyRow(Sheet $sheet, int $columnCount, string $message): void
    {
        $this->sheetRow($sheet, [
            Cell::fromValue($message, $this->bodyStyle(align: CellAlignment::CENTER, italic: true, fontColor: self::COLORS['muted'])),
        ]);
        $this->mergeRow($sheet, $sheet->getWrittenRowCount(), max($columnCount - 1, 1));
    }

    /**
     * Penyelesaian sheet: lebar kolom, freeze sampai baris header,
     * autofilter tabel, gridlines mati, dan judul baris untuk cetak.
     *
     * @param  array<int, float>  $widths
     */
    protected function finalizeSheet(Sheet $sheet, int $columnCount, array $widths, int $headerRow, int $lastRow, ?string $printRange = null): void
    {
        foreach ($widths as $index => $width) {
            $sheet->setColumnWidth($width, $index);
        }

        $sheet->setSheetView(
            (new SheetView)
                ->setFreezeRow($headerRow)
                ->setShowGridLines(false)
        );

        if ($lastRow >= $headerRow) {
            $sheet->setAutoFilter(new AutoFilter(0, $headerRow, $columnCount - 1, $lastRow));
        }

        if ($printRange !== null) {
            $sheet->setPrintTitleRows($headerRow.':'.$headerRow);
        }
    }

    /**
     * Tulis satu baris ke sheet aktif (berurutan) — seluruh sel memakai
     * border tipis; baris zebra diberi latar slate-50.
     *
     * @param  array<int, Cell>  $cells
     */
    protected function sheetRow(Sheet $sheet, array $cells, bool $zebra = false): void
    {
        if ($zebra) {
            $cells = array_map(function (Cell $cell) {
                $style = clone $cell->getStyle();
                $style->setBackgroundColor(Color::toARGB(self::COLORS['zebra']));

                return Cell::fromValue($cell->getValue(), $style);
            }, $cells);
        }

        // Border menyelimuti seluruh sel, termasuk sel kosong di judul.
        $cells = array_map(function (Cell $cell) {
            if ($cell->getStyle()->getBorder() === null) {
                $style = clone $cell->getStyle();
                $style->setBorder($this->border());

                return Cell::fromValue($cell->getValue(), $style);
            }

            return $cell;
        }, $cells);

        // Baris ditulis ke sheet yang sedang aktif (satu per satu, berurutan).
        $this->writer->addRow(new Row($cells));
    }

    /**
     * Gabungkan seluruh kolom pada baris terakhir yang ditulis.
     */
    protected function mergeRow(Sheet $sheet, int $rowNumber, int $lastColumn): void
    {
        $this->writer->getOptions()->mergeCells(0, $rowNumber, $lastColumn, $rowNumber, $this->sheetIndex);
    }

    /*
    |--------------------------------------------------------------------------
    | Gaya sel
    |--------------------------------------------------------------------------
    */

    protected function headingStyle(
        int $size = 12,
        string $color = self::COLORS['ink'],
        bool $italic = false,
        string $align = CellAlignment::CENTER,
    ): Style {
        $style = new Style;
        $style->setFontBold();
        $style->setFontSize($size);
        $style->setFontColor($color);
        $style->setCellAlignment($align);
        $style->setCellVerticalAlignment(CellVerticalAlignment::CENTER);

        if ($italic) {
            $style->setFontItalic();
        }

        return $style;
    }

    protected function headerStyle(): Style
    {
        $style = new Style;
        $style->setFontBold();
        $style->setFontSize(10);
        $style->setFontColor(self::COLORS['white']);
        $style->setBackgroundColor(Color::toARGB(self::COLORS['brand']));
        $style->setCellAlignment(CellAlignment::CENTER);
        $style->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
        $style->setShouldWrapText();
        $style->setBorder($this->border());

        return $style;
    }

    protected function bodyStyle(
        string $align = CellAlignment::LEFT,
        bool $bold = false,
        bool $italic = false,
        bool $wrap = false,
        ?string $fontColor = null,
    ): Style {
        $style = new Style;
        $style->setFontSize(10);
        $style->setFontColor($fontColor ?? self::COLORS['ink']);
        $style->setCellAlignment($align);
        $style->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
        $style->setBorder($this->border());

        if ($bold) {
            $style->setFontBold();
        }
        if ($italic) {
            $style->setFontItalic();
        }
        if ($wrap) {
            $style->setShouldWrapText();
        }

        return $style;
    }

    protected function totalStyle(string $align = CellAlignment::LEFT, bool $bold = true): Style
    {
        $style = $this->bodyStyle(align: $align, bold: $bold);
        $style->setBackgroundColor(Color::toARGB(self::COLORS['brand_soft']));

        return $style;
    }

    protected function border(): Border
    {
        return new Border(
            new BorderPart(Border::LEFT, self::COLORS['line'], Border::WIDTH_THIN),
            new BorderPart(Border::RIGHT, self::COLORS['line'], Border::WIDTH_THIN),
            new BorderPart(Border::TOP, self::COLORS['line'], Border::WIDTH_THIN),
            new BorderPart(Border::BOTTOM, self::COLORS['line'], Border::WIDTH_THIN),
        );
    }
}
