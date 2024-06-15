<?php

namespace r3pt1s\socketlib;

use Closure;
use r3pt1s\socketlib\builder\options\SocketOptions;
use pocketmine\snooze\SleeperHandler;
use r3pt1s\socketlib\socket\handler\decode\IDecoder;
use r3pt1s\socketlib\socket\handler\encode\IEncoder;
use r3pt1s\socketlib\socket\handler\IClientHandler;
use r3pt1s\socketlib\socket\handler\IServerHandler;
use r3pt1s\socketlib\socket\type\client\SocketClient;
use r3pt1s\socketlib\socket\type\server\SocketServer;
use r3pt1s\socketlib\socket\type\SocketType;
use r3pt1s\socketlib\socket\util\Address;

class Network {

    private static self $instance;
    private SocketServer|SocketClient $socket;

    public function __construct(
        private readonly Address $address,
        private readonly SocketType $socketType,
        private readonly SocketOptions $options,
        private readonly SleeperHandler $eventLoop,
        private readonly IServerHandler|IClientHandler|null $handler,
        private readonly ?IEncoder $encoder,
        private readonly ?IDecoder $decoder,
        private readonly bool $encryption,
        private readonly int $threadOptions,
        private readonly Closure $classLoadClosure
    ) {
        self::$instance = $this;
        $this->socket = match ($this->socketType) {
            SocketType::CLIENT => SocketClient::create($this->address, $this->options, $this->classLoadClosure),
            default => new SocketServer($this->address, $this->options, $this->classLoadClosure)
        };

        $this->socket->init();
        $this->socket->start($this->threadOptions);
    }

    public function stop(): void {
        $this->socket->close();
    }

    public function getAddress(): Address {
        return $this->address;
    }

    public function getSocketType(): SocketType {
        return $this->socketType;
    }

    public function getOptions(): SocketOptions {
        return $this->options;
    }

    public function getEventLoop(): SleeperHandler {
        return $this->eventLoop;
    }

    public function getHandler(): IServerHandler|IClientHandler|null {
        return $this->handler;
    }

    public function getEncoder(): ?IEncoder {
        return $this->encoder;
    }

    public function getDecoder(): ?IDecoder {
        return $this->decoder;
    }

    public function isEncryption(): bool {
        return $this->encryption;
    }

    public function getThreadOptions(): int {
        return $this->threadOptions;
    }

    public function getSocket(): SocketServer|SocketClient {
        return $this->socket;
    }

    public static function getInstance(): Network {
        return self::$instance;
    }
}