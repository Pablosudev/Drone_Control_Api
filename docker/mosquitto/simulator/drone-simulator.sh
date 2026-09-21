#!/bin/sh

sequence=1

while true; do
    sent_at=$(date -u +"%Y-%m-%dT%H:%M:%SZ")

    payload=$(printf '{"status":"flying","battery_percentage":80,"latitude":40.4168,"longitude":-3.7038,"sequence":%s,"sent_at":"%s"}' "$sequence" "$sent_at")

    mosquitto_pub \
    -h mqtt \
    -p 1883 \
    -t "drones/1/telemetry" \
    -m "$payload"

echo "Published: $payload"

    sequence=$((sequence + 1))

    sleep 5
done
