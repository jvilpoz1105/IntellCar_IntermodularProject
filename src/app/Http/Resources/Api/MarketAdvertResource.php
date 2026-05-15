<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketAdvertResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // parent::toArray() inyecta todos los campos y también las 
        // relaciones si las hemos cargado previamente con el with()
        return parent::toArray($request);
    }
}
