<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use React\EventLoop\Factory;
use WyriHaximus\React\GuzzlePsr7\HttpClientAdapter;

$loop = Factory::create();

(new Client([
    'handler' => HandlerStack::create(new HttpClientAdapter($loop)),
]))->getAsync('http://blog.wyrihaximus.net/', [
    'stream' => true,
])->then(function(Response $response) use ($loop) { // Success callback
    $body = $response->getBody();
    $tick = function () use ($body, $loop, &$tick) {
        do {
            $data = $body->read(1024);
            echo $data;
            echo PHP_EOL;
            echo '----------------------------------------';
            echo PHP_EOL;
            echo '|                Chunk                 |';
            echo PHP_EOL;
            echo '----------------------------------------';
            echo PHP_EOL;
        } while ($data !== '');

        if (!$body->eof()) {
            $loop->futureTick($tick);
        } else {
            echo PHP_EOL;
        }
    };
    $loop->futureTick($tick);
}, function(Exception $error) { // Error callback
    echo $error->getMessage(), PHP_EOL;
    var_export($error);
});

$loop->run();
