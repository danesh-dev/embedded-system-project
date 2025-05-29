#include <SoftwareSerial.h>
#include <Wire.h>
#include <TinyGPSPlus.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_ADXL345_U.h>

// ========== PIN CONFIGURATION ==========
#define SIM_RX 7         
#define SIM_TX 8         
#define EMERGENCY_BTN 2 
#define ACC_OFF_BTN 5    


// ========== OBJECTS ==========
SoftwareSerial sim800(SIM_RX, SIM_TX);
TinyGPSPlus gps;
SoftwareSerial gpsSerial(7, 8); // Hardware UART ideally used for GPS
Adafruit_ADXL345_Unified accel = Adafruit_ADXL345_Unified(123);

// ========== SETTINGS ==========
const char* contact1 = "+989143329942";
const char* contact2 = "+98945647047";
const unsigned long LOCATION_INTERVAL = 60000; // 1 minute interval

// ========== STATE ==========
bool accidentDetected = false;
bool accidentMonitoring = true;
unsigned long lastLocationSent = 0;
float lastAccelMag = 0;

void setup() {
  // Basic setup
  pinMode(EMERGENCY_BTN, INPUT_PULLUP);
  pinMode(ACC_OFF_BTN, INPUT_PULLUP);

  sim800.begin(9600);
  gpsSerial.begin(9600);
  Serial.begin(9600);

  // Initialize ADXL345
  if (!accel.begin()) {
    Serial.println("ADXL345 not detected");
    while (1);
  }
  accel.setRange(ADXL345_RANGE_16_G);

  // SIM800L Init
  delay(2000);
  sendAT("AT");
  sendAT("AT+CMGF=1");
  sendAT("AT+CNMI=1,2,0,0,0");  // Immediate SMS forwarding

  Serial.println("System initialized.");
}

void loop() {
  readGPS();
  checkEmergencyButton();
  checkAccident();

  if (millis() - lastLocationSent > LOCATION_INTERVAL) {
    sendLocationToServer();
    lastLocationSent = millis();
  }
}

// ========== FUNCTIONS ==========

void checkEmergencyButton() {
  if (digitalRead(EMERGENCY_BTN) == LOW) {
    delay(100); // debounce
    if (digitalRead(EMERGENCY_BTN) == LOW) {
      sendEmergencySMS("I Am In Danger, Help.");
    }
  }

  if (digitalRead(ACC_OFF_BTN) == LOW) {
    accidentMonitoring = false;
  }
}

void checkAccident() {
  if (!accidentMonitoring || accidentDetected) return;

  sensors_event_t event;
  accel.getEvent(&event);
  float mag = sqrt(event.acceleration.x * event.acceleration.x +
                   event.acceleration.y * event.acceleration.y +
                   event.acceleration.z * event.acceleration.z);

  if (abs(mag - lastAccelMag) > 15.0) {
    accidentDetected = true;
    sendEmergencySMS("Possible Accident Detected!");
  }

  lastAccelMag = mag;
}

void readGPS() {
  while (gpsSerial.available()) {
    gps.encode(gpsSerial.read());
  }
}

String getLocationString() {
  if (!gps.location.isValid()) {
    return "GPS not fixed";
  }
  float lat = gps.location.lat();
  float lon = gps.location.lng();
  return "Lat: " + String(lat, 6) + ", Lon: " + String(lon, 6);
}

void sendEmergencySMS(String alertMsg) {
  String msg = alertMsg + "\nLocation:\n" + getLocationString();
  sendSMS(contact1, msg);
  sendSMS(contact2, msg);
}

void sendSMS(String number, String message) {
  sim800.println("AT+CMGS=\"" + number + "\"");
  delay(500);
  sim800.print(message);
  sim800.write(26); // CTRL+Z to send
  delay(2000);
}


void sendLocationToServer() {
  if (!gps.location.isValid()) return;

  String url = "http://yourdomain.com/api/gps?lat=" +
               String(gps.location.lat(), 6) +
               "&lon=" + String(gps.location.lng(), 6);

  sendAT("AT+HTTPTERM");
  sendAT("AT+HTTPINIT");
  sendAT("AT+HTTPPARA=\"CID\",1");
  sendAT("AT+HTTPPARA=\"URL\",\"" + url + "\"");
  sendAT("AT+HTTPACTION=0"); // GET
  delay(3000);
  sendAT("AT+HTTPTERM");
}

void sendAT(String cmd) {
  sim800.println(cmd);
  delay(500);
  while (sim800.available()) {
    Serial.write(sim800.read());
  }
}
