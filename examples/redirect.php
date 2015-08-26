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
]))->getAsync('http://www.wyrihaximus.net/')->then(function(Response $response) { // Success callback
    var_export($response);
}, function(Exception $e) { // Error callback
    echo $e->getMessage(), PHP_EOL;
});

$loop->run();
