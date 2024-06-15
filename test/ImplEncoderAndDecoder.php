<?php

namespace test;


use r3pt1s\socketlib\socket\handler\decode\IDecoder;
use r3pt1s\socketlib\socket\handler\encode\IEncoder;
use r3pt1s\socketlib\socket\util\JsonBuffer;

/** @implements IEncoder<TextObject> */
/** @implements IDecoder<TextObject> */
class ImplEncoderAndDecoder implements IEncoder, IDecoder {

    /**
     * @param TextObject $object
     * @param JsonBuffer $jsonBuffer
     * @return void
     */
    public function encode(mixed $object, JsonBuffer $jsonBuffer): void {
        $jsonBuffer->writeString($object->getText());
    }

    /**
     * @param JsonBuffer $jsonBuffer
     * @return TextObject
     */
    public function decode(JsonBuffer $jsonBuffer): TextObject {
        return new TextObject($jsonBuffer->readString());
    }
}