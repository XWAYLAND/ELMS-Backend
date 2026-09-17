<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Anggota extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'anggota';
    protected $primaryKey = 'nis';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['nis', 'nama_lengkap', 'kelas', 'password', 'fcm_token'];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'nis', 'nis');
    }

    public function peminjamanAktif(): HasMany
    {
        return $this->peminjaman()->whereIn('status', ['aktif', 'terlambat', 'menunggu_kembali']);
    }

    public function getInisialAttribute(): string
    {
        $words = explode(' ', trim($this->nama_lengkap));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->nama_lengkap, 0, 2));
    }
}
