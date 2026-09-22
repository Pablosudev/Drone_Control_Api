<?php

namespace App\Support;


class TelemetryRules
{

    public static function ingest(): array
    {
        return [
            'status' => ['required', 'string', 'in:available,flying,charging,offline'],
            'battery_percentage' => ['required', 'integer', 'between:0,100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'sequence' => ['required', 'integer', 'min:0'],
            'sent_at' => ['required', 'date']
        ];
    }
}
