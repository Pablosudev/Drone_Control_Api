# drone-control-api

Proyecto práctico de una API de control y telemetría de drones con Laravel, Docker, MySQL, Redis y MQTT. Implementa una arquitectura asíncrona orientada a sistemas en tiempo real, sin sobrearquitecturarla.

## Requisitos

- Docker Desktop (incluye Docker Compose).
- Git, si se va a clonar el repositorio.

## Instalación y arranque

1. Crea el archivo de configuración de Laravel:

   ```bash
   cp app/.env.example app/.env
   ```

2. En `app/.env`, configura la conexión interna entre contenedores:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=drone_control
   DB_USERNAME=laravel
   DB_PASSWORD=secret

   REDIS_HOST=redis
   REDIS_PORT=6379
   QUEUE_CONNECTION=redis

   MQTT_HOST=mqtt
   MQTT_PORT=1883
   MQTT_CLIENT_ID=drone-control-api-consumer
   ```

3. Construye y levanta los servicios:

   ```bash
   docker compose up -d --build
   ```

4. Genera la clave de Laravel y aplica las migraciones:

   ```bash
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate
   ```

5. Comprueba el estado de los contenedores:

   ```bash
   docker compose ps
   ```

La API queda disponible en `http://localhost:8000`.

Para detener los servicios:

```bash
docker compose down
```

Los datos de MySQL se conservan en el volumen `mysql_data`.

## Servicios Docker

| Servicio | Responsabilidad | Puerto publicado |
| --- | --- | --- |
| `app` | API Laravel/PHP | `8000` |
| `mysql` | Persistencia de drones y telemetría | No publicado al host |
| `redis` | Backend de Laravel Queue | No publicado al host |
| `worker` | Consume Jobs desde Redis | No publicado al host |
| `mqtt` | Broker Eclipse Mosquitto | `1883` |
| `simulator` | Publica telemetría MQTT de prueba | No publicado al host |

Los contenedores comparten la red interna de Docker Compose. Por eso Laravel usa `mysql` y `redis` como hosts internos; los futuros consumidores MQTT usarán `mqtt:1883`, nunca `localhost`.

## Tecnologías utilizadas

- **Laravel 13 / PHP 8.3**: API REST, validación, Eloquent, Resources, Jobs y Queue.
- **Docker Compose**: entorno de desarrollo reproducible y aislamiento de servicios.
- **MySQL 8**: estado actual de cada dron e histórico de telemetría.
- **Redis 7**: cola asíncrona de Laravel; desacopla la petición HTTP del procesamiento de telemetría.
- **Eclipse Mosquitto 2**: broker MQTT para comunicación publish/subscribe con dispositivos.
- **MQTT**: protocolo ligero para IoT. El simulador publica en el topic `drones/1/telemetry`.
- **php-mqtt/laravel-client**: cliente MQTT para que Laravel se conecte y se suscriba a Mosquitto.

## Uso actual

### API HTTP

Rutas disponibles:

```text
GET    /api/drones
POST   /api/drones
GET    /api/drones/{drone}
PATCH  /api/drones/{drone}
POST   /api/drones/{drone}/telemetry
GET    /api/drones/{drone}/telemetry
```

El endpoint de telemetría recibe un snapshot completo con:

```text
status, battery_percentage, latitude, longitude, sequence, sent_at
```

Una petición válida responde `202 Accepted`. Laravel valida el body, normaliza el payload y encola `ProcessDroneTelemetry` en Redis.

### Procesamiento de telemetría

El flujo HTTP actual es:

```text
POST HTTP
→ IngestDroneTelemetryRequest
→ DroneTelemetryController
→ TelemetryIngestionService
→ Redis Queue
→ worker
→ ProcessDroneTelemetry
→ MySQL
```

El Job procesa cada lectura dentro de una transacción y usa `lockForUpdate()` para evitar carreras entre workers. Mantiene dos representaciones del dato:

- `drone_telemetries`: histórico de lecturas únicas.
- `drones`: snapshot actual con estado, batería, ubicación, `last_telemetry_sequence` y `last_telemetry_at`.

La restricción única `(drone_id, sequence)` y la comprobación previa evitan duplicados. Una secuencia tardía se conserva en el histórico, pero no hace retroceder el snapshot actual.

### MQTT manual

Mosquitto escucha MQTT/TCP en el puerto `1883`. Para escuchar un topic manualmente:

```bash
docker compose exec mqtt mosquitto_sub \
  -h localhost \
  -p 1883 \
  -t 'drones/1/telemetry' \
  -v
```

El servicio `simulator` publica un snapshot cada cinco segundos en ese topic. Sus logs pueden verse con:

```bash
docker compose logs simulator --tail=20
```

Para detener temporalmente las publicaciones:

```bash
docker compose stop simulator
```

Laravel incluye el comando `mqtt:consume-drone-telemetry`, que se conecta a Mosquitto y se suscribe a `drones/+/telemetry`. En esta iteración inicial imprime los mensajes recibidos; todavía no los valida ni los encola.

Para ejecutarlo manualmente:

```bash
docker compose exec app php artisan mqtt:consume-drone-telemetry
```

El flujo MQTT actual es:

```text
simulator → Mosquitto → consumer Artisan manual → consola
```

## Estado actual y siguientes tareas

Implementado:

- Entorno Docker con Laravel, MySQL, Redis, worker, Mosquitto y simulador MQTT.
- Gestión básica de drones.
- Validación de telemetría HTTP mediante Form Request.
- Cola Redis y worker con reintentos configurados.
- Histórico, snapshot actual, transacciones, control de secuencia y deduplicación.
- API Resources para drones y telemetría paginada.
- Broker MQTT, publisher/simulator y subscriber manual verificados.
- `TelemetryIngestionService` para evitar duplicar la normalización y el encolado entre transportes.
- Paquete `php-mqtt/laravel-client` y comando Artisan subscriber MQTT inicial.

Pendiente:

- Completar el consumer MQTT: decodificar JSON, extraer el identificador del dron desde el topic, validar el payload y reutilizar `TelemetryIngestionService`.
- Ejecutar el consumer MQTT como servicio Docker de larga duración, separado de `app` y `worker`.
- Conectar `TelemetryRules` tanto al Form Request HTTP como al consumer MQTT para que las reglas no se dupliquen.
- Hacer la secuencia del simulador persistente o configurable tras reinicios.
- Autenticación y autorización para operadores y dispositivos.
- Completar operaciones de gestión de drones, según reglas de negocio (por ejemplo, borrado lógico).
- Tests automatizados del Job, del flujo HTTP y del futuro consumer MQTT.
- Observabilidad de workers, gestión de `failed_jobs` y políticas de reintento.
- Cache de estado actual, broadcasting/WebSockets y frontend React en fases posteriores.
- Nginx, credenciales seguras y configuración cercana a producción.

## Nota de seguridad

Las credenciales de Compose y `allow_anonymous true` de Mosquitto son exclusivamente para desarrollo local. No deben utilizarse en producción.
