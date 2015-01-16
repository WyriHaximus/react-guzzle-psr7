<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use React\EventLoop\Factory;
use WyriHaximus\React\RingPHP\HttpClientAdapter;

(new Client([
    'handler' => new HttpClientAdapter(Factory::create()),
]))->get('http://blog.wyrihaximus.net/robots.txt', [
    'future' => true,
])->then(function(Response $response) { // Success callback
    echo (string)$response->getBody(), PHP_EOL;
}, function($error) { // Error callback
    echo $error->getMessage(), PHP_EOL;
});

$loop->run();
