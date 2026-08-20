<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'isbn';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'isbn',
        'judul',
        'edisi',
        'deskripsi_fisik',
        'bahasa',
        'cover',
        'id_jenis',
        'id_penulis',
        'id_penerbit',
    ];

    /**
     * Relasi ke jenis buku.
     */
    public function jenis(): BelongsTo
    {
        return $this->belongsTo(Jenis::class, 'id_jenis', 'id_jenis');
    }

    /**
     * Relasi ke penulis buku.
     */
    public function penulis(): BelongsTo
    {
        return $this->belongsTo(Penulis::class, 'id_penulis', 'id_penulis');
    }

    /**
     * Relasi ke penerbit buku.
     */
    public function penerbit(): BelongsTo
    {
        return $this->belongsTo(Penerbit::class, 'id_penerbit', 'id_penerbit');
    }

    /**
     * Relasi ke peminjaman buku ini.
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'isbn', 'isbn');
    }

    /**
     * Scope untuk pencarian berdasarkan judul, penulis, jenis, penerbit.
     */
    public function scopeSearch($query, ?string $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }

        $term = '%' . strtolower($keyword) . '%';

        return $query->where(function ($q) use ($term) {
            $q->whereRaw('LOWER(judul) LIKE ?', [$term])
                ->orWhereHas('penulis', fn ($p) => $p->whereRaw('LOWER(nama_penulis) LIKE ?', [$term]))
                ->orWhereHas('penerbit', fn ($p) => $p->whereRaw('LOWER(nama_penerbit) LIKE ?', [$term]))
                ->orWhereHas('jenis', fn ($j) => $j->whereRaw('LOWER(nama_jenis) LIKE ?', [$term]));
        });
    }

    /**
     * Scope untuk filter berdasarkan jenis.
     */
    public function scopeFilterJenis($query, ?string $idJenis)
    {
        return $idJenis ? $query->where('id_jenis', $idJenis) : $query;
    }

    /**
     * Scope untuk filter berdasarkan penulis.
     */
    public function scopeFilterPenulis($query, ?string $idPenulis)
    {
        return $idPenulis ? $query->where('id_penulis', $idPenulis) : $query;
    }

    /**
     * Scope untuk filter berdasarkan penerbit.
     */
    public function scopeFilterPenerbit($query, ?string $idPenerbit)
    {
        return $idPenerbit ? $query->where('id_penerbit', $idPenerbit) : $query;
    }

    /**
     * Scope untuk filter berdasarkan bahasa.
     */
    public function scopeFilterBahasa($query, ?string $bahasa)
    {
        return $bahasa ? $query->where('bahasa', $bahasa) : $query;
    }
}
