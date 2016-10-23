<?php

namespace ColinODell\PHPIoTExamples\PHPPIRSensor;

class Room
{
    /**
     * @var bool
     */
    private $occupied = false;

    /**
     * @var int
     */
    private $lastMovement;

    /**
     * @var callable
     */
    private $onRoomStateChange;

    /**
     * @var int
     */
    private $roomEmptyTimeout;

    /**
     * Room constructor.
     */
    public function __construct()
    {
        $this->lastMovement = time();
    }

    /**
     * After $timeout seconds of no movement, the room will be marked as vacant.
     *
     * @param int $timeout
     */
    public function setRoomEmptyTimeout($timeout)
    {
       $this->roomEmptyTimeout = $timeout;
    }

    /**
     * Provide a callback to respond to the room becoming vacant or occupied.
     *
     * A single boolean parameter will be passed, denoting whether:
     *   - The room is now occupied (true)
     *   - The room is now vacant (false)
     *
     * @param callable $callback
     */
    public function setOnRoomChangeCallback(callable $callback)
    {
        $this->onRoomStateChange = $callback;
    }

    /**
     * Call this function whenever there is motion.
     *
     * @return void
     */
    public function motionDetected()
    {
        $this->setOccupied(true);

        $this->lastMovement = time();
    }

    /**
     * Call this function regularly to see if enough time has elapsed
     * to mark the room vacant.
     *
     * @return void
     */
    public function tick()
    {
        // If there's been no movement lately, set the room to vacant
        if (time() - $this->lastMovement >= $this->roomEmptyTimeout) {
            $this->setOccupied(false);
        }
    }

    /**
     * @param bool $newState
     */
    private function setOccupied($newState)
    {
        if ($newState !== $this->occupied) {
            $this->occupied = $newState;
            if (is_callable($this->onRoomStateChange)) {
                $func = $this->onRoomStateChange;
                $func($newState);
            }
        }
    }
}
