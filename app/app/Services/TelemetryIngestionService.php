<?php
namespace App\Services;

use App\Models\Drone;
use App\Jobs\ProcessDroneTelemetry;
use Illuminate\Support\Str;

class TelemetryIngestionService {

    public function ingest(Drone $drone, array $telemetry) : void {


        $telemetryForJob = [
            'message_id' => (string) Str::uuid(),
            'sequence' => $telemetry['sequence'],
            'status' => $telemetry['status'],
            'battery_percentage' => $telemetry['battery_percentage'],
            'latitude' => $telemetry['latitude'],
            'longitude' => $telemetry['longitude'],
            'observed_at' => $telemetry['sent_at']
        ];
        
        ProcessDroneTelemetry::dispatch($drone , $telemetryForJob);
    }

}