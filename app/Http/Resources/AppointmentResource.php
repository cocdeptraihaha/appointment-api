<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'title' => $this->title,
            'type_id' => $this->type_id,
            'contact_id' => $this->contact_id,
            'staff_id' => $this->staff_id,
            'service_ids' => $this->whenLoaded('services', function () {
                return $this->services->pluck('id')->toArray();
            }),
            'start' => $this->start_time,
            'end' => $this->end_time,
        ];
    }
}
