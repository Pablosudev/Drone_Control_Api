<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Drone;
use App\Http\Requests\StoreDroneRequest;
use App\Http\Requests\UpdateDroneRequest;
use App\Http\Resources\DroneResource;


class DroneController extends Controller
{
    
    public function index(){
        $drones = Drone::query()->get();
        return DroneResource::collection($drones);
    }

    public function store(StoreDroneRequest $request){
        $drone = Drone::create($request->validated());
        $drone->refresh();
        return (new DroneResource($drone))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Drone $drone){
        return new DroneResource($drone);
    }

    public function update(UpdateDroneRequest $request, Drone $drone){
        $drone->update($request->validated());
        return (new DroneResource($drone));
    }


}
