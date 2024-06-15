<?php

namespace test;

use pocketmine\snooze\SleeperHandler;
use r3pt1s\socketlib\builder\NetworkBuilder;
use r3pt1s\socketlib\builder\options\SocketOptions;
use r3pt1s\socketlib\socket\util\Address;

spl_autoload_register(function($class): void {
    if (class_exists($class)) return;
    if (str_contains($class, "socketlib\\")) require __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . $class . ".php";
    else if (str_contains($class, "test\\")) require getcwd() . "/../" . $class . ".php";
    else require __DIR__ . DIRECTORY_SEPARATOR . $class . ".php";
});

require __DIR__ . DIRECTORY_SEPARATOR . "ImplServerHandler.php";

$handler = new SleeperHandler();

try {
    $net = NetworkBuilder::server(
        Address::create("127.0.0.1", 9273),
        SocketOptions::create(),
        $handler,
        static function () {
            spl_autoload_register(function($class): void {
                $requirePath = "";
                if (str_contains($class, "pocketmine\\")) {
                    $requirePath = __DIR__ . DIRECTORY_SEPARATOR;
                } else if (str_contains($class, "socketlib\\")) {
                    $requirePath = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR;
                } else if (str_contains($class, "test\\")) {
                    $requirePath = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
                }

                if ($requirePath !== "") require $requirePath . str_replace(["\\\\", "\\"], DIRECTORY_SEPARATOR, $class) . ".php";
            });
        },
        new ImplServerHandler(),
        new ImplEncoderAndDecoder(),
        new ImplEncoderAndDecoder()
    );
} catch (\Throwable $throwable) {
    var_dump($throwable);
}

$start = microtime(true);
while (true) {
    $handler->sleepUntil($start);
    usleep(50 * 1000);
}