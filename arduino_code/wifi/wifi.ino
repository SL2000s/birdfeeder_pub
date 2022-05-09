#include <ESP8266WiFi.h>
#include <WiFiClientSecure.h>


// Constants for wifi connection
const char* ssid = "WIFI NAME";                       // <---------- fill in here
const char* password = "WIFI PASSWORD";               // <---------- fill in here
const char* host = "birdfeedercollective.com";
const int httpsPort = 443;
const char fingerprint[] PROGMEM = "624de4430f06489d6e0c5672627aa1acdcdb4a98"; //"08 59 A7 FC A2 9E 01 DA E4 D3 6B B7 C0 57 32 D3 30 A6 85 73";

// Constants for receiving data
const int arduino_read_pin = 2;
int pause = 100;
int num_bits = 12;   // exclusive start bits

// Constans for sending data
String url_prefix = "/upload.php?pwd=PASSWORD&";      // <---------- fill in here

void setup() {
  Serial.begin(115200);
 
  pinMode(arduino_read_pin, INPUT);

  Serial.println("Setup done!");
  delay(1000);
}


void loop() {
  if (digitalRead(arduino_read_pin) == HIGH){ 
    delay(pause + (pause >> 1));
    String url_param = "";
    if (digitalRead(arduino_read_pin) == LOW) {
      url_param = "p=1";
      delay(pause);
    }
    else {
      delay(pause);
      int rec_data = 0;
      int two_pot = 1;    
      for (int i = 0; i < num_bits; i++) {
        if (digitalRead(arduino_read_pin) == HIGH) {
          rec_data += two_pot;
        }
        two_pot <<= 1;
        delay(pause);
      }
      if (rec_data != 4095) {
        url_param = "w=" + String(rec_data); 
      }
    }
    
    Serial.print("Wifi module url parameters: ");
    Serial.println(url_param);

    String url = url_prefix + url_param;
    
    Serial.print("connecting to ");
    Serial.println(ssid);
    WiFi.mode(WIFI_STA);
    WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED) {
      delay(500);
      Serial.print(".");
    }
    Serial.println("");
    Serial.println("WiFi connected");
    Serial.println("IP address: ");
    Serial.println(WiFi.localIP());
  
    // Use WiFiClientSecure class to create TLS connection
    WiFiClientSecure client;
    Serial.print("connecting to ");
    Serial.println(host);
  
    Serial.printf("Using fingerprint '%s'\n", fingerprint);
    client.setFingerprint(fingerprint);
  
    if (!client.connect(host, httpsPort)) {
      Serial.println("connection failed");
      return;
    }
  
    Serial.print("requesting URL: ");
    Serial.println(url);
  
    client.print(String("GET ") + url + " HTTP/1.1\r\n" +
                 "Host: " + host + "\r\n" +
                 "User-Agent: BuildFailureDetectorESP8266\r\n" +
                 "Connection: close\r\n\r\n");
  
    Serial.println("request sent");
    while (client.connected()) {
      String line = client.readStringUntil('\n');
      if (line == "\r") {
        Serial.println("headers received");
        break;
      }
    }
    String line = client.readStringUntil('\n');
    if (line.startsWith("{\"state\":\"success\"")) {
      Serial.println("esp8266/Arduino CI successfull!");
    } else {
      Serial.println("esp8266/Arduino CI has failed");
    }
    Serial.println("reply was:");
    Serial.println("==========");
    Serial.println(line);
    Serial.println("==========");
    Serial.println("closing connection");

  }  
}
