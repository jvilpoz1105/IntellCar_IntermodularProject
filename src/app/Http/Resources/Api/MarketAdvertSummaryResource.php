<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketAdvertSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'advert_id' => $this->ad_id,
            'ad_title' => $this->ad_title,
            'price' => $this->price,
            'kilometers' => $this->kilometers,
            'year_manufacture' => $this->year_manufacture,
            'region' => $this->region,
            'city' => $this->city,
            // Si el modelo y la marca están cargados, los simplificamos
            'make_name' => $this->whenLoaded('model', fn() => $this->model?->make?->make_name),
            'model_name' => $this->whenLoaded('model', fn() => $this->model?->model_name),
            // Solo mandamos la primera imagen para el listado
            'media' => $this->whenLoaded('media', fn() => $this->media->first()),
            'created_at' => $this->created_at,
        ];
    }
}
