<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IngestDroneTelemetryRequest;
use App\Models\Drone;
use App\Http\Resources\DroneTelemetryResource;
use App\Jobs\ProcessDroneTelemetry;
use Illuminate\Support\Str;

class DroneTelemetryController extends Controller
{


    public function index(Drone $drone){
        $telemetries = $drone->telemetries()
            ->orderByDesc('observed_at')
            ->paginate(20);
        return DroneTelemetryResource::collection($telemetries);
    }
    public function store(IngestDroneTelemetryRequest $request, Drone $drone){

        $telemetry = $request->validated();
        $telemetryForJob = [
            'message_id' => (string) Str::uuid(),
            'sequence' => $telemetry['sequence'],
            'status' => $telemetry['status'],
            'battery_percentage' => $telemetry['battery_percentage'],
            'latitude' => $telemetry['latitude'],
            'longitude' => $telemetry['longitude'],
            'observed_at' => $telemetry['sent_at']

        ];
        ProcessDroneTelemetry::dispatch($drone, $telemetryForJob);

        return response()->json([
            'message' => 'Telemetría aceptada para procesarse',
            'drone_id' => $drone->id,
        ],202);
    }    
}
