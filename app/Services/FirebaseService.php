<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirebaseService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('firebase.database_url');
    }

    /**
     * Obtener datos de sensores usando REST API
     */
    public function getSensorData($path = 'sensores')
    {
        if (!$this->baseUrl) {
            \Log::warning('Firebase no está configurado; no se muestran datos simulados.');
            return [];
        }

        try {
            $url = rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/') . '.json';

            // Intentar obtener datos reales de Firebase
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json();

                // Si Firebase devuelve datos, usarlos
                if ($data && is_array($data) && !empty($data)) {
                    \Log::info('Datos obtenidos exitosamente desde Firebase');
                    return $data;
                }

                return [];
            }

            \Log::warning('Firebase respondió con error. Status: ' . $response->status());
            return [];

        } catch (\Exception $e) {
            \Log::error('Error conectando con Firebase: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener datos de un sensor específico
     */
    public function getSensorById($sensorId)
    {
        $data = $this->getSensorData();
        return $data[$sensorId] ?? null;
    }

    /**
     * Obtener todos los sensores activos
     */
    public function getActiveSensors()
    {
        $data = $this->getSensorData();
        if (!$data) return [];

        return array_filter($data, function($sensor) {
            return isset($sensor['activo']) && $sensor['activo'];
        });
    }

    /**
     * Obtener lecturas recientes de un sensor
     */
    public function getRecentReadings($sensorId, $limit = 50)
    {
        $sensor = $this->getSensorById($sensorId);
        if (!$sensor || !isset($sensor['lecturas'])) {
            return [];
        }

        $lecturas = $sensor['lecturas'];
        return array_slice($lecturas, -$limit);
    }

    /**
     * Enviar datos de sensor a Firebase
     */
    public function sendSensorData($sensorId, $data)
    {
        try {
            $url = rtrim($this->baseUrl, '/') . '/sensores/' . $sensorId . '.json';
            $response = Http::timeout(15)->put($url, $data);

            if ($response->successful()) {
                \Log::info("Datos enviados exitosamente a Firebase para sensor {$sensorId}");
                return true;
            } else {
                \Log::warning("Error enviando datos a Firebase para sensor {$sensorId}. Status: " . $response->status() . ", Body: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('Error enviando datos a Firebase para sensor ' . $sensorId . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar datos de sensor específicos en Firebase
     */
    public function updateSensorData($sensorId, $newData)
    {
        try {
            // Obtener datos actuales del sensor
            $currentData = $this->getSensorById($sensorId);

            if (!$currentData) {
                return false;
            }

            // Fusionar datos actuales con nuevos
            $updatedData = array_merge($currentData, $newData);
            $updatedData['ultima_actualizacion'] = now()->toISOString();

            // Agregar nueva lectura si se proporciona
            if (isset($newData['nueva_lectura'])) {
                if (!isset($updatedData['lecturas'])) {
                    $updatedData['lecturas'] = [];
                }

                array_unshift($updatedData['lecturas'], $newData['nueva_lectura']);

                // Mantener solo las últimas 50 lecturas
                $updatedData['lecturas'] = array_slice($updatedData['lecturas'], 0, 50);
            }

            return $this->sendSensorData($sensorId, $updatedData);

        } catch (\Exception $e) {
            \Log::error('Error actualizando sensor ' . $sensorId . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Endpoint para recibir datos desde sensores físicos
     */
    public function receiveSensorData($sensorId, $sensorData)
    {
        try {
            // Validar datos básicos
            if (!isset($sensorData['tipo'])) {
                \Log::warning('Datos de sensor inválidos: falta tipo');
                return false;
            }

            // Crear nueva lectura
            $nuevaLectura = [
                'timestamp' => now()->toISOString(),
                'tipo' => $sensorData['tipo']
            ];

            // Agregar campos específicos según el tipo
            switch ($sensorData['tipo']) {
                case 'movimiento_tierra':
                    if (isset($sensorData['movimiento'])) {
                        $nuevaLectura['movimiento'] = $sensorData['movimiento'];
                    }
                    if (isset($sensorData['aceleracion'])) {
                        $nuevaLectura['aceleracion'] = $sensorData['aceleracion'];
                    }
                    break;
                case 'gases_toxicos':
                    if (isset($sensorData['co'])) $nuevaLectura['co'] = $sensorData['co'];
                    if (isset($sensorData['co2'])) $nuevaLectura['co2'] = $sensorData['co2'];
                    if (isset($sensorData['metano'])) $nuevaLectura['metano'] = $sensorData['metano'];
                    if (isset($sensorData['oxigeno'])) $nuevaLectura['oxigeno'] = $sensorData['oxigeno'];
                    break;
                case 'signos_vitales':
                    if (isset($sensorData['frecuencia_cardiaca'])) $nuevaLectura['frecuencia_cardiaca'] = $sensorData['frecuencia_cardiaca'];
                    if (isset($sensorData['temperatura_corporal'])) $nuevaLectura['temperatura_corporal'] = $sensorData['temperatura_corporal'];
                    if (isset($sensorData['saturacion_oxigeno'])) $nuevaLectura['saturacion_oxigeno'] = $sensorData['saturacion_oxigeno'];
                    if (isset($sensorData['presion_arterial'])) $nuevaLectura['presion_arterial'] = $sensorData['presion_arterial'];
                    break;
            }

            // Actualizar sensor con nueva lectura
            $updateData = [
                'ultima_lectura' => now()->toISOString(),
                'nueva_lectura' => $nuevaLectura
            ];

            // Actualizar valores actuales si se proporcionan
            foreach (['movimiento', 'aceleracion', 'co', 'co2', 'metano', 'oxigeno', 'frecuencia_cardiaca', 'temperatura_corporal', 'saturacion_oxigeno', 'presion_arterial'] as $campo) {
                if (isset($sensorData[$campo])) {
                    $updateData[$campo] = $sensorData[$campo];
                }
            }

            $success = $this->updateSensorData($sensorId, $updateData);

            if ($success) {
                \Log::info("Datos recibidos y actualizados para sensor {$sensorId}");
            }

            return $success;

        } catch (\Exception $e) {
            \Log::error('Error procesando datos del sensor ' . $sensorId . ': ' . $e->getMessage());
            return false;
        }
    }
}
