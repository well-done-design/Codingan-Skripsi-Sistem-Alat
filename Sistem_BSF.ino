#include <DHT.h>
#include <Wire.h>
#include <BH1750.h>
#include <WiFi.h>
#include <HTTPClient.h>

// --- Library untuk LCD TFT ST7735 ---
#include <Adafruit_GFX.h>
#include <Adafruit_ST7735.h>
#include <SPI.h>

// --- Konfigurasi Pin Sensor & Relay ---
#define DHTPIN 2
#define DHTTYPE DHT22
#define SDA_PIN 21
#define SCL_PIN 22
#define RELAY_PIN 13

// --- Konfigurasi Pin untuk LCD TFT ST7735 ---
#define TFT_MOSI 23
#define TFT_CS   5
#define TFT_RST  16
#define TFT_RS   17 // Sering disebut juga DC atau A0

// --- Inisialisasi Objek Sensor & Layar ---
DHT dht(DHTPIN, DHTTYPE);
BH1750 lightMeter;
Adafruit_ST7735 tft = Adafruit_ST7735(TFT_CS, TFT_RS, TFT_RST);

// --- Konfigurasi WiFi & Server ---
const char* ssid = "Andromax-M3Z-45E0";
const char* pass = "31723934";
const char* host = "magrowkit.my.id";

// --- Variabel & Konstanta Kontrol ---
float temperature = 0;
float humidity = 0;
float lightIntensity = 0;

const float DARKNESS_THRESHOLD = 50.0;
const float SUHU_MINIMUM_IDEAL = 27.0;

const long interval = 30 * 60 * 1000; // 30 menit
unsigned long previousMillis = 0;

// --- Deklarasi Fungsi Status (BARU) ---
String getTempStatus(float temp);
String getHumidStatus(float hum);

// --- Fungsi untuk update tampilan LCD (DIPERBARUI) ---
void updateDisplay(float temp, float hum, float lux, String tempStatus, String humidStatus, String lampStatus) {
  tft.fillScreen(ST7735_BLACK);
  tft.setCursor(0, 5);
  tft.setTextColor(ST7735_WHITE);
  tft.setTextSize(1);
  tft.println("   Sistem Kontrol BSF");
  tft.println("-------------------------");
  
  // Tampilan Suhu dan Statusnya
  tft.setCursor(5, 25);
  tft.setTextColor(ST7735_YELLOW);
  tft.print("Suhu: "); tft.print(temp, 1); tft.println(" C");
  tft.setCursor(15, 35); // Posisi untuk status suhu
  tft.print("Status: "); tft.print(tempStatus);

  // Tampilan Kelembapan dan Statusnya
  tft.setCursor(5, 50);
  tft.setTextColor(ST7735_CYAN);
  tft.print("Lembab: "); tft.print(hum, 1); tft.println(" %");
  tft.setCursor(15, 60); // Posisi untuk status kelembapan
  tft.print("Status: "); tft.print(humidStatus);
  
  // Tampilan Intensitas Cahaya
  tft.setCursor(5, 75);
  tft.setTextColor(ST7735_ORANGE);
  tft.print("Cahaya: "); tft.print(lux, 0); tft.println(" lux");

  // Tampilan Status Lampu
  tft.setTextSize(2);
  tft.setCursor(10, 100);
  if (lampStatus == "MENYALA") {
    tft.setTextColor(ST7735_GREEN);
    tft.println("Lampu: ON");
  } else {
    tft.setTextColor(ST7735_RED);
    tft.println("Lampu: OFF");
  }
}

void setup() {
  Serial.begin(115200);
  Serial.println("Sistem Monitoring dan Kontrol Otomatis BSF");
  
  pinMode(RELAY_PIN, OUTPUT);
  digitalWrite(RELAY_PIN, LOW);
  Serial.println("Pin Relay diinisialisasi");
  
  tft.initR(INITR_BLACKTAB);
  tft.setRotation(1);
  tft.fillScreen(ST7735_BLACK);
  tft.setCursor(10, 10);
  tft.setTextColor(ST7735_WHITE);
  tft.setTextSize(1);
  tft.println("Inisialisasi Sistem...");
  Serial.println("LCD TFT ST7735 diinisialisasi");
  delay(1000);

  WiFi.begin(ssid, pass);
  Serial.print("Connecting to WiFi...");
  tft.setCursor(10, 30);
  tft.print("Menghubungkan WiFi...");
  while(WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }
  Serial.println("\nConnected");
  tft.setCursor(10, 50);
  tft.setTextColor(ST7735_GREEN);
  tft.print("Terhubung!");
  delay(1500);

  dht.begin();
  Serial.println("Sensor DHT22 diinisialisasi");
  
  Wire.begin(SDA_PIN, SCL_PIN);
  if (lightMeter.begin(BH1750::CONTINUOUS_HIGH_RES_MODE)) {
    Serial.println("Sensor BH1750 diinisialisasi");
  } else {
    Serial.println("Error! Sensor BH1750 tidak ditemukan");
  }

  Serial.println("\nSistem siap.");
}

void loop() {
  unsigned long currentMillis = millis();

  if (currentMillis - previousMillis >= interval) {
    previousMillis = currentMillis;
    String lampStatusMessage = "PADAM";

    humidity = dht.readHumidity();
    temperature = dht.readTemperature();
    
    if (isnan(humidity) || isnan(temperature)) {
      Serial.println("Gagal membaca data dari sensor DHT22!");
    } else {
      lightIntensity = lightMeter.readLightLevel();

      // --- BARU: Dapatkan status dari setiap sensor ---
      String tempStatus = getTempStatus(temperature);
      String humidStatus = getHumidStatus(humidity);

      Serial.println("\n=== Data Sensor Diambil ===");
      Serial.print("Suhu: "); Serial.print(temperature); Serial.print(" *C ("); Serial.print(tempStatus); Serial.println(")");
      Serial.print("Kelembaban: "); Serial.print(humidity); Serial.print(" % ("); Serial.print(humidStatus); Serial.println(")");
      Serial.print("Intensitas Cahaya: "); Serial.print(lightIntensity); Serial.println(" Lux");

      if (lightIntensity < DARKNESS_THRESHOLD) {
        digitalWrite(RELAY_PIN, HIGH);
        lampStatusMessage = "MENYALA";
        Serial.println("Status Lampu: MENYALA (Kondisi Gelap)");
      } else if (temperature < SUHU_MINIMUM_IDEAL) {
        digitalWrite(RELAY_PIN, HIGH);
        lampStatusMessage = "MENYALA";
        Serial.println("Status Lampu: MENYALA (Suhu Dingin)");
      } else {
        digitalWrite(RELAY_PIN, LOW);
        lampStatusMessage = "PADAM";
        Serial.println("Status Lampu: PADAM (Kondisi Ideal)");
      }

      // --- Panggil updateDisplay dengan parameter status ---
      updateDisplay(temperature, humidity, lightIntensity, tempStatus, humidStatus, lampStatusMessage);

      // --- BLOK PENGIRIMAN DATA ---
      if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        WiFiClient client;
        
        String url = String("http://") + host + "/kirimdatalalat.php?temperature=" + String(temperature) 
                     + "&humidity=" + String(humidity) 
                     + "&lightIntensity=" + String(lightIntensity);

        Serial.println("Mengirim data ke: " + url);

        http.setTimeout(5000); 
        http.begin(client, url);
        
        int httpCode = http.GET();
        if (httpCode > 0) {
          Serial.printf("[HTTP] Pengiriman berhasil, kode respons: %d\n", httpCode);
          String payload = http.getString();
          Serial.println("Respon server: " + payload);
        } else {
          Serial.printf("[HTTP] Pengiriman gagal, error: %s\n", http.errorToString(httpCode).c_str());
        }
        http.end();
      } else {
        Serial.println("WiFi terputus. Gagal mengirim data.");
      }
    }
  }
} 

// --- FUNGSI STATUS SENSOR (BARU) ---

String getTempStatus(float temp) {
    if (temp >= 27 && temp <= 30) {
        return "Bagus";
    } else if (temp >= 31 && temp <= 35) {
        return "Sedang";
    } else if (temp >= 36) {
        return "Bahaya";
    } else {
        return "Dingin"; // Status untuk suhu di bawah 27
    }
}

String getHumidStatus(float hum) {
    if (hum >= 60 && hum <= 80) {
        return "Bagus";
    } else if (hum > 80) {
        return "Basah";
    } else { // Mencakup 0-59%
        return "Kering";
    }
}
