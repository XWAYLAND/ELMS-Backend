<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BukuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'isbn'            => $this->isbn,
            'judul'           => $this->judul,
            'slug'            => $this->slug,
            'cover'           => $this->cover,
            'cover_url'       => $this->cover_url,
            'edisi'           => $this->edisi,
            'deskripsi_fisik' => $this->deskripsi_fisik,
            'bahasa'          => $this->bahasa,
            'tersedia'        => (bool) $this->tersedia,
            'id_jenis'        => $this->id_jenis,
            'jenis'           => new JenisResource($this->whenLoaded('jenis')),
            'penulis'         => $this->penulis,
            'penerbit'        => $this->penerbit,
            'created_at'      => $this->created_at?->toDateTimeString(),
            'updated_at'      => $this->updated_at?->toDateTimeString(),
        ];
    }
}
