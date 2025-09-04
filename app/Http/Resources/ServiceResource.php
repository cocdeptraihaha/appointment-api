<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'appointments_count' => $this->when(isset($this->appointments_count), $this->appointments_count),
            'staff_count' => $this->when(isset($this->staff_count), $this->staff_count),
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
            'staff' => StaffResource::collection($this->whenLoaded('staff')),
        ];
    }
}
