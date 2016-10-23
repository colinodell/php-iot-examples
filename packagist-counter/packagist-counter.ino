// This #include statement was automatically added by the Particle IDE.
#include "LedControl-MAX7219-MAX7221/LedControl-MAX7219-MAX7221.h"

#include <math.h>

LedControl lc=LedControl(D6,D4,D5,1);

int count = 0;

void setup() {
    
  /*
   The MAX72XX is in power-saving mode on startup,
   we have to do a wakeup call
   */
  lc.shutdown(0,false);
  /* and clear the display */
  lc.clearDisplay(0);
  
  Particle.subscribe("hook-response/packagist-count", gotPackagistCount, MY_DEVICES);
  
  lc.setChar(0, 0, '-', true);
  lc.setChar(0, 1, '-', false);
  lc.setChar(0, 2, '-', false);
  lc.setChar(0, 3, '-', false);
  lc.setChar(0, 4, '-', false);
  lc.setChar(0, 5, '-', false);
  lc.setChar(0, 6, '-', false);
  lc.setChar(0, 7, '-', false);
  
  Particle.publish("packagist-count");
}

void loop() { 
    // Fetch updates every 3 minutes
    delay(1000 * 60 * 3);
    
    // Set the last decimal point to indicate that we're fetching an update
    // To do this, we need to set the last digit as well
    lc.setDigit(0, 0, count % 10, true);
    
    Particle.publish("packagist-count");
}

void gotPackagistCount(const char *name, const char *data) {
    char *key = "total\":\"";
    char *ptrTotal = strstr(data, key);
    if (ptrTotal == NULL) {
        return;
    }
    
    ptrTotal += strlen(key);
    
    int x = 0;
    char result[8];
    while (x < 8 && *ptrTotal != '"') {
        result[x++] = *ptrTotal++;
    }
    result[x] = '\0';
    count = strtol(result, NULL, 10);
    
    // Display the count
    displayCount();
}

void displayCount() {
    int i = 0;
    int n = numPlaces(count);
    do {
        int digit = count % 10;
        lc.setDigit(0, i, digit, false);
        i++;
        count=(int)(count/10);
    } while (i < n);
    
    while(i < 8) {
        lc.setChar(0, i, ' ', false);
        i++;
    }
}

int numPlaces (int n) {
    if (n == 0) return 1;
    return floor (log10 (abs (n))) + 1;
}

