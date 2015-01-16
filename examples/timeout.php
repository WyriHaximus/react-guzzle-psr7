<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use React\EventLoop\Factory;
use WyriHaximus\React\RingPHP\HttpClientAdapter;

$guzzle = new Client([
    'handler' => new HttpClientAdapter(Factory::create()),
]);

$guzzle->get('http://www.amazon.com/', [
    'timeout' => 0.001,
    'future' => true,
])->then(function(Response $response) {
    echo 'Amazon completed' . PHP_EOL;
}, function(exception $error) {
    echo 'Amazon error' . PHP_EOL;
    echo $error->getMessage() . PHP_EOL;
});


$loop->run();
