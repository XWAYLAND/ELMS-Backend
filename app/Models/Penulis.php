<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penulis extends Model
{
    protected $table = 'penulis';
    protected $primaryKey = 'id_penulis';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_penulis', 'nama_penulis'];

    public function buku()
    {
        return $this->hasMany(Buku::class, 'id_penulis', 'id_penulis');
    }
}