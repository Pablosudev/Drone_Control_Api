<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Drone;
use App\Http\Requests\StoreDroneRequest;
use App\Http\Requests\UpdateDroneRequest;



class DroneController extends Controller
{
    
    public function index(){
        return Drone::query()->get();
    }

    public function store(StoreDroneRequest $request){
        $drone = Drone::create($request->validated());
        $drone->refresh();
        return response()->json($drone,201);
    }

    public function show(Drone $drone){
        return $drone;
    }

    public function update(UpdateDroneRequest $request, Drone $drone){
        $drone->update($request->validated());
        return response()->json($drone);
    }



}
