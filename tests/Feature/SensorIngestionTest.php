<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\SensorData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SensorIngestionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['esp32.sensor_secret' => 'test-sensor-secret']);
    }

    public function test_esp32_endpoint_requires_the_device_key(): void
    {
        $this->postJson('/api/sensor/esp32/recibir', [])->assertUnauthorized();
        $this->assertDatabaseCount('sensor_data', 0);
    }

    public function test_esp32_rejects_measurements_outside_the_device_range(): void
    {
        $this->withHeader('X-SENSOR-KEY', 'test-sensor-secret')
            ->postJson('/api/sensor/esp32/recibir', [
                'device_id' => 'esp32_gases_01',
                'tipo' => 'gases_toxicos',
                'mediciones' => ['mq7_co' => 4096, 'mq135_aire' => 300, 'alerta' => false],
            ])
            ->assertUnprocessable();

        $this->assertDatabaseCount('sensor_data', 0);
    }

    public function test_esp32_persists_a_valid_reading_without_creating_an_alert(): void
    {
        $this->withHeader('X-SENSOR-KEY', 'test-sensor-secret')
            ->postJson('/api/sensor/esp32/recibir', [
                'device_id' => 'esp32_gases_01',
                'tipo' => 'gases_toxicos',
                'mediciones' => ['mq7_co' => 1234, 'mq135_aire' => 2048, 'alerta' => false],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('sensor_data', ['device_id' => 'esp32_gases_01']);
        $this->assertDatabaseCount('alertas', 0);
    }

    public function test_generic_sensor_endpoint_requires_authentication_and_valid_data(): void
    {
        $this->postJson('/sensors/data', [
            'device_id' => 'dev-01',
            'tipo' => 'particulas',
            'payload' => ['pm2_5' => 42.5],
        ])->assertUnauthorized();

        $this->withHeader('X-SENSOR-KEY', 'test-sensor-secret')
            ->postJson('/sensors/data', [
                'device_id' => 'dev-01',
                'tipo' => 'particulas',
                'alert' => 'quizas',
            ])
            ->assertUnprocessable();

        $this->assertDatabaseCount('sensor_data', 0);
    }
}
