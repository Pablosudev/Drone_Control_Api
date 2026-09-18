<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Drone extends Model
{


    protected $fillable = [
        'name',
        'serial_number',
        'status',
        'battery_percentage',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'battery_percentage' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
            'last_telemetry_sequence' => 'integer',
            'last_telemetry_at' => 'datetime',
        ];
    }

    public function telemetries(): HasMany
    {
        return $this->hasMany(DroneTelemetry::class);
    }
}
