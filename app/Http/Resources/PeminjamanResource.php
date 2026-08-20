<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_transaksi'  => $this->id_transaksi,
            'waktu_pinjam'  => $this->waktu_pinjam?->toDateString(),
            'batas_kembali' => $this->batas_kembali?->toDateString(),
            'status'        => $this->status,
            'anggota'       => new AnggotaResource($this->whenLoaded('anggota')),
            'buku'          => new BukuResource($this->whenLoaded('buku')),
            'pegawai'       => new PegawaiResource($this->whenLoaded('pegawai')),
            'created_at'    => $this->created_at?->toDateTimeString(),
            'updated_at'    => $this->updated_at?->toDateTimeString(),
        ];
    }
}
