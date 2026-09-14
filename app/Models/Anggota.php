<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anggota extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'anggota';
    protected $primaryKey = 'nis';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nis',
        'nama_lengkap',
        'kelas',
        'password',
        'fcm_token',
    ];

    /**
     * Sembunyikan password dan FCM token dari response JSON.
     */
    protected $hidden = [
        'password',
        'fcm_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Accessor untuk mendapatkan inisial nama siswa (2 huruf pertama).
     */
    public function getInisialAttribute(): string
    {
        $words = explode(' ', trim($this->nama_lengkap ?? ''));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->nama_lengkap ?? 'NN', 0, 2));
    }

    /**
     * Relasi ke seluruh riwayat peminjaman siswa.
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'nis', 'nis');
    }

    /**
     * Peminjaman yang sedang aktif (menunggu, aktif, menunggu_kembali, terlambat).
     */
    public function peminjamanAktif(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'nis', 'nis')
            ->whereIn('status', ['menunggu', 'aktif', 'menunggu_kembali', 'terlambat']);
    }
}
