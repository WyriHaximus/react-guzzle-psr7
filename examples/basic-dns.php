<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use React\EventLoop\Factory;
use React\Dns\Resolver\Factory as ResolverFactory;
use WyriHaximus\React\GuzzlePsr7\HttpClientAdapter;

$loop = Factory::create();
$resolver = (new ResolverFactory())->createCached('8.8.8.8', $loop); // Specify your own DNS server

(new Client([
    'handler' => HandlerStack::create(new HttpClientAdapter($loop, null, $resolver)),
]))->getAsync('http://blog.wyrihaximus.net/robots.txt')->then(function(Response $response) { // Success callback
    echo $response->getBody()->getContents(), PHP_EOL;
}, function(Exception $error) { // Error callback
    echo $error->getMessage(), PHP_EOL;
});

$loop->run();
