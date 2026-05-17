<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KddEventSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userId = $request->user('sanctum')?->user_id;

        return [
            'event_id' => $this->event_id ?? $this->id,
            'title' => $this->title,
            'event_date' => $this->event_date,
            'location_name' => $this->location_name,
            'city' => $this->city,
            'creator' => $this->whenLoaded('creator', function() {
                return [
                    'user_id' => $this->creator->user_id ?? $this->creator->id,
                    'username' => $this->creator->username,
                    'profile_picture' => $this->creator->profile_picture,
                ];
            }),
            'max_participants' => $this->max_participants,
            'attendees_count' => $this->whenLoaded('attendees', fn() => $this->attendees->count(), fn() => $this->attendees()->count()),
            'is_attending' => $userId ? $this->whenLoaded('attendees', fn() => $this->attendees->contains('user_id', $userId), fn() => $this->attendees()->where('app_user.user_id', $userId)->exists()) : false,
            'created_at' => $this->created_at,
        ];
    }
}
