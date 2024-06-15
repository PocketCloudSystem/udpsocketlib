<?php

namespace test;

use r3pt1s\socketlib\socket\handler\IClientHandler;
use r3pt1s\socketlib\socket\util\Address;

class ImplClientHandler implements IClientHandler {

    public function connected(Address $address): void {
        echo "Successfully connected to the server: $address\n";
    }

    public function messageReceived($message): void {
        echo "Data received from the server: $message\n";
    }

    public function messageSent($message): void {
        echo "Data sent to the server: $message\n";
    }

    public function exceptionCaught(\Throwable $t): void {
        throw $t;
    }
}