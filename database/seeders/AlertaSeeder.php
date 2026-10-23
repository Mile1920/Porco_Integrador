<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alerta;

class AlertaSeeder extends Seeder
{
    public function run(): void
    {
        $alertas = [
            [
                'tipo' => 'gases_toxicos',
                'nivel' => 'critico',
                'mensaje' => 'MQ-7: 2450 ppm | MQ-135: 3200 ppm — ¡Peligro inminente! Niveles de CO y gases tóxicos superan el límite seguro.',
                'origen' => 'ESP32-GASES',
                'area' => 'Mina Subterránea',
                'leida' => false,
                'fecha_alerta' => now()->subMinutes(5),
            ],
            [
                'tipo' => 'gases_toxicos',
                'nivel' => 'alto',
                'mensaje' => 'MQ-7: 1800 ppm | MQ-135: 2600 ppm — Niveles elevados de gas detectados en el sector este.',
                'origen' => 'ESP32-GASES',
                'area' => 'Mina Subterránea',
                'leida' => false,
                'fecha_alerta' => now()->subMinutes(15),
            ],
            [
                'tipo' => 'movimiento_tierra',
                'nivel' => 'critico',
                'mensaje' => 'Movimiento de tierra excesivo: 12 mm detectado en el pilar central. Riesgo de derrumbe.',
                'origen' => 'SENSOR-GEOTECNIA-01',
                'area' => 'Sector C - Profundidad 200m',
                'leida' => false,
                'fecha_alerta' => now()->subMinutes(30),
            ],
            [
                'tipo' => 'salida_pendiente',
                'nivel' => 'alto',
                'mensaje' => 'Carlos Mamani lleva 10 horas sin registrar salida (ingreso: 06:30).',
                'origen' => 'TRABAJADOR-5',
                'area' => 'Extracción',
                'leida' => false,
                'fecha_alerta' => now()->subHours(2),
            ],
            [
                'tipo' => 'signos_vitales',
                'nivel' => 'critico',
                'mensaje' => 'Frecuencia cardíaca anormal: 145 bpm detectada en trabajador del sector B.',
                'origen' => 'SENSOR-SIGNOS-03',
                'area' => 'Sector B',
                'leida' => false,
                'fecha_alerta' => now()->subMinutes(45),
            ],
            [
                'tipo' => 'gases_toxicos',
                'nivel' => 'medio',
                'mensaje' => 'MQ-7: 1200 ppm — Nivel moderado de CO. Monitoreo continuo recomendado.',
                'origen' => 'ESP32-GASES',
                'area' => 'Mina Subterránea',
                'leida' => true,
                'fecha_alerta' => now()->subHours(5),
            ],
        ];

        foreach ($alertas as $alerta) {
            Alerta::create($alerta);
        }

        $this->command->info('Alertas de demostración insertadas correctamente.');
    }
}
