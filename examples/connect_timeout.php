<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use React\EventLoop\Factory;
use WyriHaximus\React\RingPHP\HttpClientAdapter;

$guzzle = new Client([
    'connect_timeout' => 0.001,
    'handler' => new HttpClientAdapter(Factory::create()),
]);

$guzzle->get('http://www.amazon.com/', [
    'connect_timeout' => 0.001,
    'future' => true,
])->then(function() {
    echo 'Amazon completed, we really shouldn\'t get getting here...' . PHP_EOL;
}, function(Exception $error) {
    echo 'Amazon error' . $error->getMessage() . PHP_EOL;
});


$loop->run();
