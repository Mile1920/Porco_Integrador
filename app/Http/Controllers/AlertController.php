<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Trabajador;
use App\Models\Alerta;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;
use App\Models\SensorData;

class AlertController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        $alerts = [];

        // 1. Alertas de Firebase (sensores IoT)
        $sensorData = $this->firebaseService->getSensorData();
        if ($sensorData) {
            foreach ($sensorData as $sensorId => $sensor) {
                if (isset($sensor['alertas']) && is_array($sensor['alertas'])) {
                    foreach ($sensor['alertas'] as $alert) {
                        $alerts[] = $this->makeAlert(
                            $sensorId, $sensor['tipo'] ?? 'desconocido',
                            $sensor['area'] ?? 'Sin área',
                            $alert['mensaje'] ?? 'Alerta detectada',
                            $alert['nivel'] ?? 'medio',
                            $alert['timestamp'] ?? now()->toISOString()
                        );
                    }
                }
                $alerts = array_merge($alerts, $this->checkSensorThresholds($sensorId, $sensor));
            }
        }

        // 2. Alertas de trabajadores con ingreso sin salida (>8 horas)
        $limite = now()->subHours(8);
        $ingresosSinSalida = Ingreso::where('tipo', 'ingreso')
            ->where('registrado_en', '<=', $limite)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('ingresos', 'salidas')
                    ->whereColumn('salidas.trabajador_id', 'ingresos.trabajador_id')
                    ->where('salidas.tipo', 'salida')
                    ->whereRaw('salidas.registrado_en > ingresos.registrado_en');
            })
            ->with('trabajador')
            ->get();

        foreach ($ingresosSinSalida as $ingreso) {
            $trabajador = $ingreso->trabajador;
            if (!$trabajador) continue;

            $horas = now()->diffInHours($ingreso->registrado_en);
            $areaNombre = $trabajador->area?->nombre ?? 'Sin área';
            $horaIngreso = $ingreso->registrado_en ? $ingreso->registrado_en->format('H:i') : '--:--';
            $timestamp = $ingreso->registrado_en ? $ingreso->registrado_en->toISOString() : now()->toISOString();
            $alerts[] = $this->makeAlert(
                'TRABAJADOR-' . $trabajador->id,
                'salida_pendiente',
                $areaNombre,
                "{$trabajador->nombre_completo} lleva {$horas}h sin registrar salida (ingreso: {$horaIngreso})",
                $horas >= 12 ? 'critico' : 'alto',
                $timestamp
            );
        }

        // 3. Alertas de gases ESP32 (lecturas con alertas activas en los últimos 10 min)
        $ultimasLecturas = SensorData::where('device_id', 'esp32_gases_01')
            ->where('created_at', '>=', now()->subMinutes(10))
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        foreach ($ultimasLecturas as $lectura) {
            $payload = $lectura->payload;
            if (!is_array($payload)) continue;

            $alertaActiva = $payload['alerta'] ?? false;
            $mq7 = $payload['mq7_co'] ?? 0;
            $mq135 = $payload['mq135_aire'] ?? 0;

            if ($alertaActiva || $mq7 > 2200 || $mq135 > 2850) {
                $alerts[] = $this->makeAlert(
                    'ESP32-GASES',
                    'gases_toxicos',
                    'Mina Subterránea',
                    ($alertaActiva ? '⚠️ ' : '') . "MQ-7: {$mq7} | MQ-135: {$mq135} — Niveles peligrosos detectados",
                    $mq7 > 2600 || $mq135 > 3200 ? 'critico' : 'alto',
                    $lectura->created_at->toISOString()
                );
            }
        }

        // 4. Alertas persistentes desde la tabla alertas
        $alertasDb = Alerta::orderBy('fecha_alerta', 'desc')->limit(50)->get();
        foreach ($alertasDb as $a) {
            $alerts[] = $this->makeAlert(
                $a->origen,
                $a->tipo,
                $a->area ?? 'Sin área',
                $a->mensaje,
                $a->nivel,
                $a->fecha_alerta->toISOString()
            );
        }

        // 5. Ordenar: más recientes primero
        usort($alerts, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        $alerts = array_slice($alerts, 0, 100);

        return view('alerts.index', compact('alerts'));
    }

    private function makeAlert($sensor, $tipo, $area, $mensaje, $nivel, $timestamp)
    {
        return compact('sensor', 'tipo', 'area', 'mensaje', 'nivel', 'timestamp');
    }

    private function checkSensorThresholds($sensorId, $sensor)
    {
        $alerts = [];
        if (!isset($sensor['tipo'])) return $alerts;

        switch ($sensor['tipo']) {
            case 'gases_toxicos':
                if (($sensor['co'] ?? 0) > 50) {
                    $alerts[] = $this->makeAlert($sensorId, 'gases_toxicos', $sensor['area'] ?? 'Sin área',
                        'Nivel de CO crítico: ' . ($sensor['co'] ?? 0) . ' ppm', 'critico', now()->toISOString());
                }
                break;
            case 'movimiento_tierra':
                if (($sensor['movimiento'] ?? 0) > 5) {
                    $alerts[] = $this->makeAlert($sensorId, 'movimiento_tierra', $sensor['area'] ?? 'Sin área',
                        'Movimiento de tierra excesivo: ' . ($sensor['movimiento'] ?? 0) . ' mm', 'critico', now()->toISOString());
                }
                break;
            case 'signos_vitales':
                $hr = $sensor['frecuencia_cardiaca'] ?? 0;
                if ($hr > 120 || ($hr < 50 && $hr > 0)) {
                    $alerts[] = $this->makeAlert($sensorId, 'signos_vitales', $sensor['area'] ?? 'Sin área',
                        'Frecuencia cardíaca anormal: ' . $hr . ' bpm', 'critico', now()->toISOString());
                }
                break;
        }
        return $alerts;
    }
}
