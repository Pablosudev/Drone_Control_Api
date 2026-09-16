<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DroneResource extends JsonResource
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
            'serial_number' => $this->serial_number,
            'status' => $this->status,
            'battery_percentage' => $this->battery_percentage,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'last_telemetry_sequence' => $this->last_telemetry_sequence
        ];
    }
}
