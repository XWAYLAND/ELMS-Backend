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
            'edisi'           => $this->edisi,
            'deskripsi_fisik' => $this->deskripsi_fisik,
            'bahasa'          => $this->bahasa,
            'cover'           => $this->cover,
            'jenis'           => new JenisResource($this->whenLoaded('jenis')),
            'penulis'         => new PenulisResource($this->whenLoaded('penulis')),
            'penerbit'        => new PenerbitResource($this->whenLoaded('penerbit')),
            'created_at'      => $this->created_at?->toDateTimeString(),
            'updated_at'      => $this->updated_at?->toDateTimeString(),
        ];
    }
}
