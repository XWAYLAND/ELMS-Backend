<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $primaryKey = 'id_transaksi';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_transaksi',
        'waktu_pinjam',
        'batas_kembali',
        'status',
        'nis',
        'isbn',
        'id_pegawai',
    ];

    protected function casts(): array
    {
        return [
            'waktu_pinjam'  => 'date',
            'batas_kembali' => 'date',
        ];
    }

    /**
     * Relasi ke anggota yang melakukan peminjaman.
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'nis', 'nis');
    }

    /**
     * Relasi ke buku yang dipinjam.
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'isbn', 'isbn');
    }

    /**
     * Relasi ke pegawai yang memverifikasi peminjaman.
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    /**
     * Scope untuk memfilter berdasarkan status.
     */
    public function scopeStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    /**
     * Scope untuk peminjaman yang mendekati jatuh tempo (dalam N hari).
     */
    public function scopeMendekatiJatuhTempo($query, int $hari = 3)
    {
        return $query->whereIn('status', ['dipinjam', 'terlambat'])
            ->whereDate('batas_kembali', '<=', now()->addDays($hari));
    }
}
