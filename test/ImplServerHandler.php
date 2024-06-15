<?php

namespace test;

use r3pt1s\socketlib\Network;
use r3pt1s\socketlib\socket\handler\IServerHandler;
use r3pt1s\socketlib\socket\util\Address;

/** @implements IServerHandler<TextObject> */
class ImplServerHandler implements IServerHandler {

    public function bound(Address $address): void {
        echo "Successfully bound to the address: $address\n";
    }

    /**
     * @param Address $address
     * @param TextObject $message
     * @return void
     */
    public function messageReceived(Address $address, $message): void {
        echo "[$address] Data received from client: $message\n";
        Network::getInstance()->getSocket()->write(new TextObject("anfrage wurde erhalten du hurensohn"), $address);
    }

    /**
     * @param Address $address
     * @param TextObject $message
     * @return void
     */
    public function messageSent(Address $address, $message): void {
        echo "[$address] Data sent to client: $message\n";
    }

    public function exceptionCaught(\Throwable $t, ?Address $address = null): void {
        throw $t;
    }
}