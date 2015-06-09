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
]))->get('http://blog.wyrihaximus.net/', [
    'stream' => true,
])->then(function(Response $response) { // Success callback
    $body = $response->getBody();
    while (!$body->eof()) { // Reading from the body untill there is nothing more to read
        echo $body->read(1024);
    }
    echo PHP_EOL;
}, function(Exception $error) { // Error callback
    echo $error->getMessage(), PHP_EOL;
});

$loop->run();
