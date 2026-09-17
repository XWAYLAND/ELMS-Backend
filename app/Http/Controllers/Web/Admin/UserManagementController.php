<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\StoreBulkUsersRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Anggota;
use App\Models\Pegawai;
use App\Services\ExcelImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

class UserManagementController extends Controller
{
    public function __construct(private ExcelImportService $importService) {}

    public function index(Request $request)
    {
        $q = $request->string('q')->trim()->value();

        $students = Anggota::select(
                'nis as id',
                'nama_lengkap as nama',
                'kelas as sub',
                \DB::raw("'student' as role"),
                'created_at'
            )
            ->when($q, fn($query) => $query->where(function ($query) use ($q) {
                $query->where('nis', 'like', "%{$q}%")
                      ->orWhere('nama_lengkap', 'like', "%{$q}%")
                      ->orWhere('kelas', 'like', "%{$q}%");
            }));

        $staff = Pegawai::select(
                'id_pegawai as id',
                'nama',
                \DB::raw("COALESCE(email, '') as sub"),
                \DB::raw("'staff' as role"),
                'created_at'
            )
            ->when($q, fn($query) => $query->where(function ($query) use ($q) {
                $query->where('id_pegawai', 'like', "%{$q}%")
                      ->orWhere('nama', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            }));

        $users = $students->unionAll($staff)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function storeSingle(StoreUserRequest $request)
    {
        if ($request->input('role') === 'student') {
            $password = $request->filled('password')
                ? $request->input('password')
                : $request->input('nis');

            Anggota::create([
                'nis'          => $request->input('nis'),
                'nama_lengkap' => $request->input('name'),
                'kelas'        => $request->input('kelas'),
                'password'     => Hash::make($password),
            ]);
        } else {
            $password = $request->filled('password')
                ? $request->input('password')
                : $request->input('id_pegawai');

            Pegawai::create([
                'id_pegawai' => $request->input('id_pegawai'),
                'nama'       => $request->input('name'),
                'email'      => $request->input('email') ?: null,
                'password'   => Hash::make($password),
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function storeBulk(StoreBulkUsersRequest $request)
    {
        $path   = $request->file('file')->getRealPath();
        $result = $this->importService->import($path);

        $message = "Import selesai: {$result['created']} pengguna berhasil ditambahkan.";
        if (!empty($result['errors'])) {
            $message .= ' ' . count($result['errors']) . ' baris gagal.';
        }

        return redirect()->route('admin.users.index')
            ->with('success', $message)
            ->with('import_errors', $result['errors']);
    }

    public function edit(string $id)
    {
        // Try student first, then staff
        $user = Anggota::find($id);
        if ($user) {
            $role = 'student';
        } else {
            $user = Pegawai::findOrFail($id);
            $role = 'staff';
        }

        return view('admin.users.edit', compact('user', 'role'));
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        $role = $request->input('role');

        if ($role === 'student') {
            $anggota = Anggota::findOrFail($id);
            $data = [
                'nama_lengkap' => $request->input('name'),
                'kelas'        => $request->input('kelas'),
            ];
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->input('password'));
            }
            $anggota->update($data);
        } else {
            $pegawai = Pegawai::findOrFail($id);
            $data = [
                'nama'  => $request->input('name'),
                'email' => $request->input('email') ?: null,
            ];
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->input('password'));
            }
            $pegawai->update($data);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        // Try student first, then staff
        $user = Anggota::find($id);
        if ($user) {
            $user->delete();
        } else {
            $user = Pegawai::find($id);
            if (!$user) {
                abort(404);
            }
            $user->delete();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = $this->importService->generateTemplate();
        $writer      = new XlsxWriter($spreadsheet);

        $filename = 'template_import_users.xlsx';
        $tmpPath  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        $writer->save($tmpPath);

        return response()->download($tmpPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
