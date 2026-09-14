<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnggotaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'nis'          => $this->nis,
            'nama_lengkap' => $this->nama_lengkap,
            'kelas'        => $this->kelas,
            'inisial'      => $this->inisial,
            'created_at'   => $this->created_at?->toDateTimeString(),
            'updated_at'   => $this->updated_at?->toDateTimeString(),
        ];
    }
}
