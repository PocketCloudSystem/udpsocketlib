<?php

namespace r3pt1s\socketlib\socket\type\server;

use Closure;
use LogicException;
use pmmp\thread\Thread;
use pmmp\thread\ThreadSafeArray;
use pocketmine\snooze\SleeperHandlerEntry;
use r3pt1s\socketlib\builder\options\SocketOptions;
use r3pt1s\socketlib\Network;
use r3pt1s\socketlib\socket\util\Address;
use r3pt1s\socketlib\socket\util\JsonBuffer;
use r3pt1s\socketlib\socket\util\UnconnectedMessage;
use Socket;
use Throwable;

class SocketServer extends Thread {

    private ?Socket $socket = null;
    private bool $bound = false;
    private ?string $throwable = null;
    private ThreadSafeArray $buffer;
    private SleeperHandlerEntry $sleeperHandlerEntry;

    public function __construct(
        private readonly Address $address,
        private readonly SocketOptions $options,
        private readonly Closure $classLoadClosure
    ) {
        $this->buffer = new ThreadSafeArray();
        $this->sleeperHandlerEntry = Network::getInstance()->getEventLoop()->addNotifier(function (): void {
            if (($throwable = $this->getThrowable()) !== null) {
                Network::getInstance()->getHandler()?->exceptionCaught($throwable);
                return;
            }

            while (($buffer = $this->buffer->shift()) !== null && $buffer instanceof UnconnectedMessage) {
                $decode = Network::getInstance()->isEncryption() ? new JsonBuffer(
                    base64_decode(zlib_decode($buffer->getBuffer()))
                ) : new JsonBuffer($buffer->getBuffer());
                if (Network::getInstance()->getDecoder() !== null) $decode = Network::getInstance()->getDecoder()->decode($decode);
                Network::getInstance()->getHandler()?->messageReceived($buffer->getAddress(), $decode);
            }
        });
    }

    public function run(): void {
        ($this->classLoadClosure)();
        do {
            $notifier = $this->sleeperHandlerEntry->createNotifier();
            try {
                if ($this->read($buffer, $address, $port) !== false) {
                    $this->buffer[] = new UnconnectedMessage(Address::create($address, $port), $buffer);
                    $notifier->wakeupSleeper();
                }
            } catch (Throwable $throwable) {
                $this->throwable = igbinary_serialize($throwable);
                $notifier->wakeupSleeper();
                break;
            }
        } while ($this->bound);
    }

    public function init(): void {
        if ($this->bound) throw new LogicException("The socket is already bound to " . $this->address . "!");
        $this->socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        $this->bound = @socket_bind($this->socket, $this->address->getAddress(), $this->address->getPort());
        if (!$this->bound) throw new LogicException("The socket is already bound to " . $this->address . "!");
        $this->options->apply($this->socket);
        Network::getInstance()->getHandler()?->bound($this->address);
    }

    public function close(): void {
        if ($this->bound) {
            Network::getInstance()->getHandler()?->close();
            socket_close($this->socket);
            $this->bound = false;
        }
    }

    public function write($buffer, Address $address): bool {
        Network::getInstance()->getHandler()?->messageSent($address, $buffer);
        $jsonBuffer = new JsonBuffer();
        if (Network::getInstance()->getEncoder() !== null) Network::getInstance()->getEncoder()?->encode($buffer, $jsonBuffer);
        else $jsonBuffer->write($buffer);
        $buffer = (Network::getInstance()->isEncryption() ? zlib_encode(base64_encode($jsonBuffer), ZLIB_ENCODING_GZIP) : (string) $jsonBuffer);
        return @socket_sendto($this->socket, $buffer, strlen($buffer), 0, $address->getAddress(), $address->getPort()) !== false;
    }

    public function read(?string &$buffer, ?string &$address, ?int &$port): bool {
        if (!$this->isBound()) return false;
        return @socket_recvfrom($this->socket, $buffer, 65535, 0, $address, $port) !== false;
    }

    public function getSocket(): ?Socket {
        return $this->socket;
    }

    public function isBound(): bool {
        return $this->bound;
    }

    public function getThrowable(): ?Throwable {
        return $this->throwable === null ? null : igbinary_unserialize($this->throwable);
    }

    public function getAddress(): Address {
        return $this->address;
    }

    public function getOptions(): SocketOptions {
        return $this->options;
    }

    public function getSleeperHandlerEntry(): SleeperHandlerEntry {
        return $this->sleeperHandlerEntry;
    }
}