<?php

namespace r3pt1s\socketlib\socket\handler;

use r3pt1s\socketlib\socket\util\Address;
use Throwable;

/** @template TValue */
interface IServerHandler {

    public function bound(Address $address): void;

    /**
     * @param Address $address
     * @param TValue $message
     * @return void
     */
    public function messageReceived(Address $address, $message): void;

    /**
     * @param Address $address
     * @param TValue $message
     * @return void
     */
    public function messageSent(Address $address, $message): void;

    public function exceptionCaught(Throwable $t, ?Address $address = null): void;
}