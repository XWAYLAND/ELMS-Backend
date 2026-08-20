<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penulis extends Model
{
    protected $table = 'penulis';
    protected $primaryKey = 'id_penulis';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_penulis',
        'nama_penulis',
    ];

    /**
     * Relasi ke buku yang ditulis oleh penulis ini.
     */
    public function buku(): HasMany
    {
        return $this->hasMany(Buku::class, 'id_penulis', 'id_penulis');
    }
}
