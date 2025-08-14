#include <Wire.h>
#include <Adafruit_AMG88xx.h>
#include <SPI.h>
#include <Adafruit_GFX.h>
#include <Adafruit_ILI9341.h>
#include <DHT.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <AverageValue.h>

// — Konfigurasi WiFi & Server —
const char* ssid = "Andromax-M3Z-45E0";
const char* pass = "31723934";
const char* host = "magrowkit.my.id";

// — Konfigurasi AMG8833 (Kamera Termal) —
#define AMG_SDA 16
#define AMG_SCL 17
TwoWire I2C_AMG = TwoWire(1);
Adafruit_AMG88xx amg;
float pixels[AMG88xx_PIXEL_ARRAY_SIZE];
#define INTERPOLATED_COLS 24
#define INTERPOLATED_ROWS 24
float displayPixels[INTERPOLATED_ROWS * INTERPOLATED_COLS];

// — Konfigurasi TFT SPI (Layar) —
#define TFT_MISO 25
#define TFT_LED 5
#define TFT_SCK 19
#define TFT_MOSI 23
#define TFT_DC 21
#define TFT_RESET 18
#define TFT_CS 22
Adafruit_ILI9341 tft = Adafruit_ILI9341(TFT_CS, TFT_DC, TFT_MOSI, TFT_SCK, TFT_RESET, TFT_MISO);

// — Palet Warna Kamera Termal —
const uint16_t camColors[] = {
    0x480F, 0x400F, 0x400F, 0x400F, 0x4010, 0x3810, 0x3810, 0x3810, 0x3810, 0x3010, 0x3010, 0x3010, 0x2810, 0x2810, 0x2810, 0x2010,
    0x2010, 0x1810, 0x1810, 0x1811, 0x1811, 0x1011, 0x1011, 0x1011, 0x0811, 0x0811, 0x0811, 0x0011, 0x0011, 0x0011, 0x0011, 0x0011,
    0x0031, 0x0031, 0x0051, 0x0072, 0x0072, 0x0092, 0x00B2, 0x00B2, 0x00D2, 0x00F2, 0x00F2, 0x0112, 0x0132, 0x0152, 0x0152, 0x0172,
    0x0192, 0x0192, 0x01B2, 0x01D2, 0x01F3, 0x01F3, 0x0213, 0x0233, 0x0253, 0x0253, 0x0273, 0x0293, 0x02B3, 0x02D3, 0x02D3, 0x02F3,
    0x0313, 0x0333, 0x0333, 0x0353, 0x0373, 0x0394, 0x03B4, 0x03D4, 0x03D4, 0x03F4, 0x0414, 0x0434, 0x0454, 0x0474, 0x0474, 0x0494,
    0x04B4, 0x04D4, 0x04F4, 0x0514, 0x0534, 0x0534, 0x0554, 0x0554, 0x0574, 0x0574, 0x0573, 0x0573, 0x0573, 0x0572, 0x0572, 0x0572,
    0x0571, 0x0591, 0x0591, 0x0590, 0x0590, 0x058F, 0x058F, 0x058F, 0x058E, 0x05AE, 0x05AE, 0x05AD, 0x05AD, 0x05AD, 0x05AC, 0x05AC,
    0x05AB, 0x05CB, 0x05CB, 0x05CA, 0x05CA, 0x05CA, 0x05C9, 0x05C9, 0x05C8, 0x05E8, 0x05E8, 0x05E7, 0x05E7, 0x05E6, 0x05E6, 0x05E6,
    0x05E5, 0x05E5, 0x0604, 0x0604, 0x0604, 0x0603, 0x0603, 0x0602, 0x0602, 0x0601, 0x0621, 0x0621, 0x0620, 0x0620, 0x0620, 0x0620,
    0x0E20, 0x0E20, 0x0E40, 0x1640, 0x1640, 0x1E40, 0x1E40, 0x2640, 0x2640, 0x2E40, 0x2E60, 0x3660, 0x3660, 0x3E60, 0x3E60, 0x3E60,
    0x4660, 0x4660, 0x4E60, 0x4E80, 0x5680, 0x5680, 0x5E80, 0x5E80, 0x6680, 0x6680, 0x6E80, 0x6EA0, 0x76A0, 0x76A0, 0x7EA0, 0x7EA0,
    0x86A0, 0x86A0, 0x8EA0, 0x8EC0, 0x96C0, 0x96C0, 0x9EC0, 0x9EC0, 0xA6C0, 0xAEC0, 0xAEC0, 0xB6E0, 0xB6E0, 0xBEE0, 0xBEE0, 0xC6E0,
    0xC6E0, 0xCEE0, 0xCEE0, 0xD6E0, 0xD700, 0xDF00, 0xDEE0, 0xDEC0, 0xDEA0, 0xDE80, 0xDE80, 0xE660, 0xE640, 0xE620, 0xE600, 0xE5E0,
    0xE5C0, 0xE5A0, 0xE580, 0xE560, 0xE540, 0xE520, 0xE500, 0xE4E0, 0xE4C0, 0xE4A0, 0xE480, 0xE460, 0xEC40, 0xEC20, 0xEC00, 0xEBE0,
    0xEBC0, 0xEBA0, 0xEB80, 0xEB60, 0xEB40, 0xEB20, 0xEB00, 0xEAE0, 0xEAC0, 0xEAA0, 0xEA80, 0xEA60, 0xEA40, 0xF220, 0xF200, 0xF1E0,
    0xF1C0, 0xF1A0, 0xF180, 0xF160, 0xF140, 0xF100, 0xF0E0, 0xF0C0, 0xF0A0, 0xF080, 0xF060, 0xF040, 0xF020, 0xF800
};

// Pengaturan Waktu
const unsigned long INTERVAL_KIRIM_DATA = 10 * 60 * 1000UL; 
const unsigned long INTERVAL_KAMERA_TERMAL = 30 * 1000UL; 
unsigned long previousMillisKirimData = 0;
unsigned long previousMillisKameraTermal = 0;
unsigned long pompaMatiMillis = 0;
unsigned long buzzerMatiMillis = 0;

// Variabel Global Sensor
float minTemp, maxTemp, avgTemp;
float dht_temp = 0.0;
float dht_hum = 0.0;
bool dht_valid = false;
float co2_ppm = 0;
bool co2_valid = false;

// Konfigurasi Sensor Lainnya
#define DHTPIN 4
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

#define MQ135PIN 34
const int RLOAD = 20000;
const float RO_BERSIH = 44000;
const float KURVA_A = 110.7432567;
const float KURVA_B = -2.856935538;
float rasioRsRo_min;
float rasioRsRo_maks;
const long JUMLAH_MAKS_NILAI = 10; 
AverageValue<long> averageValue(JUMLAH_MAKS_NILAI);

// Konfigurasi Aktuator
#define RELAY_PIN 26
#define BUZZER_PIN 27

// — Deklarasi Fungsi —
void tampilkanHeader();
void tampilkanCitraTermal();
void tampilkanStatistikDanSensor();
void interpolate_image(float *src, float *dest, int src_rows, int src_cols, int dest_rows, int dest_cols);
void cekDanKontrolAktuator(float temp, float hum, float co2);
float bacaMQ135();
String getTempStatus(float temp);
String getHumidStatus(float hum);
String getCO2Status(float co2);

void setup(){
    Serial.begin(115200);
    pinMode(TFT_LED, OUTPUT);
    digitalWrite(TFT_LED, HIGH);

    pinMode(RELAY_PIN, OUTPUT);
    pinMode(BUZZER_PIN, OUTPUT);
    digitalWrite(RELAY_PIN, LOW);
    digitalWrite(BUZZER_PIN, LOW);

    pinMode(MQ135PIN, INPUT);
    rasioRsRo_min = pow((10000 / KURVA_A), (1 / KURVA_B));
    rasioRsRo_maks = pow((400 / KURVA_A), (1 / KURVA_B));

    tft.begin();
    tft.setRotation(3);
    tft.fillScreen(ILI9341_BLACK);
    tampilkanHeader();

    tft.setTextColor(ILI9341_WHITE);
    tft.setTextSize(2);
    tft.setCursor(20, 100);
    tft.print("Connecting to WiFi...");

    WiFi.begin(ssid, pass);
    while(WiFi.status() != WL_CONNECTED){
        delay(500);
        Serial.print(".");
    }
    Serial.println("\nWiFi Connected");
    
    tft.fillRect(0, 22, 320, 220, ILI9341_BLACK);

    dht.begin();
    I2C_AMG.begin(AMG_SDA, AMG_SCL, 100000);
    if(!amg.begin(0x69, &I2C_AMG)){
        Serial.println("AMG8833 not found");
        tft.setCursor(20, 100);
        tft.setTextColor(ILI9341_RED);
        tft.print("AMG8833 Not Found!");
        while(1);
    }

    previousMillisKirimData = -INTERVAL_KIRIM_DATA;
    previousMillisKameraTermal = -INTERVAL_KAMERA_TERMAL;
}

void loop(){
    unsigned long currentMillis = millis();

    if(currentMillis - previousMillisKameraTermal >= INTERVAL_KAMERA_TERMAL){
        previousMillisKameraTermal = currentMillis;
        amg.readPixels(pixels);
        minTemp = 100; maxTemp = 0; float sum = 0;
        for(int i = 0; i < AMG88xx_PIXEL_ARRAY_SIZE; i++){
            minTemp = min(minTemp, pixels[i]);
            maxTemp = max(maxTemp, pixels[i]);
            sum += pixels[i];
        }
        avgTemp = sum / AMG88xx_PIXEL_ARRAY_SIZE;
        interpolate_image(pixels, displayPixels, 8, 8, INTERPOLATED_ROWS, INTERPOLATED_COLS);
        tampilkanCitraTermal();
        tampilkanStatistikDanSensor();
    }

    if(currentMillis - previousMillisKirimData >= INTERVAL_KIRIM_DATA){
        previousMillisKirimData = currentMillis;
        Serial.println("Membaca Sensor DHT & MQ135...");

        float temp = dht.readTemperature();
        float hum = dht.readHumidity();
        
        if (isnan(temp) || isnan(hum)) {
            Serial.println("Gagal membaca dari sensor DHT!");
            dht_valid = false;
        } else {
            dht_valid = true;
            dht_temp = temp;
            dht_hum = hum;
            
            co2_ppm = bacaMQ135();
            cekDanKontrolAktuator(dht_temp, dht_hum, co2_ppm);

            if (WiFi.status() == WL_CONNECTED) {
                HTTPClient http;
                WiFiClient client;
                
                String url = String("http://") + host + "/kirimdata.php?temperature=" + String(dht_temp) + "&humidity=" + String(dht_hum)
                                 + "&airQuality=" + String(co2_ppm);
                
                http.setTimeout(5000); 
                http.begin(client, url);
                
                int httpCode = http.GET();
                if (httpCode > 0) {
                    Serial.printf("[HTTP] GET... code: %d\n", httpCode);
                } else {
                    Serial.printf("[HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());
                }
                http.end();
            } else {
                Serial.println("WiFi Disconnected. Cannot send data.");
            }
        }
        tampilkanStatistikDanSensor();
    }

    if (pompaMatiMillis > 0 && currentMillis >= pompaMatiMillis) {
        digitalWrite(RELAY_PIN, LOW);
        pompaMatiMillis = 0;
    }
    if (buzzerMatiMillis > 0 && currentMillis >= buzzerMatiMillis) {
        digitalWrite(BUZZER_PIN, LOW);
        buzzerMatiMillis = 0;
    }
}

// --- FUNGSI TAMPILAN ---

void tampilkanHeader(){
    tft.fillRect(0, 0, 320, 22, ILI9341_DARKCYAN);
    tft.setTextColor(ILI9341_WHITE);
    tft.setTextSize(2);
    tft.setCursor(5, 5);
    tft.print("Thermal Cam & Enviro-Sensor");
}

void tampilkanCitraTermal() {
    int boxSize = 8;
    int startX = 10;
    int startY = 30;
    for (int y = 0; y < INTERPOLATED_ROWS; y++) {
        for (int x = 0; x < INTERPOLATED_COLS; x++) {
            float temp = displayPixels[y * INTERPOLATED_COLS + x];
            uint8_t colorIndex = map(temp, 20, 45, 0, 255);
            colorIndex = constrain(colorIndex, 0, 255);
            tft.fillRect(startX + x * boxSize, startY + y * boxSize, boxSize, boxSize, camColors[colorIndex]);
        }
    }
}

void tampilkanStatistikDanSensor(){
    int xPos = 215;
    int yPos = 35;
    
    // Bersihkan area yang lebih luas untuk semua data
    tft.fillRect(xPos - 5, yPos, 110, 200, ILI9341_BLACK); 
    
    // Tampilkan statistik kamera termal
    tft.setTextSize(2);
    tft.setTextColor(ILI9341_RED);
    tft.setCursor(xPos, yPos);
    tft.printf("Max:%.1f", maxTemp);
    tft.setTextColor(ILI9341_GREEN);
    tft.setCursor(xPos, yPos + 25);
    tft.printf("Avg:%.1f", avgTemp);
    tft.setTextColor(ILI9341_CYAN);
    tft.setCursor(xPos, yPos + 50);
    tft.printf("Min:%.1f", minTemp);
    
    // Tampilkan data sensor lingkungan dan statusnya
    tft.setTextSize(1); 
    int yOffset = yPos + 80;

    if (dht_valid) {
        // Suhu
        tft.setTextColor(ILI9341_WHITE);
        tft.setCursor(xPos, yOffset);
        tft.printf("T: %.1f C", dht_temp);
        tft.setTextColor(ILI9341_YELLOW); // Warna untuk status
        tft.setCursor(xPos, yOffset + 10);
        tft.print(getTempStatus(dht_temp));

        // Kelembapan
        tft.setTextColor(ILI9341_WHITE);
        tft.setCursor(xPos, yOffset + 25);
        tft.printf("H: %.0f %%", dht_hum);
        tft.setTextColor(ILI9341_YELLOW);
        tft.setCursor(xPos, yOffset + 35);
        tft.print(getHumidStatus(dht_hum));
    } else {
        tft.setTextColor(ILI9341_RED);
        tft.setCursor(xPos, yOffset);
        tft.print("T/H: Error");
    }

    if (co2_valid) {
        // CO2
        tft.setTextColor(ILI9341_WHITE);
        tft.setCursor(xPos, yOffset + 50);
        tft.printf("CO2: %.0f ppm", co2_ppm);
        tft.setTextColor(ILI9341_YELLOW);
        tft.setCursor(xPos, yOffset + 60);
        tft.print(getCO2Status(co2_ppm));
    } else {
        tft.setTextColor(ILI9341_RED);
        tft.setCursor(xPos, yOffset + 50);
        tft.print("CO2: Error");
    }
}

// --- FUNGSI SENSOR & KONTROL ---

// --- FUNGSI DIUBAH ---
float bacaMQ135() {
    float adcRaw = analogRead(MQ135PIN);
    if (adcRaw < 1) {
        co2_valid = false;
        return -1; // Mengembalikan nilai error jika pembacaan ADC tidak valid
    }
    
    float rS = ((4095.0 * RLOAD) / adcRaw) - RLOAD;
    float rSrO = rS / RO_BERSIH;

    // Menghapus pengecekan ambang batas (if-else)
    // Sekarang, semua nilai akan dihitung dan ditampilkan
    float ppm = KURVA_A * pow(rSrO, KURVA_B);
    averageValue.push(ppm);
    co2_valid = true; // Selalu set `true` agar nilai ditampilkan
    return averageValue.average();
}

void cekDanKontrolAktuator(float temp, float hum, float co2) {
    unsigned long currentTime = millis();
    if ((temp > 35.0 || temp < 27.0) && hum < 40.0) {
        if (pompaMatiMillis == 0) { 
            digitalWrite(RELAY_PIN, HIGH);
            pompaMatiMillis = currentTime + 5000;
        }
    }
    if (co2_valid && co2 > 1000) {
        if (buzzerMatiMillis == 0) {
            digitalWrite(BUZZER_PIN, HIGH);
            buzzerMatiMillis = currentTime + 2000;
        }
    }
}

// --- FUNGSI UTILITAS & STATUS ---

void interpolate_image(float *src, float *dest, int src_rows, int src_cols, int dest_rows, int dest_cols) {
    float row_ratio = (float)(src_rows - 1) / (dest_rows - 1);
    float col_ratio = (float)(src_cols - 1) / (dest_cols - 1);
    for (int i = 0; i < dest_rows; i++) {
        for (int j = 0; j < dest_cols; j++) {
            float src_row_f = i * row_ratio; float src_col_f = j * col_ratio;
            int src_row_i = (int)src_row_f; int src_col_i = (int)src_col_f;
            float row_frac = src_row_f - src_row_i; float col_frac = src_col_f - src_col_i;
            if (src_row_i >= src_rows - 1) src_row_i = src_rows - 2;
            if (src_col_i >= src_cols - 1) src_col_i = src_cols - 2;
            float p1 = src[src_row_i * src_cols + src_col_i];
            float p2 = src[src_row_i * src_cols + src_col_i + 1];
            float p3 = src[(src_row_i + 1) * src_cols + src_col_i];
            float p4 = src[(src_row_i + 1) * src_cols + src_col_i + 1];
            float c1 = p1 * (1 - col_frac) + p2 * col_frac;
            float c2 = p3 * (1 - col_frac) + p4 * col_frac;
            dest[i * dest_cols + j] = c1 * (1 - row_frac) + c2 * row_frac;
        }
    }
}

String getTempStatus(float temp) {
    if (temp >= 27 && temp <= 30) {
        return "Bagus";
    } else if (temp >= 31 && temp <= 35) {
        return "Sedang";
    } else if (temp >= 36) {
        return "Bahaya";
    } else {
        return "Dingin"; // Untuk suhu di bawah 27
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

String getCO2Status(float co2) {
    if (co2 >= 0 && co2 < 500) {
        return "Aman";
    } else if (co2 >= 500 && co2 < 1000) {
        return "Sedang";
    } else { // Mencakup >= 1000
        return "Bahaya";
    }
}
