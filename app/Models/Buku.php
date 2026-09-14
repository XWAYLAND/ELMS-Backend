<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Buku extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'isbn';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'isbn',
        'judul',
        'slug',
        'cover',
        'edisi',
        'deskripsi_fisik',
        'bahasa',
        'tersedia',
        'id_jenis',
        'penulis',
        'penerbit',
    ];

    protected function casts(): array
    {
        return [
            'tersedia' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Buku $buku) {
            if (empty($buku->slug)) {
                $buku->slug = Str::slug($buku->judul) . '-' . Str::slug($buku->isbn);
            }
            if (! isset($buku->tersedia)) {
                $buku->tersedia = true;
            }
        });

        static::updating(function (Buku $buku) {
            if ($buku->isDirty('judul') && empty($buku->slug)) {
                $buku->slug = Str::slug($buku->judul) . '-' . Str::slug($buku->isbn);
            }
        });
    }

    /**
     * Accessor untuk URL Cover lengkap (mendukung URL eksternal, storage local, dan placeholder).
     */
    public function getCoverUrlAttribute(): ?string
    {
        if (empty($this->cover)) {
            return null;
        }

        if (Str::startsWith($this->cover, ['http://', 'https://'])) {
            return $this->cover;
        }

        return asset('storage/' . ltrim($this->cover, '/'));
    }

    /**
     * Relasi ke jenis buku.
     */
    public function jenis(): BelongsTo
    {
        return $this->belongsTo(Jenis::class, 'id_jenis', 'id_jenis');
    }

    /**
     * Relasi ke peminjaman buku ini.
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'isbn', 'isbn');
    }

    /**
     * Scope untuk buku yang tersedia untuk dipinjam.
     */
    public function scopeTersedia($query)
    {
        return $query->where('tersedia', true);
    }

    /**
     * Scope filter berdasarkan jenis / kategori.
     */
    public function scopeByKategori($query, ?string $idJenis)
    {
        return $idJenis ? $query->where('id_jenis', $idJenis) : $query;
    }

    /**
     * Scope untuk pencarian berdasarkan judul, ISBN, penulis, atau penerbit.
     */
    public function scopeSearch($query, ?string $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }

        $term = '%' . strtolower($keyword) . '%';

        return $query->where(function ($q) use ($term) {
            $q->whereRaw('LOWER(judul) LIKE ?', [$term])
                ->orWhereRaw('LOWER(isbn) LIKE ?', [$term])
                ->orWhereRaw('LOWER(penulis) LIKE ?', [$term])
                ->orWhereRaw('LOWER(penerbit) LIKE ?', [$term])
                ->orWhereHas('jenis', fn ($j) => $j->whereRaw('LOWER(nama_jenis) LIKE ?', [$term]));
        });
    }

    /**
     * Scope filter berdasarkan bahasa.
     */
    public function scopeFilterBahasa($query, ?string $bahasa)
    {
        return $bahasa ? $query->where('bahasa', $bahasa) : $query;
    }
}
