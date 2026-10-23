<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class FirebaseSensorController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Mostrar dashboard de sensores Firebase
     */
    public function index()
    {
        $sensorData = $this->firebaseService->getSensorData();
        $activeSensors = $this->firebaseService->getActiveSensors();

        // Estadísticas
        $totalSensors = count($activeSensors);
        $totalReadings = 0;
        $alerts = 0;

        if ($sensorData) {
            foreach ($sensorData as $sensorId => $sensor) {
                if (isset($sensor['lecturas'])) {
                    $totalReadings += count($sensor['lecturas']);
                }
                if (isset($sensor['alertas'])) {
                    $alerts += count($sensor['alertas']);
                }
            }
        }

        return view('firebase-sensors.index', compact(
            'sensorData',
            'activeSensors',
            'totalSensors',
            'totalReadings',
            'alerts'
        ));
    }

    /**
     * Mostrar detalles de un sensor específico
     */
    public function show($sensorId)
    {
        $sensor = $this->firebaseService->getSensorById($sensorId);
        $recentReadings = $this->firebaseService->getRecentReadings($sensorId);

        if (!$sensor) {
            abort(404, 'Sensor no encontrado');
        }

        return view('firebase-sensors.show', compact('sensor', 'sensorId', 'recentReadings'));
    }

    /**
     * API endpoint para obtener datos de sensores
     */
    public function apiData(Request $request): JsonResponse
    {
        $sensorData = $this->firebaseService->getSensorData();

        return response()->json([
            'success' => true,
            'data' => $sensorData,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Dashboard de sensores para admins
     */
    public function dashboard()
    {
        $sensorData = $this->firebaseService->getSensorData();
        $activeSensorsData = $this->firebaseService->getActiveSensors();
        $activeSensors = array_keys($activeSensorsData);

        // Estadísticas
        $totalSensors = count($activeSensors);
        $totalReadings = 0;
        $alerts = 0;
        $avgTemperature = null;
        $avgHumidity = null;
        $temperatureCount = 0;
        $humidityCount = 0;
        $temperatureTotal = 0;
        $humidityTotal = 0;

        if ($sensorData) {
            foreach ($sensorData as $sensorId => $sensor) {
                if (isset($sensor['lecturas'])) {
                    $totalReadings += count($sensor['lecturas']);
                    foreach ($sensor['lecturas'] as $reading) {
                        if (isset($reading['temperatura'])) {
                            $temperatureTotal += (float) $reading['temperatura'];
                            $temperatureCount++;
                        }
                        if (isset($reading['humedad'])) {
                            $humidityTotal += (float) $reading['humedad'];
                            $humidityCount++;
                        }
                    }
                }
                if (isset($sensor['alertas'])) {
                    $alerts += count($sensor['alertas']);
                }
            }
        }

        if ($temperatureCount > 0) $avgTemperature = $temperatureTotal / $temperatureCount;
        if ($humidityCount > 0) $avgHumidity = $humidityTotal / $humidityCount;

        $esp32Mac = config('esp32.mac', env('ESP32_MAC', '00:4B:12:35:3E:00'));
        $macClean = str_replace(':', '', $esp32Mac);
        $esp32Lecturas = \App\Models\SensorData::where(function ($q) use ($esp32Mac, $macClean) {
                $q->where('device_id', 'esp32_001')
                  ->orWhere('device_id', 'esp32_gases_01')
                  ->orWhere('device_id', $esp32Mac)
                  ->orWhere('device_id', 'ESP32-' . $macClean)
                  ->orWhere('device_id', 'like', 'esp32_%')
                  ->orWhere('device_id', 'like', 'ESP32%');
            })
            ->where('device_id', '!=', 'esp32_config')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        $esp32Ip = config('esp32.ip', env('ESP32_IP', '192.168.1.205'));

        $localSensores = \App\Models\Sensor::with('area')
            ->orderBy('created_at', 'desc')
            ->get();

        $areas = \App\Models\Area::orderBy('nombre')->get();

        return view('sensor-dashboard', compact(
            'sensorData',
            'activeSensors',
            'totalSensors',
            'totalReadings',
            'alerts',
            'avgTemperature',
            'avgHumidity',
            'esp32Lecturas',
            'esp32Ip',
            'esp32Mac',
            'localSensores',
            'areas'
        ));
    }

    /**
     * Mostrar formulario para crear sensor
     */
    public function create()
    {
        return view('firebase-sensors.create');
    }

    /**
     * Guardar nuevo sensor en Firebase
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'sensor_id' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('sensors', 'device_id')],
            'tipo' => 'required|in:movimiento_tierra,gases_toxicos,signos_vitales',
            'area_id' => 'required|integer|exists:areas,id',
        ]);

        $area = \App\Models\Area::findOrFail($data['area_id']);

        $sensorData = [
            'activo' => true,
            'tipo' => $data['tipo'],
            'area' => $area->nombre,
            'lecturas' => [],
        ];

        // Registrar únicamente metadatos; las mediciones llegan del dispositivo real.
        $localSensor = Sensor::create([
            'device_id' => $data['sensor_id'],
            'nombre' => $data['sensor_id'],
            'area_id' => $area->id,
            'activo' => true,
            'estado' => 'activo',
        ]);

        // Crear el registro remoto sin fabricar una lectura.
        $firebaseSuccess = $this->firebaseService->sendSensorData($data['sensor_id'], $sensorData);

        $message = 'Sensor creado exitosamente en el sistema local';
        $type = 'success';

        if ($firebaseSuccess) {
            $message = 'Sensor registrado en Firebase y en el sistema local; aún no tiene lecturas recibidas.';
        } else {
            $message .= ' (Firebase no disponible, funciona solo localmente)';
            $type = 'warning';
        }

        // Registrar en auditoría
        \App\Helpers\AuditLogger::log(
            'CREAR_SENSOR',
            'sensors',
            $localSensor->id,
            "Sensor {$data['sensor_id']} de tipo {$data['tipo']} registrado en área {$area->nombre}"
        );

        return redirect()->route('sensor-dashboard')->with($type, $message);
    }

    /**
     * API endpoint para datos de un sensor específico
     */
    public function apiSensorData($sensorId): JsonResponse
    {
        $sensor = $this->firebaseService->getSensorById($sensorId);
        $readings = $this->firebaseService->getRecentReadings($sensorId);

        return response()->json([
            'success' => true,
            'sensor' => $sensor,
            'readings' => $readings,
            'timestamp' => now()->toISOString()
        ]);
    }
}
