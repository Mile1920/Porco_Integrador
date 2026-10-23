<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\SensorData;
use App\Models\Incidente;
use App\Models\Alerta;
use App\Services\FirebaseService;

class SensorController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    // Receive sensor payloads. A configured device key is required before accepting telemetry.
    public function receive(Request $request)
    {
        $secret = (string) config('esp32.sensor_secret', '');
        $key = (string) $request->header('X-SENSOR-KEY', '');
        if ($secret === '') {
            return response()->json(['message' => 'Recepción de sensores no configurada'], 503);
        }
        if ($key === '' || !hash_equals($secret, $key)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (strlen($request->getContent()) > 16384) {
            return response()->json(['message' => 'El paquete supera el tamaño permitido'], 413);
        }

        $validator = Validator::make($request->all(), [
            'device_id' => 'required_without:sensor_id|string|max:100|regex:/\A[a-zA-Z0-9._:-]+\z/',
            'sensor_id' => 'required_without:device_id|string|max:100|regex:/\A[a-zA-Z0-9._:-]+\z/',
            'tipo' => 'required|string|max:50|alpha_dash',
            'payload' => 'nullable|array',
            'area_id' => 'nullable|integer|exists:areas,id',
            'area' => 'nullable|string|max:150',
            'alert' => 'sometimes|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Datos inválidos', 'errors' => $validator->errors()], 422);
        }

        $data = $request->except(['key']);
        $isBoundedValue = function ($value, int $depth = 0) use (&$isBoundedValue): bool {
            if (is_null($value) || is_bool($value) || is_int($value)) return true;
            if (is_float($value)) return is_finite($value);
            if (is_string($value)) return mb_strlen($value) <= 255;
            if (!is_array($value) || $depth >= 4 || count($value) > 50) return false;

            foreach ($value as $child) {
                if (!$isBoundedValue($child, $depth + 1)) return false;
            }
            return true;
        };
        if (!$isBoundedValue($data)) {
            return response()->json(['message' => 'La estructura o el tamaño de los datos no es válido'], 422);
        }

        $sensorId = $request->input('device_id') ?: $request->input('sensor_id');

        try {
            // Guardar en base de datos local
            $record = SensorData::create([
                'device_id' => $sensorId,
                'tipo' => $request->input('tipo'),
                'payload' => $data,
            ]);

            // Sincronizar con Firebase
            $firebaseSuccess = $this->firebaseService->receiveSensorData($sensorId, $data);

            if ($firebaseSuccess) {
                Log::info("Datos del sensor {$sensorId} sincronizados con Firebase");
            } else {
                Log::warning("Error sincronizando sensor {$sensorId} con Firebase");
            }

            // Si el payload trae un flag de alerta crítica, crear incidente automático
            if (isset($data['alert']) && $data['alert'] === true) {
                $incidente = Incidente::create([
                    'trabajador_id' => null,
                    'area_id' => $request->input('area_id'),
                    'descripcion' => 'Alerta generada por sensor: ' . json_encode($data),
                    'gravedad' => 'critica',
                    'estado' => 'pendiente',
                ]);

                Alerta::create([
                    'tipo' => $request->input('tipo', 'desconocido'),
                    'nivel' => 'critico',
                    'mensaje' => 'Alerta generada por sensor: ' . json_encode($data),
                    'origen' => $sensorId,
                    'area' => $request->input('area'),
                    'fecha_alerta' => now(),
                ]);

                Log::info("Incidente crítico creado automáticamente por sensor {$sensorId}, ID: {$incidente->id}");
            }

            return response()->json([
                'status' => 'ok',
                'firebase_sync' => $firebaseSuccess,
                'record_id' => $record->id
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error recibiendo datos sensor: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }

    /**
     * Mostrar listados de datos de sensores recientes (página de módulo).
     */
    public function index(Request $request)
    {
        $recent = SensorData::orderBy('created_at', 'desc')->limit(50)->get();

        $esp32Mac = config('esp32.mac', env('ESP32_MAC', '00:4B:12:35:3E:00'));
        $macClean = str_replace(':', '', $esp32Mac);
        $esp32Lecturas = SensorData::where(function ($q) use ($esp32Mac, $macClean) {
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

        $datos = $recent;

        return view('sensors.index', compact('datos', 'esp32Lecturas', 'esp32Ip', 'esp32Mac'));
    }
}
