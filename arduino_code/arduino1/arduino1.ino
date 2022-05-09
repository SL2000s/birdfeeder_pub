// Transmitter
#include <SPI.h>
#include <nRF24L01.h>
#include <RF24.h>

// Weight
#include <HX711_ADC.h>            // https://github.com/olkal/HX711_ADC
#include <Wire.h>

// PIR parameters
int calibrationTime = 60;         //the time we give the sensor to calibrate (10-60 secs according to the datasheet)
long unsigned int lowIn;          //the time when the sensor outputs a low impulse       
long unsigned int pause = 5000;   //the amount of milliseconds the sensor has to be low before we assume all motion has stopped
boolean lockLow = true; 
boolean takeLowTime; 
int pirPin = 3;                   //the digital pin connected to the PIR sensor's output

// Weight variables
HX711_ADC LoadCell(4, 5);         // parameters: dt pin, sck pin<span data-mce-type="bookmark" style="display: inline-block; width: 0px; overflow: hidden; line-height: 0;" class="mce_SELRES_start"></span>
unsigned long last_weight_time;
const int time_interval = 30*1000;      // ms

// Arduino transmitter antenna parameters
RF24 radio(7, 8); // CE, CSN
const byte address[6] = "00001";

// Constants at receiving side (arduino1 and wifi)
const int rec_pause = 100;
const int num_bits = 12;   // exclusive start bit


void setup() { 
  Serial.begin(115200);

  // Prepere arduino transmitter antenna
  radio.begin();
  radio.openWritingPipe(address);
  radio.setPALevel(RF24_PA_MIN);
  radio.stopListening();

  // Prepere PIR sensor output
  pinMode(pirPin, INPUT);
  digitalWrite(pirPin, LOW);

  // Give the sensor some time to calibrate
  Serial.print("calibrating sensor ");
  for(int i = 0; i < calibrationTime; i++){ 
    Serial.print(".");
    delay(1000);
  }
  
  Serial.println(" done");
  Serial.println("PIR SENSOR ACTIVE");
  Serial.print("Prepering weight sensor...");

  // Prepere weight sensor
  LoadCell.begin();             // start connection to HX711
  LoadCell.start(2000);         // load cells gets 2000ms of time to stabilize
  LoadCell.setCalFactor(999.0); // calibration factor for load cell => strongly dependent on your individual setup
  last_weight_time = millis();  // for regular weight 

  Serial.println(" done");
  Serial.println("WEIGHT SENSOR ACTIVE");
    
  delay(50);
}

void transmit_int(int data) {
  Serial.print("Arduino 1 sent: ");
  Serial.println(data);
  radio.write(&data, sizeof(data));
  delay(rec_pause*(num_bits+3));            // avoid lag
}

void loop() {
    
  if(digitalRead(pirPin) == HIGH){
    if(lockLow){ //makes sure we wait for a transition to LOW before any further output is made:
      lockLow = false;
      Serial.println("---");
      Serial.print("motion detected at ");
      Serial.print(millis()/1000);
      Serial.println(" sec");
      delay(50);

      transmit_int(1);
    }
    takeLowTime = true;
  }
  if(digitalRead(pirPin) == LOW){
    if(takeLowTime){
      lowIn = millis();
      takeLowTime = false;
    }
    if(!lockLow && millis() - lowIn > pause){
      lockLow = true;
      Serial.print("motion ended at ");
      Serial.print((millis() - pause)/1000);
      Serial.println(" sec");
      delay(50);
    }
  }

  // handle time overflow in millis() (approximately every 50 days)
  const unsigned long t = millis();
  if (t < last_weight_time) {
    last_weight_time = 0;
  }

  // take weight regularly
  if (t - last_weight_time > time_interval) {
    Serial.println("Taking weight");
    LoadCell.update();                    // retrieves data from the load cell
    int weight = LoadCell.getData();      // get output value
    if (weight < 0) {
      weight = 0;
    }
    
    Serial.print("Weight[g]: ");
    Serial.println(weight);

    int data = (weight << 2) | 3;         // add two ones in end for parsing
    transmit_int(data);
    
    last_weight_time = millis();
  }
}
