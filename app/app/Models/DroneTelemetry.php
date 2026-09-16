<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class DroneTelemetry extends Model
{
    protected $fillable =[
    'message_id',
    'sequence',
    'status',
    'battery_percentage',
    'latitude',
    'longitude',
    'observed_at',
    ];


    public function drone(): BelongsTo{
        return $this->belongsTo(Drone::class);
    }

    protected function casts(): array
    {
        return[
            'sequence' => 'integer',
            'battery_percentage' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
            'observed_at' => 'datetime',
        ];
    }
}
