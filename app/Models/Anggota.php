<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anggota extends Model
{
    protected $table = 'anggota';
    protected $primaryKey = 'nis';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nis',
        'nama_lengkap',
        'kelas',
        'fcm_token',
    ];

    /**
     * Sembunyikan FCM token dari response JSON secara default.
     */
    protected $hidden = ['fcm_token'];

    /**
     * Relasi ke peminjaman yang dilakukan oleh anggota ini.
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'nis', 'nis');
    }

    /**
     * Peminjaman yang sedang aktif (dipinjam atau terlambat).
     */
    public function peminjamanAktif(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'nis', 'nis')
            ->whereIn('status', ['dipinjam', 'terlambat']);
    }
}
