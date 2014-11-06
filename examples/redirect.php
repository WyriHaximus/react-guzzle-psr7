<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use React\EventLoop\Factory;
use WyriHaximus\React\RingPHP\HttpClientAdapter;
use WyriHaximus\React\RingPHP\HttpClient\ProgressInterface;

// Create eventloop
$loop = Factory::create();

(new Client([
    'handler' => new HttpClientAdapter($loop),
]))->get('http://github.com/', [ // This will redirect to https://github.com/
    'future' => true,
])->then(function(Response $response) { // Success callback
    var_export($response);
}, function($event) { // Error callback
    var_export($event);
});

$loop->run();
