<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    protected $table = 'jenis';
    protected $primaryKey = 'id_jenis';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id_jenis', 'nama_jenis'];

    public function buku()
    {
        return $this->hasMany(Buku::class, 'id_jenis', 'id_jenis');
    }
}