<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class StudentsImport implements ToCollection, WithHeadingRow, SkipsOnError, WithChunkReading
{
    use SkipsErrors;

    public int $imported = 0;
    public int $skipped  = 0;
    public array $errors = [];

    // Proses dalam chunk 100 baris untuk mencegah timeout
    public function chunkSize(): int
    {
        return 100;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 karena heading row

            // Skip baris kosong
            if (empty(trim($row['nis'] ?? ''))) {
                continue;
            }

            // Cari kelas berdasarkan nama
            $class = null;
            if (!empty($row['kelas'])) {
                $class = SchoolClass::where('name', trim($row['kelas']))->first();
            }

            // Validasi NIS unik
            if (Student::where('nis', trim($row['nis']))->exists()) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNum}: NIS '{$row['nis']}' sudah ada, dilewati.";
                continue;
            }

            // Validasi NISN unik jika diisi
            if (!empty($row['nisn']) && Student::where('nisn', trim($row['nisn']))->exists()) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNum}: NISN '{$row['nisn']}' sudah ada, dilewati.";
                continue;
            }

            try {
                // Parse tanggal lahir
                $birthDate = null;
                if (!empty($row['tanggal_lahir'])) {
                    try {
                        $birthDate = \Carbon\Carbon::parse($row['tanggal_lahir'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        $birthDate = null;
                    }
                }

                // Normalize gender
                $gender = 'L';
                if (!empty($row['jenis_kelamin'])) {
                    $g = strtoupper(trim($row['jenis_kelamin']));
                    $gender = in_array($g, ['P', 'PEREMPUAN', 'WANITA', 'PR']) ? 'P' : 'L';
                }

                // Normalize location
                $location = null;
                if (!empty($row['lokasi'])) {
                    $l = strtolower(trim($row['lokasi']));
                    if (str_contains($l, 'selatan') || str_contains($l, 'cendrawasih')) {
                        $location = 'selatan';
                    } elseif (str_contains($l, 'utara') || str_contains($l, 'sarbini')) {
                        $location = 'utara';
                    }
                }

                Student::create([
                    'nis'          => trim($row['nis']),
                    'nisn'         => !empty($row['nisn']) ? trim($row['nisn']) : null,
                    'name'         => trim($row['nama']),
                    'gender'       => $gender,
                    'birth_place'  => $row['tempat_lahir'] ?? null,
                    'birth_date'   => $birthDate,
                    'religion'     => $row['agama'] ?? null,
                    'address'      => $row['alamat'] ?? null,
                    'phone'        => $row['nomor_hp'] ?? null,
                    'parent_name'  => $row['nama_orang_tua'] ?? null,
                    'parent_phone' => $row['nomor_hp_orang_tua'] ?? null,
                    'class_id'     => $class?->id,
                    'location'     => $location,
                    'status'       => 'aktif',
                ]);
                $this->imported++;
            } catch (\Exception $e) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNum}: Gagal — " . $e->getMessage();
            }
        }
    }
}

