<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use React\EventLoop\Factory;
use WyriHaximus\React\GuzzlePsr7\HttpClientAdapter;

$loop = Factory::create();

(new Client([
    'handler' => HandlerStack::create(new HttpClientAdapter($loop)),
]))->getAsync('http://www.amazon.com/', [
    'timeout' => 300,
])->then(function() {
    echo 'Amazon completed' . PHP_EOL;
}, function(exception $error) {
    echo 'Amazon error' . PHP_EOL;
    echo $error->getMessage() . PHP_EOL;
});


$loop->run();
