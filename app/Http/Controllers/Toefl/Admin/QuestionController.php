<?php

namespace App\Http\Controllers\Toefl\Admin;

use App\Http\Controllers\Controller;
use App\Models\ToeflAudioAsset;
use App\Models\ToeflExam;
use App\Models\ToeflPassage;
use App\Models\ToeflQuestion;
use App\Models\ToeflSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Kelola soal: pertanyaan (pilihan ganda), bacaan (passage), dan audio Listening.
 * Kunci jawaban (answer_key) hanya disimpan & dibaca di server/admin —
 * tidak pernah masuk payload sisi siswa.
 */
class QuestionController extends Controller
{
    public function index(ToeflExam $exam): View
    {
        $exam->load(['sections.questions', 'sections.passages', 'sections.audioAssets']);

        return view('toefl.admin.questions.index', [
            'exam' => $exam,
            'questionTypes' => config('toefl.question_types'),
            'answerOptions' => config('toefl.answer_options'),
        ]);
    }

    public function store(Request $request, ToeflExam $exam): RedirectResponse
    {
        $validated = $this->validateQuestion($request);

        $section = $exam->sections()->findOrFail((int) $validated['section_id']);

        $lastOrder = (int) $section->questions()->max('question_order');

        $section->questions()->create([
            'section_id' => $section->id,
            'passage_id' => $validated['passage_id'] ?? null,
            'audio_asset_id' => $validated['audio_asset_id'] ?? null,
            'question_type' => $validated['question_type'],
            'question_text' => $validated['question_text'],
            'options' => $this->normalizeOptions($validated['options']),
            'answer_key' => $validated['answer_key'],
            'points' => $validated['points'] ?? 1,
            'question_order' => $lastOrder + 1,
        ]);

        $exam->update(['total_questions' => $exam->questionCount()]);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function update(Request $request, ToeflExam $exam, ToeflQuestion $question): RedirectResponse
    {
        abort_unless($question->section->exam_id === $exam->id, 404);

        $validated = $this->validateQuestion($request, $question);

        // Pertahankan section asal (perpindahan bagian tidak didukung agar
        // penomoran tetap rapi; hapus lalu tambah bila ingin memindah).
        $question->update([
            'passage_id' => $validated['passage_id'] ?? null,
            'audio_asset_id' => $validated['audio_asset_id'] ?? null,
            'question_type' => $validated['question_type'],
            'question_text' => $validated['question_text'],
            'options' => $this->normalizeOptions($validated['options']),
            'answer_key' => $validated['answer_key'],
            'points' => $validated['points'] ?? 1,
        ]);

        return back()->with('success', 'Soal diperbarui.');
    }

    public function destroy(ToeflExam $exam, ToeflQuestion $question): RedirectResponse
    {
        abort_unless($question->section->exam_id === $exam->id, 404);

        $question->delete();
        $exam->update(['total_questions' => $exam->questionCount()]);

        return back()->with('success', 'Soal dihapus.');
    }

    /**
     * Tambah bacaan (passage) pada sebuah bagian.
     */
    public function storePassage(Request $request, ToeflExam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'section_id' => ['required', 'integer', Rule::exists('toefl_sections', 'id')->where('exam_id', $exam->id)],
            'title' => ['nullable', 'string', 'max:160'],
            'passage_text' => ['required', 'string', 'max:60000'],
        ], [], [
            'section_id' => 'bagian',
            'title' => 'judul bacaan',
            'passage_text' => 'isi bacaan',
        ]);

        $section = ToeflSection::findOrFail($validated['section_id']);

        $section->passages()->create([
            'title' => $validated['title'] ?? null,
            'passage_text' => $validated['passage_text'],
            'passage_order' => ((int) $section->passages()->max('passage_order')) + 1,
        ]);

        return back()->with('success', 'Bacaan ditambahkan.');
    }

    public function destroyPassage(ToeflExam $exam, ToeflPassage $passage): RedirectResponse
    {
        abort_unless($passage->section->exam_id === $exam->id, 404);

        $passage->delete();

        return back()->with('success', 'Bacaan dihapus (soal terkait kehilangan bacaan).');
    }

    /**
     * Unggah audio Listening (disimpan di disk PRIVAT — di luar public/).
     */
    public function storeAudio(Request $request, ToeflExam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'section_id' => ['required', 'integer', Rule::exists('toefl_sections', 'id')->where('exam_id', $exam->id)],
            'label' => ['nullable', 'string', 'max:160'],
            'audio' => ['required', 'file', 'mimes:mp3,wav,ogg,m4a', 'max:51200'],
        ], [
            'audio.mimes' => 'Format audio harus MP3, WAV, OGGA, atau M4A.',
            'audio.max' => 'Ukuran audio maksimal 50 MB.',
        ], [
            'section_id' => 'bagian',
            'audio' => 'berkas audio',
        ]);

        $file = $request->file('audio');
        $disk = (string) config('toefl.audio.disk', 'local');
        $directory = (string) config('toefl.audio.directory', 'toefl/audio');

        $path = $file->store($directory, $disk);

        $section = ToeflSection::findOrFail($validated['section_id']);

        $section->audioAssets()->create([
            'label' => $validated['label'] ?? $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
        ]);

        return back()->with('success', 'Audio diunggah ke penyimpanan privat.');
    }

    public function destroyAudio(ToeflExam $exam, ToeflAudioAsset $audio): RedirectResponse
    {
        abort_unless($audio->section->exam_id === $exam->id, 404);

        $disk = (string) config('toefl.audio.disk', 'local');

        if (Storage::disk($disk)->exists($audio->storage_path)) {
            Storage::disk($disk)->delete($audio->storage_path);
        }

        $audio->delete();

        return back()->with('success', 'Audio dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateQuestion(Request $request, ?ToeflQuestion $question = null): array
    {
        $options = config('toefl.answer_options', ['A', 'B', 'C', 'D']);

        $validated = $request->validate([
            'section_id' => ['required', 'integer', Rule::exists('toefl_sections', 'id')],
            'question_type' => ['required', Rule::in(array_keys(config('toefl.question_types')))],
            'question_text' => ['required', 'string', 'max:10000'],
            // Array kode opsi: harus tepat memuat semua kode pilihan.
            'options' => ['required', 'array', 'size:'.count($options)],
            'options.*' => ['required', 'string', 'max:1000'],
            'answer_key' => ['required', Rule::in($options)],
            'points' => ['nullable', 'integer', 'min:1', 'max:100'],
            'passage_id' => ['nullable', 'integer', Rule::exists('toefl_passages', 'id')],
            'audio_asset_id' => ['nullable', 'integer', Rule::exists('toefl_audio_assets', 'id')],
        ], [
            'options.size' => 'Setiap soal wajib punya tepat '.count($options).' pilihan jawaban.',
        ], [
            'section_id' => 'bagian',
            'question_type' => 'jenis soal',
            'question_text' => 'teks soal',
            'answer_key' => 'kunci jawaban',
            'passage_id' => 'bacaan',
            'audio_asset_id' => 'audio',
        ]);

        return $validated;
    }

    /**
     * Terima input options sebagai array kode -> array kode => teks.
     * Bentuk yang dikirim form: options[A], options[B], ...
     *
     * @param  array<int, string>  $raw
     * @return array<string, string>
     */
    protected function normalizeOptions(array $raw): array
    {
        $normalized = [];
        foreach ($raw as $key => $value) {
            $normalized[strtoupper((string) $key)] = trim((string) $value);
        }

        ksort($normalized);

        return $normalized;
    }
}
