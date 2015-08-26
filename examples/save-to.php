<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use React\EventLoop\Factory;
use WyriHaximus\React\GuzzlePsr7\HttpClientAdapter;

$loop = Factory::create();

(new Client([
    'handler' => HandlerStack::create(new HttpClientAdapter($loop)),
]))->getAsync('http://www.google.com/robots.txt', [
    'save_to' => 'google-robots.txt',
])->then(function() {
    echo 'Done!' . PHP_EOL;
}, function(Exception $error) {
    echo 'Error: ' . var_export($error, true) . PHP_EOL;
});

$loop->run();
