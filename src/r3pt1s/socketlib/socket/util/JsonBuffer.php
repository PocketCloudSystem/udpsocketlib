<?php

namespace r3pt1s\socketlib\socket\util;

use JsonException;

class JsonBuffer {

    private array $data;

    public function __construct(string $json = "[]") {
        try {
            $this->data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $this->data = [];
        }
    }

    public function write(mixed $data): self {
        $this->data[] = $data;
        return $this;
    }

    public function writeString(string $data): self {
        return $this->write($data);
    }

    public function writeInt(int $data): self {
        return $this->write($data);
    }

    public function writeFloat(int $data): self {
        return $this->write($data);
    }

    public function writeArray(array $data): self {
        return $this->write($data);
    }

    public function read(): mixed {
        if (count($this->data) > 0) {
            $get = array_shift($this->data);
            $this->data = array_values($this->data);
            return $get;
        }
        return null;
    }

    public function readString(): ?string {
        return $this->read();
    }

    public function readInt(): ?int {
        return $this->read();
    }

    public function readFloat(): ?float {
        return $this->read();
    }

    public function readArray(): ?array {
        $array = $this->read();
        if (!is_array($array)) return null;
        return $array;
    }

    public function getRawData(): array {
        return $this->data;
    }

    /** @throws JsonException */
    public function __toString(): string {
        return json_encode($this->data, JSON_THROW_ON_ERROR);
    }
}