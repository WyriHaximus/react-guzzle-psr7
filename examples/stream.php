<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use React\EventLoop\Factory;
use WyriHaximus\React\RingPHP\HttpClientAdapter;

(new Client([
    'handler' => new HttpClientAdapter(Factory::create()),
]))->get('http://blog.wyrihaximus.net/', [
    'future' => true,
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
