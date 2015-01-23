<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use React\EventLoop\Factory;
use WyriHaximus\React\RingPHP\HttpClientAdapter;

$loop = Factory::create();

(new Client([
    'handler' => new HttpClientAdapter($loop),
]))->get('http://www.google.com/robots.txt', [
    'save_to' => 'google-robots.txt',
    'future' => true,
])->then(function() {
    echo 'Done!' . PHP_EOL;
}, function(Exception $error) {
    echo 'Error: ' . var_export($error, true) . PHP_EOL;
});

$loop->run();
