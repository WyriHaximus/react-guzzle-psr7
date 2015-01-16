<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use React\EventLoop\Factory;
use WyriHaximus\React\RingPHP\HttpClientAdapter;

(new Client([
    'handler' => new HttpClientAdapter(Factory::create()),
]))->get('http://www.wyrihaximus.net/', [ // This will redirect to http://wyrihaximus.net/
    'future' => true,
])->then(function(Response $response) { // Success callback
    echo $response->getEffectiveUrl(), PHP_EOL;
});

$loop->run();
