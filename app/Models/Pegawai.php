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
     * Accessor untuk mendapatkan inisial nama pegawai.
     */
    public function getInisialAttribute(): string
    {
        $words = explode(' ', trim($this->nama ?? ''));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->nama ?? 'PG', 0, 2));
    }

    /**
     * Relasi ke peminjaman yang diverifikasi oleh pegawai ini.
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'id_pegawai', 'id_pegawai');
    }
}
