<?php

namespace r3pt1s\socketlib\builder\options;

use pmmp\thread\ThreadSafe;
use pmmp\thread\ThreadSafeArray;
use Socket;

class SocketOptions extends ThreadSafe {

    private bool $blocking = true;
    private int $receiveBuffer = 1024;
    private int $sendBuffer = 1024;
    private ThreadSafeArray $receiveTimeout;

    public function __construct() {
        $this->receiveTimeout = ThreadSafeArray::fromArray(["sec" => 30, "usec" => 0]);
    }

    public function blocking(bool $blocking): self {
        $this->blocking = $blocking;
        return $this;
    }

    public function receiveBuffer(int $length): self {
        $this->receiveBuffer = $length;
        return $this;
    }

    public function sendBuffer(int $length): self {
        $this->sendBuffer = $length;
        return $this;
    }

    public function receiveTimeout(int $seconds, int $microseconds = 0): self {
        $this->receiveTimeout = ThreadSafeArray::fromArray(["sec" => $seconds, "usec" => $microseconds]);
        return $this;
    }

    /** @internal */
    public function apply(Socket $socket): void {
        ($this->blocking ? @socket_set_block($socket) : @socket_set_nonblock($socket));
        @socket_set_option($socket, SOL_SOCKET, SO_SNDBUF, $this->sendBuffer);
        @socket_set_option($socket, SOL_SOCKET, SO_RCVBUF, $this->receiveBuffer);
        @socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, $this->receiveTimeout);
    }

    public function isBlocking(): bool {
        return $this->blocking;
    }

    public function getReceiveBuffer(): int {
        return $this->receiveBuffer;
    }

    public function getSendBuffer(): int {
        return $this->sendBuffer;
    }

    public function getReceiveTimeout(): array {
        return (array) $this->receiveTimeout;
    }

    public static function create(): self {
        return new SocketOptions();
    }
}