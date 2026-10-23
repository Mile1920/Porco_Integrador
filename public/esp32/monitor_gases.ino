// =============================================
// Monitor de gases MQ-7 + MQ-135 + Buzzer
// Mina Porco Illapa
// ESP32
// =============================================

#include <WiFi.h>
#include <HTTPClient.h>

// ============ CONFIGURACIÓN ============
const char* WIFI_SSID     = "CONFIGURAR_WIFI_SSID";
const char* WIFI_PASSWORD = "CONFIGURAR_WIFI_PASSWORD";
const char* SERVIDOR = "http://CONFIGURAR_IP_DEL_SERVIDOR";
const char* ENDPOINT      = "/api/sensor/esp32/recibir";
const char* DEVICE_ID     = "esp32_gases_01";
const char* SENSOR_KEY    = "CONFIGURAR_SENSOR_SECRET";
// =======================================

// Pines
const int PIN_MQ7   = 34;
const int PIN_MQ135 = 35;
const int PIN_BUZZ  = 26;

// Umbrales
const int UMBRAL_MQ7   = 2200;
const int UMBRAL_MQ135 = 2850;

unsigned long ultimoEnvio = 0;
const unsigned long INTERVALO_ENVIO = 10000;

void setup() {
    Serial.begin(115200);
    pinMode(PIN_BUZZ, OUTPUT);
    digitalWrite(PIN_BUZZ, LOW);

    Serial.println();
    Serial.println("=================================");
    Serial.println(" SISTEMA DE MONITOREO MINERO");
    Serial.println("=================================");
    Serial.println();

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

    Serial.println("Calentando sensores...");
    Serial.println("Espere 60 segundos");
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
