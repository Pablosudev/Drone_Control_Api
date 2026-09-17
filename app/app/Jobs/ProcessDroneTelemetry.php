<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use App\Models\Drone;

class ProcessDroneTelemetry implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Drone $drone, public array $telemetry)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // throw new \RuntimeException('Fallo de prueba de telemetría');
        DB::transaction(function () {
            $drone = Drone::query()
                ->lockForUpdate()
                ->findOrFail($this->drone->getKey());
            if (
                $drone->last_telemetry_sequence !== null
                && $this->telemetry['sequence'] <= $drone->last_telemetry_sequence
            ) {
                return;
            }
            $drone->telemetries()->create($this->telemetry);

            $drone->forceFill([
                'status' => $this->telemetry['status'],
                'battery_percentage' => $this->telemetry['battery_percentage'],
                'latitude' => $this->telemetry['latitude'],
                'longitude' => $this->telemetry['longitude'],
                'last_telemetry_sequence' => $this->telemetry['sequence'],
            ])->save();
        });
    }
}
