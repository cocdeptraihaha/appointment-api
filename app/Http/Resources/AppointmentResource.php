<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->start_time,
            'end' => $this->end_time,
            
            // Related data loaded in single fetch
            'appointment_type' => $this->whenLoaded('appointment_type', function () {
                return [
                    'id' => $this->appointment_type->id,
                    'label' => $this->appointment_type->label,
                    'color' => $this->appointment_type->color,
                ];
            }),
            
            'contact' => $this->whenLoaded('contact', function () {
                return [
                    'id' => $this->contact->id,
                    'name' => trim($this->contact->first_name . ' ' . $this->contact->last_name),
                    'avatar' => $this->contact->avatar,
                ];
            }),
            
            'staff' => $this->whenLoaded('staff', function () {
                return [
                    'id' => $this->staff->id,
                    'name' => $this->staff->name,
                    'avatar' => $this->staff->avatar,
                ];
            }),
            
            'services' => $this->whenLoaded('services', function () {
                return $this->services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                    ];
                });
            }),
        ];
    }
}
