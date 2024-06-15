<?php

namespace test;

class TextObject {

    public function __construct(private readonly string $text) {}

    public function getText(): string {
        return $this->text;
    }

    public function __toString(): string {
        return "TextObject(text={$this->text})";
    }
}