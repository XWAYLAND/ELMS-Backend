<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $primaryKey = 'id_transaksi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_transaksi', 'nis', 'isbn', 'id_pegawai', 'kode_unik',
        'kode_unik_expires_at',
        'waktu_pengajuan', 'batas_waktu', 'durasi_hari',
        'tanggal_kembali',
        'status'
    ];

    protected $casts = [
        'waktu_pengajuan'     => 'datetime',
        'batas_waktu'         => 'datetime',
        'kode_unik_expires_at'=> 'datetime',
        'tanggal_kembali'     => 'datetime',
        'durasi_hari'         => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id_transaksi = 'TRX-' . Str::upper(Str::random(8));
            $model->kode_unik = Str::upper(Str::random(8));
            $model->kode_unik_expires_at = now()->addHours(24);
            $model->waktu_pengajuan = now();
        });
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'nis', 'nis');
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'isbn', 'isbn');
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function getSisaWaktuAttribute()
    {
        if (!$this->batas_waktu) {
            return null;
        }
        return Carbon::now()->diffForHumans($this->batas_waktu, true);
    }

    public function getIsTerlambatAttribute(): bool
    {
        if (!$this->batas_waktu) {
            return false;
        }
        return $this->status !== 'dikembalikan' && now()->greaterThan($this->batas_waktu);
    }

    public function isKodeExpired(): bool
    {
        if (!$this->kode_unik_expires_at) {
            return false;
        }
        return now()->greaterThan($this->kode_unik_expires_at);
    }

    public function isKodeUsable(): bool
    {
        if ($this->isKodeExpired()) {
            return false;
        }
        if (in_array($this->status, ['dikembalikan', 'ditolak'])) {
            return false;
        }
        return true;
    }

    public function scopeFindByValidKode($query, string $kode)
    {
        return $query->where('kode_unik', strtoupper(trim($kode)));
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'menunggu' && !$this->isKodeExpired();
    }

    public function canBeReturned(): bool
    {
        return in_array($this->status, ['aktif', 'terlambat', 'menunggu_kembali']) && !$this->isKodeExpired();
    }
}