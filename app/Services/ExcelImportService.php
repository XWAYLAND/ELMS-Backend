<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExcelImportService
{
    /**
     * Import users from uploaded Excel/CSV file.
     * Sheet columns: role, nis, nama_lengkap, kelas, id_pegawai, nama, email
     * Password auto-generated from NIS or ID Pegawai.
     *
     * @return array{created: int, errors: array<int, string>}
     */
    public function import(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $rows        = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        if (empty($rows)) {
            return ['created' => 0, 'errors' => ['File kosong.']];
        }

        // First row = headers
        $headers = array_map(fn($h) => strtolower(trim((string) $h)), array_shift($rows));

        $created = 0;
        $errors  = [];

        DB::transaction(function () use ($rows, $headers, &$created, &$errors) {
            foreach ($rows as $rowIndex => $row) {
                $line = $rowIndex + 2; // human-readable row number

                // Skip fully empty rows
                if (empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                    continue;
                }

                $data = array_combine($headers, array_map(fn($v) => trim((string) ($v ?? '')), $row));

                $role = strtolower($data['role'] ?? '');

                if ($role === 'student') {
                    $err = $this->importStudent($data);
                } elseif ($role === 'staff') {
                    $err = $this->importStaff($data);
                } else {
                    $errors[] = "Baris {$line}: kolom 'role' tidak valid (gunakan 'student' atau 'staff').";
                    continue;
                }

                if ($err) {
                    $errors[] = "Baris {$line}: {$err}";
                } else {
                    $created++;
                }
            }
        });

        return ['created' => $created, 'errors' => $errors];
    }

    private function importStudent(array $data): ?string
    {
        $nis  = $data['nis'] ?? '';
        $nama = $data['nama_lengkap'] ?? ($data['nama'] ?? '');
        $kelas = $data['kelas'] ?? '';

        if (!$nis)   return "NIS wajib diisi.";
        if (!$nama)  return "Nama lengkap wajib diisi.";
        if (!$kelas) return "Kelas wajib diisi.";

        if (Anggota::where('nis', $nis)->exists()) {
            return "NIS '{$nis}' sudah terdaftar.";
        }

        Anggota::create([
            'nis'          => $nis,
            'nama_lengkap' => $nama,
            'kelas'        => $kelas,
            'password'     => Hash::make($nis),
        ]);

        return null;
    }

    private function importStaff(array $data): ?string
    {
        $idPegawai = $data['id_pegawai'] ?? '';
        $nama      = $data['nama'] ?? ($data['nama_lengkap'] ?? '');
        $email     = $data['email'] ?? '';

        if (!$idPegawai) return "ID Pegawai wajib diisi.";
        if (!$nama)      return "Nama wajib diisi.";

        if (Pegawai::where('id_pegawai', $idPegawai)->exists()) {
            return "ID Pegawai '{$idPegawai}' sudah terdaftar.";
        }

        if ($email && Pegawai::where('email', $email)->exists()) {
            return "Email '{$email}' sudah digunakan.";
        }

        Pegawai::create([
            'id_pegawai' => $idPegawai,
            'nama'       => $nama,
            'email'      => $email ?: null,
            'password'   => Hash::make($idPegawai),
        ]);

        return null;
    }

    /**
     * Generate a downloadable template spreadsheet.
     */
    public function generateTemplate(): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $headers = ['role', 'nis', 'nama_lengkap', 'kelas', 'id_pegawai', 'nama', 'email'];
        foreach ($headers as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Example rows
        $sheet->fromArray(['student', '12345', 'Budi Santoso', 'XII IPA 1', '', '', ''], null, 'A2');
        $sheet->fromArray(['staff', '', '', '', 'PGW001', 'Siti Rahayu', 'siti@school.id'], null, 'A3');

        return $spreadsheet;
    }
}
