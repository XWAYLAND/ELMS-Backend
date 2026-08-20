<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_pegawai',
        'nama',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke peminjaman yang diverifikasi oleh pegawai ini.
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'id_pegawai', 'id_pegawai');
    }
}
