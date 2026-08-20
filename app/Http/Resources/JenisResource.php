<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JenisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_jenis'   => $this->id_jenis,
            'nama_jenis' => $this->nama_jenis,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
