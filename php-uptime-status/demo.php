<?php

const URL = 'https://www.colinodell.com';
const GPIO_PIN_GREEN = 12;
const GPIO_PIN_RED = 16;

require_once 'vendor/autoload.php';

$gpio = new PiPHP\GPIO\GPIO();
$greenLed = $gpio->getOutputPin(GPIO_PIN_GREEN);
$redLed = $gpio->getOutputPin(GPIO_PIN_RED);

$httpClient = new GuzzleHttp\Client();

while (true) {
    try {
        $response = $httpClient->head(URL, [
            'connect_timeout' => 3,
            'timeout' => 3,
        ]);

        echo URL . " is online\n";
        $greenLed->setValue(1);
        $redLed->setValue(0);
    } catch (\RuntimeException $ex) {
        echo URL . " is OFFLINE!\n";
        $greenLed->setValue(0);
        $redLed->setValue(1);
    }

    sleep(3);
}
