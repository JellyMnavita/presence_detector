/* 
  ESP8266 Fingerprint + I2C LCD + HTTP POST to PHP
  - I2C: SDA -> D1 (GPIO5), SCL -> D0 (GPIO16)
  - Fingerprint: SoftwareSerial RX->D7 (GPIO13), TX->D8 (GPIO15)
*/

#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <SoftwareSerial.h>
#include <Adafruit_Fingerprint.h>

// ---------- CONFIG ----------
const char* ssid     = "Redmi";
const char* password = "marianng";
const char* serverEndpoint = "http://192.168.43.110:8000/fingerprint_receiver.php";

// I2C
LiquidCrystal_I2C lcd(0x27, 16, 2);

// Fingerprint (SoftwareSerial)
#define FP_TX_PIN 4
#define FP_RX_PIN 5
SoftwareSerial fpSerial(FP_RX_PIN, FP_TX_PIN);
Adafruit_Fingerprint finger(&fpSerial);

unsigned long lastSend = 0;
const unsigned long sendInterval = 1000;

// Utility timestamp
String isoTimestamp() {
  return String(millis());
}

// ---------- Setup ----------
void setup() {
  Serial.begin(115200);
  delay(200);

  Serial.println("BOOTING SYSTEM...");

  // LCD
  Wire.begin(12, 13);
  lcd.init();
  lcd.backlight();
  lcd.print("Booting...");

  // WiFi
  Serial.print("Connecting to WiFi: ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);
  unsigned long start = millis();

  while (WiFi.status() != WL_CONNECTED && millis() - start < 15000) {
    delay(500);
    Serial.print(".");
  }
  Serial.println();

  if (WiFi.status() == WL_CONNECTED) {
    Serial.print("WiFi connected. IP: ");
    Serial.println(WiFi.localIP());
    lcd.clear();
    lcd.print("WiFi OK");
  } else {
    Serial.println("WiFi FAILED");
    lcd.clear();
    lcd.print("WiFi failed");
  }

  // Fingerprint init
  Serial.println("Initializing Fingerprint Sensor...");
  fpSerial.begin(57600);
  finger.begin(57600);

  if (!finger.verifyPassword()) {
    Serial.println("ERROR: Fingerprint sensor NOT detected!");
    lcd.clear();
    lcd.print("FP FAILED");
  } else {
    Serial.println("Fingerprint sensor OK!");
    lcd.clear();
    lcd.print("FP OK");
  }

  delay(600);
  lcd.clear();
  lcd.print("Ready");
  lcd.setCursor(0,1);
  lcd.print("Scan finger");
}

// ---------- POST to server ----------
void sendEventToServer(int id, int confidence, bool known) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("ERROR: WiFi not connected.");
    return;
  }

  if (millis() - lastSend < sendInterval) return;
  lastSend = millis();

  Serial.println("Sending data to server...");

  WiFiClient client;
  HTTPClient http;
  http.begin(client, serverEndpoint);
  http.addHeader("Content-Type", "application/json");

  String payload = "{\"id\":" + String(id) +
                   ",\"confidence\":" + String(confidence) +
                   ",\"known\":" + String(known ? "true" : "false") +
                   ",\"ts\":\"" + isoTimestamp() + "\"}";

  Serial.print("POST payload: ");
  Serial.println(payload);

  int httpCode = http.POST(payload);
  Serial.print("HTTP CODE = ");
  Serial.println(httpCode);

  if (httpCode > 0) {
    Serial.print("Server response: ");
    Serial.println(http.getString());
  }

  http.end();
}

// ---------- Main loop ----------
void loop() {

  Serial.println("Waiting for finger...");
  uint8_t p = finger.getImage();

  if (p == FINGERPRINT_NOFINGER) {
    // Pas de doigt dessus
    delay(150);
    return;
  }

  if (p == FINGERPRINT_OK) {
    Serial.println("Image captured!");
  } else {
    Serial.print("Error getImage code: ");
    Serial.println(p);
    return;
  }

  // Convert image
  Serial.println("Converting image...");
  p = finger.image2Tz();
  if (p != FINGERPRINT_OK) {
    Serial.print("Error image2Tz code: ");
    Serial.println(p);
    return;
  }
  Serial.println("Image converted!");

  // Searching fingerprint
  Serial.println("Searching fingerprint...");
  p = finger.fingerFastSearch();

  if (p == FINGERPRINT_OK) {
    Serial.println("Match FOUND!");
    Serial.print("ID: ");
    Serial.println(finger.fingerID);
    Serial.print("Confidence: ");
    Serial.println(finger.confidence);

    lcd.clear();
    lcd.print("ID:");
    lcd.print(finger.fingerID);
    lcd.setCursor(0,1);
    lcd.print("Conf:");
    lcd.print(finger.confidence);

    sendEventToServer(finger.fingerID, finger.confidence, true);
  }
  else {
    Serial.println("No match found!");
    lcd.clear();
    lcd.print("Unknown Finger");

    sendEventToServer(-1, 0, false);
  }

  delay(1500);
}
