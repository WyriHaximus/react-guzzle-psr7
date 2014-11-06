<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use React\EventLoop\Factory;
use WyriHaximus\React\RingPHP\HttpClientAdapter;

// Create eventloop
$loop = Factory::create();

(new Client([
    'handler' => new HttpClientAdapter($loop),
]))->get('http://docs.guzzlephp.org/en/latest/', [
    'future' => true,
])->then(function(Response $response) { // Success callback
    var_export($response);
}, function($event) { // Error callback
    var_export($event);
});

$loop->run();
