<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $primaryKey = 'id_transaksi';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_transaksi',
        'nis',
        'isbn',
        'id_pegawai',
        'kode_unik',
        'kode_unik_expires_at',
        'waktu_pengajuan',
        'batas_waktu',
        'durasi_hari',
        'tanggal_kembali',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'waktu_pengajuan'      => 'datetime',
            'batas_waktu'          => 'datetime',
            'tanggal_kembali'      => 'datetime',
            'kode_unik_expires_at' => 'datetime',
            'durasi_hari'          => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Peminjaman $peminjaman) {
            if (empty($peminjaman->id_transaksi)) {
                $peminjaman->id_transaksi = 'TRX-' . strtoupper(Str::random(8));
            }
            if (empty($peminjaman->kode_unik)) {
                $peminjaman->kode_unik = strtoupper(Str::random(8));
            }
            if (empty($peminjaman->kode_unik_expires_at)) {
                $peminjaman->kode_unik_expires_at = now()->addHours(24);
            }
            if (empty($peminjaman->waktu_pengajuan)) {
                $peminjaman->waktu_pengajuan = now();
            }
            if (empty($peminjaman->durasi_hari)) {
                $peminjaman->durasi_hari = 7;
            }
            if (empty($peminjaman->status)) {
                $peminjaman->status = 'menunggu';
            }
        });
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
     * Relasi ke pegawai yang memverifikasi/menangani peminjaman.
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    /**
     * Cek apakah kode unik sudah kadaluwarsa (> 24 jam).
     */
    public function isKodeExpired(): bool
    {
        if (! $this->kode_unik_expires_at) {
            return false;
        }

        return now()->isAfter($this->kode_unik_expires_at);
    }

    /**
     * Cek apakah kode unik masih bisa digunakan.
     */
    public function isKodeUsable(): bool
    {
        return ! $this->isKodeExpired();
    }

    /**
     * Cek apakah peminjaman dapat disetujui (status menunggu + kode belum expired).
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'menunggu' && $this->isKodeUsable();
    }

    /**
     * Cek apakah peminjaman dapat dikembalikan.
     */
    public function canBeReturned(): bool
    {
        return in_array($this->status, ['aktif', 'terlambat', 'menunggu_kembali']);
    }

    /**
     * Accessor untuk mengecek keterlambatan.
     */
    public function getIsTerlambatAttribute(): bool
    {
        if (in_array($this->status, ['dikembalikan', 'ditolak'])) {
            return false;
        }

        if (! $this->batas_waktu) {
            return false;
        }

        return now()->isAfter($this->batas_waktu);
    }

    /**
     * Accessor sisa waktu dalam format yang mudah dipahami.
     */
    public function getSisaWaktuAttribute(): ?string
    {
        if (! $this->batas_waktu) {
            return null;
        }

        if (in_array($this->status, ['dikembalikan', 'ditolak'])) {
            return 'Selesai';
        }

        $now = now();
        if ($now->isAfter($this->batas_waktu)) {
            $days = $now->diffInDays($this->batas_waktu);
            return $days === 0 ? 'Terlambat hari ini' : "Terlambat {$days} hari";
        }

        $days = $now->diffInDays($this->batas_waktu);
        return $days === 0 ? 'Hari ini terakhir' : "{$days} hari lagi";
    }

    /**
     * Scope filter berdasarkan status.
     */
    public function scopeStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    /**
     * Scope untuk peminjaman yang mendekati jatuh tempo atau sudah lewat.
     */
    public function scopeMendekatiJatuhTempo($query, int $hari = 3)
    {
        return $query->whereIn('status', ['aktif', 'terlambat', 'menunggu_kembali'])
            ->whereNotNull('batas_waktu')
            ->whereDate('batas_waktu', '<=', now()->addDays($hari));
    }
}
