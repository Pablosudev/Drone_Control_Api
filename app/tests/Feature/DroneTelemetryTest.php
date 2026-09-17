<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Models\Drone;
use App\Jobs\ProcessDroneTelemetry;

class DroneTelemetryTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_telemetry_is_queued_for_an_existing_drone(): void
    {

        Queue::fake();
        $drone = Drone::create([
            'name' => 'TEST DRONE',
            'serial_number' => 'TestDrone --002'
        ]);

        $response = $this->postJson(
            "/api/drones/{$drone->id}/telemetry",
            [
                'message_id' => '5d3c1e8b-f4a8-4c1a-91de-99f43d6f3a21',
                'sequence' => 1,
                'status' => 'flying',
                'battery_percentage' => 85,
                'latitude' => 40.4168,
                'longitude' => -3.7038,
                'observed_at' => '2026-09-17T10:00:00Z',
            ]
        );

        $response->assertAccepted();
        Queue::assertPushed(
            ProcessDroneTelemetry::class,
            function (ProcessDroneTelemetry $job) use ($drone) {
                return $job->drone->is($drone) && $job->telemetry['sequence'] === 1;
            }
        );
    }


    public function test_invalid_telemetry_is_not_queued(): void
    {
        Queue::fake();
        $drone = Drone::create([
            'name' => 'TEST DRONE',
            'serial_number' => 'TestDrone --001'
        ]);
        $response = $this->postJson(
            "/api/drones/{$drone->id}/telemetry",
            [
                'message_id' => '5d3c1e8b-f4a8-4c1a-91de-99f43d6f3a21',
                'sequence' => 1,
                'status' => 'flying',
                'battery_percentage' => 150,
                'latitude' => 40.4168,
                'longitude' => -3.7038,
                'observed_at' => '2026-09-17T10:00:00Z',
            ]
        );
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['battery_percentage']);
        Queue::assertNothingPushed();
    
    }
}
