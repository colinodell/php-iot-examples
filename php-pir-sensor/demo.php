<?php

// Configuration:

const GPIO_PIN = 17;
const DECLARE_ROOM_EMPTY_AFTER = 60*5; // 5 minutes
const SLACK_INCOMING_WEBHOOK_URL = 'https://hooks.slack.com/services/xxxx/xxxx/xxxx';

// End configuration

require_once 'vendor/autoload.php';

use ColinODell\PHPIoTExamples\PHPPIRSensor\Room;
use PiPHP\GPIO\GPIO;
use PiPHP\GPIO\Pin\InputPinInterface;

// HTTP client for communicating with Slack
$http = new \GuzzleHttp\Client();

// Instantiate a new room object to keep track of its state
$room = new Room();
$room->setRoomEmptyTimeout(DECLARE_ROOM_EMPTY_AFTER);
$room->setOnRoomChangeCallback(function($isOccupied) use ($http) {
    $message = 'The conference room is now ' . ($isOccupied ? 'occupied.' : 'vacant.');

    $http->postAsync(SLACK_INCOMING_WEBHOOK_URL, [
        'json' => [
            'text' => $message,
        ],
    ]);
});

// Create a GPIO object
$gpio = new GPIO();

// Set the pin to watch for rising edges
$pin = $gpio->getInputPin(GPIO_PIN);
$pin->setEdge(InputPinInterface::EDGE_RISING);

// Call $room->motionDetected() whenever the pin goes HIGH
$interruptWatcher = $gpio->createWatcher();
$interruptWatcher->register($pin, function($pin, $value) use ($room) {
    $room->motionDetected();
});

// Run forever
while (true) {
    $interruptWatcher->watch(DECLARE_ROOM_EMPTY_AFTER * 1000);
    $room->tick();
}
