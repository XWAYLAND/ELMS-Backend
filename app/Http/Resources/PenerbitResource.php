<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenerbitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_penerbit'   => $this->id_penerbit,
            'nama_penerbit' => $this->nama_penerbit,
            'created_at'    => $this->created_at?->toDateTimeString(),
            'updated_at'    => $this->updated_at?->toDateTimeString(),
        ];
    }
}
