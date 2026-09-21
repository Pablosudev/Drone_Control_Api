<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IngestDroneTelemetryRequest;
use App\Models\Drone;
use App\Http\Resources\DroneTelemetryResource;
use App\Services\TelemetryIngestionService;

class DroneTelemetryController extends Controller
{

    public function index(Drone $drone){
        $telemetries = $drone->telemetries()
            ->orderByDesc('observed_at')
            ->paginate(20);
        return DroneTelemetryResource::collection($telemetries);
    }
    public function store(IngestDroneTelemetryRequest $request, Drone $drone, TelemetryIngestionService $telemetryIngestionService){

       $telemetryIngestionService->ingest($drone, $request->validated());

        return response()->json([
            'message' => 'Telemetría aceptada para procesarse',
            'drone_id' => $drone->id,
        ],202);
    }    
}
