<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Buku extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'isbn';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'isbn', 'judul', 'slug', 'cover', 'edisi', 'deskripsi_fisik',
        'bahasa', 'tersedia', 'id_jenis', 'penulis', 'penerbit'
    ];

    protected $casts = [
        'tersedia' => 'boolean',
    ];

    protected $appends = ['cover_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->judul) . '-' . $model->isbn;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('judul')) {
                $model->slug = Str::slug($model->judul) . '-' . $model->isbn;
            }
        });
    }

    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'id_jenis', 'id_jenis');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'isbn', 'isbn');
    }

    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('tersedia', true);
    }

    public function scopeByKategori(Builder $query, ?string $idJenis): Builder
    {
        return $query->when($idJenis, fn($q) => $q->where('id_jenis', $idJenis));
    }

    public function scopeFilterBahasa(Builder $query, ?string $bahasa): Builder
    {
        return $query->when($bahasa, fn($q) => $q->where('bahasa', $bahasa));
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        return $query->when($keyword, function ($q) use ($keyword) {
            $q->where(function ($searchQuery) use ($keyword) {
                $searchQuery->where('judul', 'LIKE', "%{$keyword}%")
                    ->orWhere('isbn', 'LIKE', "%{$keyword}%")
                    ->orWhere('penulis', 'LIKE', "%{$keyword}%");
            });
        });
    }

    public function getCoverUrlAttribute(): string
    {
        if (empty($this->cover)) {
            return asset('images/book-placeholder.png');
        }
        if (str_starts_with($this->cover, 'http')) {
            return $this->cover;
        }

        if (! Storage::disk('public')->exists($this->cover)) {
            return asset('images/book-placeholder.png');
        }

        $timestamp = Storage::disk('public')->lastModified($this->cover);
        return asset('storage/' . $this->cover) . '?v=' . $timestamp;
    }
}
