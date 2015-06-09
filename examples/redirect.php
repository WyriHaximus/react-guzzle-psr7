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
]))->get('http://www.wyrihaximus.net/')->then(function(Response $response) { // Success callback
    //var_export($response);die();
    echo $response->getEffectiveUrl(), PHP_EOL;
}, function(Exception $e) { // Success callback
    echo $e->getMessage(), PHP_EOL;
    //echo $e->getPrevious()->getMessage(), PHP_EOL;
});

$loop->run();
