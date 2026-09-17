<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Pegawai extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_pegawai', 'nama', 'email', 'password'];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'id_pegawai', 'id_pegawai');
    }
}
