<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DroneTelemetryResource extends JsonResource
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
            'sequence' => $this->sequence,
            'status' => $this->status,
            'battery_percentage' => $this->battery_percentage,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'observed_at' => $this->observed_at
        ];
    }
}
