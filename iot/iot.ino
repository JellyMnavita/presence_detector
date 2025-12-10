#include <Adafruit_Fingerprint.h>

// ESP32 Serial2 (UART)
HardwareSerial mySerial(2);

Adafruit_Fingerprint finger = Adafruit_Fingerprint(&mySerial);

void setup() {
  Serial.begin(115200);

  // ESP32 Serial2 → RX=16 / TX=17
  mySerial.begin(57600, SERIAL_8N1, 16, 17);

  Serial.println("Initialisation du capteur...");
  delay(2000);

  if (finger.verifyPassword()) {
    Serial.println("Capteur détecté !");
  } else {
    Serial.println("ERREUR : Capteur non détecté !");
    while (true);
  }

  finger.getParameters();
  finger.getTemplateCount();

  Serial.print("Nombre d'empreintes enregistrées : ");
  Serial.println(finger.templateCount);
}

void loop() {
  Serial.println("1 = Enregistrer Empreinte");
  Serial.println("2 = Vérifier Empreinte");
  Serial.println("Choix : ");

  while (!Serial.available());
  int choix = Serial.parseInt();

  if (choix == 1) {
    enregistrerEmpreinte();
  }
  else if (choix == 2) {
    verifierEmpreinte();
  }
}

// ------------------------
// ENREGISTREMENT
// ------------------------
void enregistrerEmpreinte() {
  Serial.println("Place ton doigt...");

  int p = -1;
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    if (p == FINGERPRINT_NOFINGER) continue;
    if (p == FINGERPRINT_PACKETRECIEVEERR) continue;
    if (p != FINGERPRINT_OK) {
      Serial.println("Erreur capture");
      return;
    }
  }

  Serial.println("Image OK, conversion...");
  if (finger.image2Tz(1) != FINGERPRINT_OK) {
    Serial.println("Erreur conversion");
    return;
  }

  Serial.println("Retire ton doigt");
  delay(2000);

  Serial.println("Repose le doigt...");

  p = -1;
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    if (p == FINGERPRINT_NOFINGER) continue;
    if (p != FINGERPRINT_OK) {
      Serial.println("Erreur capture 2");
      return;
    }
  }

  Serial.println("Conversion 2...");
  if (finger.image2Tz(2) !
