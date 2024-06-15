<?php

namespace r3pt1s\socketlib\socket\handler;

use r3pt1s\socketlib\socket\util\Address;
use Throwable;

/** @template TValue */
interface IClientHandler {

    public function connected(Address $address): void;

    /**
     * @param TValue $message
     * @return void
     */
    public function messageReceived($message): void;

    /**
     * @param TValue $message
     * @return void
     */
    public function messageSent($message): void;

    public function exceptionCaught(Throwable $t): void;
}