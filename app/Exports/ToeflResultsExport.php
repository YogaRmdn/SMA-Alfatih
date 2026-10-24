<?php

namespace App\Exports;

use App\Models\Setting;
use App\Models\ToeflAttempt;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Unduhan hasil TOEFL ITP (XLSX) — satu sheet rekap:
 * identitas siswa, status attempt, skor mentah per bagian, total benar,
 * dan skor scaled (cap "latihan" — aproksimasi, bukan skor resmi ETS).
 *
 * Ditulis dengan OpenSpout (tanpa dependensi ekstensi gd).
 */
class ToeflResultsExport
{
    protected const HEADER = ['No', 'Nama Siswa', 'Nomor', 'Kelas', 'Ujian', 'Status', 'Listening', 'Structure', 'Reading', 'Total Benar', 'Total Soal', 'Skor Scaled', 'Selesai Pada'];

    protected Writer $writer;

    /**
     * @param  Collection<int, ToeflAttempt>  $attempts
     * @param  array<int, string>  $metaLines
     */
    public function __construct(
        protected Collection $attempts,
        protected array $metaLines = [],
    ) {}

    public function download(string $filename): StreamedResponse
    {
        $dir = is_dir('/tmp/opencode') ? '/tmp/opencode' : sys_get_temp_dir();
        $path = $dir.'/toefl-export-'.Str::random(10).'.xlsx';

        $this->build($path);

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    protected function build(string $path): void
    {
        $this->writer = new Writer;
        $this->writer->openToFile($path);

        $this->attempts->loadMissing(['student', 'exam', 'result']);

        // Kop laporan.
        $school = Setting::query()->pluck('value', 'key')['site_name'] ?? config('app.name');
        $this->writer->addRow(new Row([new Cell($school)], $this->titleStyle()));
        $this->writer->addRow(new Row([new Cell('Rekap Hasil TOEFL ITP Level 1 (Latihan)')], $this->titleStyle()));

        foreach ($this->metaLines as $line) {
            $this->writer->addRow(new Row([new Cell($line)]));
        }

        $this->writer->addRow(new Row([]));

        // Header tabel.
        $this->writer->addRow(new Row(
            array_map(fn (string $h) => new Cell($h), self::HEADER),
            $this->headerStyle()
        ));

        // Baris data.
        foreach ($this->attempts->values() as $i => $attempt) {
            $r = $attempt->result;
            $student = $attempt->student;

            $this->writer->addRow(new Row([
                new Cell($i + 1),
                new Cell($student?->full_name ?? '-'),
                new Cell($student?->student_number ?? '-'),
                new Cell($student?->class_name ?? '-'),
                new Cell($attempt->exam?->title ?? '-'),
                new Cell($attempt->statusLabel()),
                new Cell($r?->listening_raw_score ?? 0),
                new Cell($r?->structure_raw_score ?? 0),
                new Cell($r?->reading_raw_score ?? 0),
                new Cell($r?->total_correct ?? 0),
                new Cell($r?->total_questions ?? 0),
                new Cell($r?->scaled_score ?? 0),
                new Cell($r?->completed_at?->format('Y-m-d H:i') ?? ($attempt->submitted_at?->format('Y-m-d H:i') ?? '-')),
            ]));
        }

        $this->writer->close();
    }

    protected function titleStyle(): Style
    {
        $style = new Style;
        $style->setFontSize(14);
        $style->setFontBold(true);

        return $style;
    }

    protected function headerStyle(): Style
    {
        $style = new Style;
        $style->setFontBold(true);

        return $style;
    }
}
