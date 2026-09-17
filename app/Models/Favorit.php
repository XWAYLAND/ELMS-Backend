<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorit extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'favorit';
    protected $fillable = ['nis', 'isbn'];
    protected $primaryKey = null; // composite PK

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'nis', 'nis');
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'isbn', 'isbn');
    }
}
