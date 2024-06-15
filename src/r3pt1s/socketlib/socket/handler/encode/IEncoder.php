<?php

namespace r3pt1s\socketlib\socket\handler\encode;

use r3pt1s\socketlib\socket\util\JsonBuffer;

/** @template TValue */
interface IEncoder {

    /**
     * @param TValue $object
     * @param JsonBuffer $jsonBuffer
     * @return void
     */
    public function encode(mixed $object, JsonBuffer $jsonBuffer): void;
}