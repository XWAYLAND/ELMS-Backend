<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerbit extends Model
{
    protected $table = 'penerbit';
    protected $primaryKey = 'id_penerbit';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_penerbit', 'nama_penerbit'];

    public function buku()
    {
        return $this->hasMany(Buku::class, 'id_penerbit', 'id_penerbit');
    }
}