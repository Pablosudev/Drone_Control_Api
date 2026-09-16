<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Drone;
use App\Http\Requests\UpdateDroneTelemetryRequest;
use App\Jobs\ProcessDroneTelemetry;
use App\Http\Resources\DroneTelemetryResource;

class DroneTelemetryController extends Controller
{


    public function index(Drone $drone){
        $telemetries = $drone->telemetries()
            ->orderByDesc('observed_at')
            ->paginate(20);
        return DroneTelemetryResource::collection($telemetries);
    }
    public function store(UpdateDroneTelemetryRequest $request, Drone $drone){

        ProcessDroneTelemetry::dispatch($drone, $request->validated());

        return response()->json([
            'message' => 'Telemetría aceptada para procesar',
        ], 202);
    }

    
}
