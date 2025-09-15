<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentTypeResource extends JsonResource
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
            'label' => $this->label,
            'color' => $this->color,
            'deleted_at' => $this->deleted_at,
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
        ];
    }
}
