<?php

namespace r3pt1s\socketlib\socket\handler\decode;

use r3pt1s\socketlib\socket\util\JsonBuffer;

/** @template TValue */
interface IDecoder {

    /** @return TValue */
    public function decode(JsonBuffer $jsonBuffer): mixed;
}