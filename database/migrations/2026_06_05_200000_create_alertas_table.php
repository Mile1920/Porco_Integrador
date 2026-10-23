<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50); // gases_toxicos, movimiento_tierra, signos_vitales, salida_pendiente
            $table->string('nivel', 20); // critico, alto, medio, bajo
            $table->string('mensaje', 500);
            $table->string('origen', 100); // sensor_id, device_id, 'ESP32-GASES', 'TRABAJADOR-X'
            $table->string('area', 100)->nullable();
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_alerta')->useCurrent();
            $table->timestamps();

            $table->index('leida');
            $table->index('fecha_alerta');
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
