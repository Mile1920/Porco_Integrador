<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Ingreso;
use App\Models\SensorData;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlertaController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function ultimas(Request $request)
    {
        $ultimoId = $request->integer('desde_id', 0);
        $soloNoLeidas = $request->boolean('solo_no_leidas');
        $soloCriticas = $request->boolean('solo_criticas');

        $alertas = collect();

        // 1. Alertas desde la tabla alertas
        $query = Alerta::orderBy('id', 'desc')->limit(20);

        if ($soloNoLeidas) {
            $query->where('leida', false);
        }

        if ($soloCriticas) {
            $query->whereIn('nivel', ['critico', 'alto']);
        }

        if (!$soloNoLeidas && !$soloCriticas && $ultimoId > 0) {
            $query->where('id', '>', $ultimoId);
        }

        foreach ($query->get() as $a) {
            $alertas->push([
                'id' => $a->id,
                'tipo' => $a->tipo,
                'nivel' => $a->nivel,
                'mensaje' => $a->mensaje,
                'origen' => $a->origen,
                'area' => $a->area,
                'fecha' => $a->fecha_alerta->toIso8601String(),
                'fuente' => 'alertas',
            ]);
        }

        // 2. Alertas desde sensor_data (ESP32 - últimos 10 min)
        $ultimasLecturas = SensorData::where('device_id', 'esp32_gases_01')
            ->where('created_at', '>=', now()->subMinutes(10))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($ultimasLecturas as $lectura) {
            $payload = $lectura->payload;
            if (!is_array($payload)) continue;

            $alertaActiva = $payload['alerta'] ?? false;
            $mq7 = $payload['mq7_co'] ?? 0;
            $mq135 = $payload['mq135_aire'] ?? 0;

            if ($alertaActiva || $mq7 > 2200 || $mq135 > 2850) {
                $nivel = ($mq7 > 2600 || $mq135 > 3200) ? 'critico' : 'alto';
                $alertas->push([
                    'id' => 'sensor_' . $lectura->id,
                    'tipo' => 'gases_toxicos',
                    'nivel' => $nivel,
                    'mensaje' => "MQ-7: {$mq7} ppm | MQ-135: {$mq135} ppm — " . ($nivel === 'critico' ? '¡Peligro inminente!' : 'Niveles elevados de gas'),
                    'origen' => $lectura->device_id,
                    'area' => 'Mina Subterránea',
                    'fecha' => $lectura->created_at->toIso8601String(),
                    'fuente' => 'sensor_data',
                ]);
            }
        }

        // Ordenar por fecha descendente
        $alertas = $alertas->sortByDesc('fecha')->values();

        return response()->json([
            'alertas' => $alertas,
            'hay_nuevas' => $alertas->isNotEmpty(),
            'ultimo_id' => Alerta::max('id') ?? 0,
        ]);
    }

    public function marcarLeidas()
    {
        Alerta::where('leida', false)->update(['leida' => true]);

        return response()->json(['success' => true]);
    }
}
