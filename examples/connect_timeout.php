<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use React\EventLoop\Factory;
use WyriHaximus\React\GuzzlePsr7\HttpClientAdapter;

$loop = Factory::create();

$guzzle = new Client([
    'connect_timeout' => 0.001,
    'handler' => HandlerStack::create(new HttpClientAdapter($loop)),
]);

$guzzle->getAsync('http://www.amazon.com/', [
    'connect_timeout' => 0.001,
])->then(function() {
    echo 'Amazon completed, we really shouldn\'t get getting here...' . PHP_EOL;
}, function(Exception $error) {
    echo 'Amazon error' . $error->getMessage() . PHP_EOL;
});


$loop->run();
