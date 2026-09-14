<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jenis extends Model
{
    protected $table = 'jenis';
    protected $primaryKey = 'id_jenis';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_jenis',
        'nama_jenis',
    ];

    /**
     * Relasi ke buku yang memiliki jenis ini.
     */
    public function buku(): HasMany
    {
        return $this->hasMany(Buku::class, 'id_jenis', 'id_jenis');
    }
}
