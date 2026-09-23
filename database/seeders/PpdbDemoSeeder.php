<?php

namespace Database\Seeders;

use App\Models\Ppdb;
use App\Models\PpdbDocument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PpdbDemoSeeder extends Seeder
{
    use WithoutModelEvents;

    protected const DOCUMENT_TYPES = [
        'kk' => 'Kartu Keluarga (KK)',
        'birth_certificate' => 'Akta Kelahiran',
        'diploma' => 'Ijazah / SKL',
        'report_card' => 'Rapor',
    ];

    public function run(): void
    {
        $disk = Storage::disk('local');

        $photo = $this->fakePhoto();
        $pdf = $this->fakePdf('Dokumen PPDB Demo');

        $academicYear = '2026/2027';

        $registrants = [
            [
                'registration_number' => 'PPDB-2026-0001',
                'access_code' => 'KODE001A',
                'full_name' => 'Ahmad Fauzan Ramadhan',
                'gender' => 'L',
                'birth_place' => 'Bandung',
                'birth_date' => '2013-04-12',
                'address' => 'Jl. Cihampelas No. 25, Bandung',
                'phone' => '081234567890',
                'email' => 'fauzan.ahmad@example.com',
                'nisn' => '0137894562',
                'origin_school' => 'SDN Cihampelas 01',
                'father_name' => 'Hendra Gunawan',
                'mother_name' => 'Siti Rahmawati',
                'father_job' => 'Wiraswasta',
                'mother_job' => 'Ibu Rumah Tangga',
                'family_income' => 'Rp 2.500.000 - Rp 5.000.000',
                'status' => 'pending',
                'academic_year' => $academicYear,
            ],
            [
                'registration_number' => 'PPDB-2026-0002',
                'access_code' => 'KODE002B',
                'full_name' => 'Siti Nurhaliza Putri',
                'gender' => 'P',
                'birth_place' => 'Cimahi',
                'birth_date' => '2012-11-30',
                'address' => 'Jl. Melati No. 8, Cimahi',
                'phone' => '085711223344',
                'email' => 'nurhaliza.putri@example.com',
                'nisn' => '0131122334',
                'origin_school' => 'SDIT Al-Falah Cimahi',
                'father_name' => 'Dedi Supriadi',
                'mother_name' => 'Lina Marlina',
                'father_job' => 'Karyawan Swasta',
                'mother_job' => 'Guru TPQ',
                'family_income' => 'Rp 5.000.000 - Rp 10.000.000',
                'status' => 'verified',
                'admin_notes' => 'Berkas lengkap, menunggu verifikasi lapangan.',
                'academic_year' => $academicYear,
            ],
            [
                'registration_number' => 'PPDB-2026-0003',
                'access_code' => 'KODE003C',
                'full_name' => 'Muhammad Rizky Pratama',
                'gender' => 'L',
                'birth_place' => 'Bandung',
                'birth_date' => '2013-02-18',
                'address' => 'Jl. Antapani Lama No. 14, Bandung',
                'phone' => '082198765432',
                'email' => 'rizky.pratama@example.com',
                'nisn' => '0134455667',
                'origin_school' => 'SD Negeri 45 Antapani',
                'father_name' => 'Bambang Setiawan',
                'mother_name' => 'Dewi Anggraini',
                'father_job' => 'PNS',
                'mother_job' => 'Bidan',
                'family_income' => 'Rp 5.000.000 - Rp 10.000.000',
                'status' => 'lulus_administrasi',
                'admin_notes' => 'Administrasi lengkap dan memenuhi syarat.',
                'academic_year' => $academicYear,
            ],
            [
                'registration_number' => 'PPDB-2026-0004',
                'access_code' => 'KODE004D',
                'full_name' => 'Aisyah Fatimah Zahra',
                'gender' => 'P',
                'birth_place' => 'Bogor',
                'birth_date' => '2013-07-25',
                'address' => 'Kp. Pasir Jati RT 03/02, Bogor',
                'phone' => '081298765431',
                'email' => 'aisyah.zahra@example.com',
                'nisn' => '0137788990',
                'origin_school' => 'SDN Pasir Jati 02',
                'father_name' => 'Abdul Rohman',
                'mother_name' => 'Nurhayati',
                'father_job' => 'Buruh',
                'mother_job' => 'Ibu Rumah Tangga',
                'family_income' => '< Rp 2.500.000',
                'status' => 'rejected',
                'admin_notes' => 'Rapor asli tidak sesuai dengan dokumen yang diunggah.',
                'academic_year' => $academicYear,
            ],
            [
                'registration_number' => 'PPDB-2026-0005',
                'access_code' => 'KODE005E',
                'full_name' => 'Bintang Maulana Yusuf',
                'gender' => 'L',
                'birth_place' => 'Subang',
                'birth_date' => '2012-09-05',
                'address' => 'Jl. Raya Subang No. 33, Subang',
                'phone' => '085608112233',
                'email' => 'bintang.yusuf@example.com',
                'nisn' => '0131122334',
                'origin_school' => 'SDIT Al-Hikmah Subang',
                'father_name' => 'Agus Salim',
                'mother_name' => 'Hj. Eneng Karyati',
                'father_job' => 'Pengusaha',
                'mother_job' => 'Wirausaha',
                'family_income' => '> Rp 10.000.000',
                'status' => 'accepted',
                'admin_notes' => 'Selamat! Putra Bapak/Ibu diterima di SMA IT Tahfizh Al-Fatih.',
                'academic_year' => $academicYear,
            ],
            [
                'registration_number' => 'PPDB-2026-0006',
                'access_code' => 'KODE006F',
                'full_name' => 'Nadia Rahma Aulia',
                'gender' => 'P',
                'birth_place' => 'Garut',
                'birth_date' => '2013-05-14',
                'address' => 'Jl. Guntur No. 7, Garut',
                'phone' => '081377886655',
                'email' => 'nadia.aulia@example.com',
                'nisn' => '0139900112',
                'origin_school' => 'SDN Guntur 03',
                'father_name' => 'Ujang Suherman',
                'mother_name' => 'Ai Sari',
                'father_job' => 'Petani',
                'mother_job' => 'Pedagang',
                'family_income' => '< Rp 2.500.000',
                'status' => 'pending',
                'admin_notes' => null,
                'academic_year' => '2025/2026',
            ],
        ];

        foreach ($registrants as $index => $data) {
            $photoPath = "ppdb/photos/demo-{$data['registration_number']}.png";
            $disk->put($photoPath, $photo);
            $data['photo'] = $photoPath;

            $documents = [];
            foreach (array_keys(self::DOCUMENT_TYPES) as $type) {
                $ext = $type === 'birth_certificate' ? 'png' : 'pdf';
                $path = "ppdb/documents/demo-{$data['registration_number']}-{$type}.{$ext}";
                $disk->put($path, $ext === 'pdf' ? $pdf : $photo);
                $documents[$type] = $path;
            }

            $ppdb = Ppdb::updateOrCreate(
                ['registration_number' => $data['registration_number']],
                $data
            );

            foreach ($documents as $type => $path) {
                PpdbDocument::updateOrCreate(
                    ['ppdb_id' => $ppdb->id, 'type' => $type],
                    ['name' => self::DOCUMENT_TYPES[$type], 'file_path' => $path]
                );
            }
        }
    }

    protected function fakePhoto(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
        );
    }

    protected function fakePdf(string $title): string
    {
        $body = "BT /F1 18 Tf 72 780 Td ({$title}) Tj ET";
        $objects = [
            [1, "<< /Type /Catalog /Pages 2 0 R >>"],
            [2, "<< /Type /Pages /Kids [3 0 R] /Count 1 >>"],
            [3, "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>"],
            [4, "<< /Length ".strlen($body)." >>\nstream\n".$body."\nendstream"],
            [5, "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>"],
        ];

        $content = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as [$num, $bodyText]) {
            $offsets[$num] = strlen($content);
            $content .= "{$num} 0 obj\n{$bodyText}\nendobj\n";
        }

        $xrefOffset = strlen($content);
        $content .= "xref\n0 6\n";
        $content .= "0000000000 65535 f \n";
        for ($i = 1; $i <= 5; $i++) {
            $content .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $content .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n".$xrefOffset."\n%%EOF\n";

        return $content;
    }
}