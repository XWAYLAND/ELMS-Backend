<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_transaksi'         => $this->id_transaksi,
            'nis'                  => $this->nis,
            'isbn'                 => $this->isbn,
            'id_pegawai'           => $this->id_pegawai,
            'kode_unik'            => $this->kode_unik,
            'kode_unik_expires_at' => $this->kode_unik_expires_at?->toDateTimeString(),
            'is_kode_expired'      => $this->isKodeExpired(),
            'waktu_pengajuan'      => $this->waktu_pengajuan?->toDateTimeString(),
            'batas_waktu'          => $this->batas_waktu?->toDateTimeString(),
            'durasi_hari'          => $this->durasi_hari,
            'tanggal_kembali'      => $this->tanggal_kembali?->toDateTimeString(),
            'status'               => $this->status,
            'sisa_waktu'           => $this->sisa_waktu,
            'is_terlambat'         => $this->is_terlambat,
            'can_be_approved'      => $this->canBeApproved(),
            'can_be_returned'      => $this->canBeReturned(),
            'anggota'              => new AnggotaResource($this->whenLoaded('anggota')),
            'buku'                 => new BukuResource($this->whenLoaded('buku')),
            'pegawai'              => new PegawaiResource($this->whenLoaded('pegawai')),
            'created_at'           => $this->created_at?->toDateTimeString(),
            'updated_at'           => $this->updated_at?->toDateTimeString(),
        ];
    }
}
