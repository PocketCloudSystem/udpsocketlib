<?php

namespace r3pt1s\socketlib\builder;

use pmmp\thread\Thread;
use pocketmine\snooze\SleeperHandler;
use r3pt1s\socketlib\builder\options\SocketOptions;
use r3pt1s\socketlib\Network;
use r3pt1s\socketlib\socket\handler\decode\IDecoder;
use r3pt1s\socketlib\socket\handler\encode\IEncoder;
use r3pt1s\socketlib\socket\handler\IClientHandler;
use r3pt1s\socketlib\socket\handler\IServerHandler;
use r3pt1s\socketlib\socket\type\SocketType;
use r3pt1s\socketlib\socket\util\Address;

class NetworkBuilder {

    public static function client(
        Address $address,
        SocketOptions $options,
        SleeperHandler $eventLoop,
        \Closure $classLoadClosure,
        ?IClientHandler $clientHandler = null,
        ?IEncoder $encoder = null,
        ?IDecoder $decoder = null,
        bool $encryption = true,
        int $threadOptions = Thread::INHERIT_NONE
    ): Network {
        return new Network(
            $address,
            SocketType::CLIENT,
            $options,
            $eventLoop,
            $clientHandler,
            $encoder,
            $decoder,
            $encryption,
            $threadOptions,
            $classLoadClosure
        );
    }

    public static function server(
        Address $address,
        SocketOptions $options,
        SleeperHandler $eventLoop,
        \Closure $classLoadClosure,
        ?IServerHandler $serverHandler = null,
        ?IEncoder $encoder = null,
        ?IDecoder $decoder = null,
        bool $encryption = true,
        int $threadOptions = Thread::INHERIT_NONE
    ): Network {
        return new Network(
            $address,
            SocketType::SERVER,
            $options,
            $eventLoop,
            $serverHandler,
            $encoder,
            $decoder,
            $encryption,
            $threadOptions,
            $classLoadClosure
        );
    }
}