<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penerbit extends Model
{
    protected $table = 'penerbit';
    protected $primaryKey = 'id_penerbit';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_penerbit',
        'nama_penerbit',
    ];

    /**
     * Relasi ke buku yang diterbitkan oleh penerbit ini.
     */
    public function buku(): HasMany
    {
        return $this->hasMany(Buku::class, 'id_penerbit', 'id_penerbit');
    }
}
