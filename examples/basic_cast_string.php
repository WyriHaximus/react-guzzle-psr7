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
]))->getAsync('http://blog.wyrihaximus.net/robots.txt')->then(function(Response $response) { // Success callback
    echo (string)$response->getBody(), PHP_EOL;
}, function($error) { // Error callback
    echo $error->getMessage(), PHP_EOL;
});

$loop->run();
