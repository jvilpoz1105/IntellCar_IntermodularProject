<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnivPostSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'post_id' => $this->post_id ?? $this->id,
            'title' => $this->title,
            // Devolvemos solo un resumen del contenido para no saturar la red
            'content_excerpt' => substr($this->content, 0, 100) . (strlen($this->content) > 100 ? '...' : ''),
            'author' => $this->whenLoaded('author', function() {
                return [
                    'user_id' => $this->author->user_id ?? $this->author->id,
                    'username' => $this->author->username,
                    'profile_picture' => $this->author->profile_picture,
                ];
            }),
            'media' => $this->whenLoaded('media', fn() => $this->media->first()),
            'created_at' => $this->created_at,
        ];
    }
}
