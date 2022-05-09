#include <SPI.h>
#include <nRF24L01.h>
#include <RF24.h>

const int wifi_write_pin = 5;

RF24 radio(7, 8); // CE, CSN

const byte address[6] = "00001";

int pause = 100;
int num_bits = 12;   // exclusive start bits

void setup(){ 
  //Serial.begin(115200);

  // Prepere arduino receiver antenna
  radio.begin();
  radio.openReadingPipe(0, address);
  radio.setPALevel(RF24_PA_MIN);
  radio.startListening();

  // Prepere connection to wifi module
  pinMode(wifi_write_pin, OUTPUT);
  digitalWrite(wifi_write_pin, LOW);

  //Serial.println("Setup done!");
 
  delay(50);
}


void loop(){
  Serial.println(11);
  //delay(1000);
  if (radio.available()) {    
    // receive from arduino 1
    int data;
    radio.read(&data, sizeof(data));

    //Serial.print("Arduino 2 received: ");
    //Serial.println(data);

    // transmit to wifi module
    if (data == 1) {
      digitalWrite(wifi_write_pin, HIGH);
      delay(pause);
      digitalWrite(wifi_write_pin, LOW);
      delay(pause);
    }
    else if (data != 0) {    
      for (int i = 0; i < num_bits; i++) {
        if (data & 1) {
          digitalWrite(wifi_write_pin, HIGH);    
        }
        else {
          digitalWrite(wifi_write_pin, LOW);    
        }
        data >>= 1;
        delay(pause);
      }
    }
    digitalWrite(wifi_write_pin, LOW);
  }
}
