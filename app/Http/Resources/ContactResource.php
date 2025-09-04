<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'appointments_count' => $this->when(isset($this->appointments_count), $this->appointments_count),
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
        ];
    }
}
