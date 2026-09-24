<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;
use App\Models\Drone;
use JsonException;
use App\Support\TelemetryRules;
use Illuminate\Support\Facades\Validator;
use App\Services\TelemetryIngestionService;

#[Signature('mqtt:consume-drone-telemetry')]
#[Description('Consume telemetry messages from MQTT.')]
class ConsumeDroneTelemetry extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(TelemetryIngestionService $telemetryIngestionService)
    {
        $mqtt = MQTT::connection();

        $this->info('MQTT consumer listening on drones/+/telemetry');

        $mqtt->subscribe(
            'drones/+/telemetry',
            function (string $topic, string $message) use ($telemetryIngestionService): void {
                $this->line("Received on {$topic}: {$message}");
                if (!preg_match('#^drones/(\d+)/telemetry$#', $topic, $matches)) {
                    $this->error("Invalid telemetry topic: {$topic}");

                    return;
                } 
                $droneId = (int) $matches[1];
                try {                
                    $telemetry = json_decode(
                        $message,
                        true,
                        512,
                        JSON_THROW_ON_ERROR,
                    );

                    if (!is_array($telemetry)) {
                        $this->error("Telemetry payload must be a JSON object on {$topic}");

                        return;
                    }
                    $validator = Validator::make($telemetry, TelemetryRules::ingest());
                    if ($validator->fails()) {
                        $this->error(
                            'Invalid telemetry payload: '
                                . json_encode($validator->errors()->toArray()),
                        );

                        return;
                    }
                    $validatedTelemetry = $validator->validated();

                    $this->line(
                        'Validated telemetry: ' . json_encode($validatedTelemetry),
                    );

                    $drone = Drone::find($droneId);
                    if (!$drone) {
                        $this->error("Drone not found for telemetry topic: {$topic}");

                        return;
                    }
                    $telemetryIngestionService->ingest($drone, $validatedTelemetry);
                } catch (JsonException $exception) {
                    $this->error(
                        "Invalid JSON on {$topic}: {$exception->getMessage()}",
                    );
                }
            },
            0,
        );

        $mqtt->loop(true);
    }
}
