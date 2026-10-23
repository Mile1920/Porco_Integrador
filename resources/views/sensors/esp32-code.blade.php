@extends('layouts.app')
@section('title', 'Código ESP32 - Sensores')

@section('content')
<div class="card">
    <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
        <div>
            <h4 class="card-title mb-0">Código para ESP32</h4>
            <p class="card-category mb-0">Programá tu ESP32 con este código para enviar datos de sensores al sistema</p>
        </div>
        <a href="{{ route('sensor-dashboard') }}" class="btn btn-outline-light btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong><i class="fas fa-info-circle"></i> Instrucciones:</strong>
            <ol class="mb-0 mt-2">
                <li>Conectá los sensores MQ-7 (CO) al pin GPIO34 y MQ-135 (calidad de aire) al pin GPIO35</li>
                <li>Conectá un buzzer al pin GPIO26</li>
                <li>Cambiá <code>WIFI_SSID</code> y <code>WIFI_PASSWORD</code> con los datos de tu red WiFi</li>
                <li>Cambiá <code>SERVIDOR</code> por la IP de este servidor (ej: 192.168.1.204)</li>
                <li>El código mostrado ya incluye la clave de <code>SENSOR_SECRET</code>. Si descargás el archivo <code>.ino</code>, reemplazá <code>CONFIGURAR_SENSOR_SECRET</code> por esa misma clave antes de compilar; no publiques el archivo con la clave.</li>
                <li>El ESP32 enviará lecturas cada 10 segundos a <code>{{ url('api/sensor/esp32/recibir') }}</code></li>
            </ol>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Código Fuente (Arduino IDE)</h5>
            <a href="{{ asset('esp32/monitor_gases.ino') }}" class="btn btn-success" download>
                <i class="fas fa-download"></i> Descargar .ino
            </a>
        </div>

        <pre style="max-height: 600px; overflow-y: auto; background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 4px; font-size: 13px; line-height: 1.5;"><code>// =============================================
// Monitor de gases MQ-7 + MQ-135 + Buzzer
// Mina Porco Illapa - Integrador II
// ESP32
// =============================================
// Configuración WiFi
#include &lt;WiFi.h&gt;
#include &lt;HTTPClient.h&gt;

// ============ CONFIGURACIÓN ============
const char* WIFI_SSID     = "CONFIGURAR_WIFI_SSID";
const char* WIFI_PASSWORD = "CONFIGURAR_WIFI_PASSWORD";
const char* SERVIDOR      = "http://192.168.1.205:8000";
const char* ENDPOINT      = "/api/sensor/esp32/recibir";
const char* DEVICE_ID     = "esp32_gases_01";
const char* SENSOR_KEY    = "{{ $sensorKey }}";
// =======================================

// Pines
const int PIN_MQ7   = 34;
const int PIN_MQ135 = 35;
const int PIN_BUZZ  = 26;

// Umbrales
const int UMBRAL_MQ7   = 2200;
const int UMBRAL_MQ135 = 2850;

// Tiempo entre envíos (milisegundos)
const unsigned long INTERVALO_ENVIO = 10000; // 10 segundos
unsigned long ultimoEnvio = 0;

void setup() {
    Serial.begin(115200);
    pinMode(PIN_BUZZ, OUTPUT);
    digitalWrite(PIN_BUZZ, LOW);

    Serial.println();
    Serial.println("=================================");
    Serial.println(" SISTEMA DE MONITOREO MINERO");
    Serial.println("=================================");

    // Conectar WiFi
    Serial.print("Conectando a WiFi: ");
    Serial.println(WIFI_SSID);
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

    int intentos = 0;
    while (WiFi.status() != WL_CONNECTED && intentos < 40) {
        delay(500);
        Serial.print(".");
        intentos++;
    }

    if (WiFi.status() == WL_CONNECTED) {
        Serial.println();
        Serial.print("WiFi conectado. IP: ");
        Serial.println(WiFi.localIP());
    } else {
        Serial.println();
        Serial.println("ERROR: No se pudo conectar al WiFi");
    }

    Serial.println("Calentando sensores (60s)...");
    delay(60000);
    Serial.println("Monitoreo iniciado");
    Serial.println();
}

void loop() {
    int valMQ7   = analogRead(PIN_MQ7);
    int valMQ135 = analogRead(PIN_MQ135);
    bool alerta  = false;

    Serial.print("MQ-7 (CO): ");
    Serial.print(valMQ7);
    Serial.print(" | MQ-135 (Aire): ");
    Serial.println(valMQ135);

    if (valMQ7 > UMBRAL_MQ7) {
        Serial.println("ADVERTENCIA: CO elevado");
        alerta = true;
    }
    if (valMQ135 > UMBRAL_MQ135) {
        Serial.println("ADVERTENCIA: Calidad de aire peligrosa");
        alerta = true;
    }
    if (valMQ7 > 2600) {
        Serial.println("PELIGRO: Monoxido de carbono muy alto");
        alerta = true;
    }
    if (valMQ135 > 3200) {
        Serial.println("PELIGRO: Aire altamente contaminado");
        alerta = true;
    }

    if (alerta) {
        for (int i = 0; i < 3; i++) {
            digitalWrite(PIN_BUZZ, HIGH);
            delay(250);
            digitalWrite(PIN_BUZZ, LOW);
            delay(250);
        }
    } else {
        Serial.println("Ambiente normal");
    }

    // Enviar datos al servidor cada INTERVALO_ENVIO ms
    if (millis() - ultimoEnvio >= INTERVALO_ENVIO) {
        enviarDatos(valMQ7, valMQ135, alerta);
        ultimoEnvio = millis();
    }

    Serial.println("--------------------------------");
    delay(2000);
}

void enviarDatos(int mq7, int mq135, bool alerta) {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("WiFi no disponible");
        return;
    }

    HTTPClient http;
    String url = String(SERVIDOR) + ENDPOINT;

    http.begin(url);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("X-SENSOR-KEY", SENSOR_KEY);

    String payload = "{";
    payload += "\"device_id\":\"" + String(DEVICE_ID) + "\",";
    payload += "\"tipo\":\"gases_toxicos\",";
    payload += "\"mediciones\":{";
    payload += "\"mq7_co\":" + String(mq7) + ",";
    payload += "\"mq135_aire\":" + String(mq135) + ",";
    payload += "\"alerta\":" + String(alerta ? "true" : "false");
    payload += "}";
    payload += "}";

    Serial.print("Enviando datos... ");
    int httpCode = http.POST(payload);

    if (httpCode > 0) {
        Serial.print("HTTP ");
        Serial.println(httpCode);
    } else {
        Serial.print("Error: ");
        Serial.println(http.errorToString(httpCode));
    }

    http.end();
}
</code></pre>
    </div>
</div>
@endsection
