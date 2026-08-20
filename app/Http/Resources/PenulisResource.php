<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenulisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_penulis'   => $this->id_penulis,
            'nama_penulis' => $this->nama_penulis,
            'created_at'   => $this->created_at?->toDateTimeString(),
            'updated_at'   => $this->updated_at?->toDateTimeString(),
        ];
    }
}
