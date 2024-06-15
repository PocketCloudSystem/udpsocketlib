<?php

namespace r3pt1s\socketlib\socket\util;

use pmmp\thread\ThreadSafe;

class UnconnectedMessage extends ThreadSafe {

    public function __construct(
        private readonly Address $address,
        private readonly string $buffer
    ) {}

    public function getAddress(): Address {
        return $this->address;
    }

    public function getBuffer(): string {
        return $this->buffer;
    }
}