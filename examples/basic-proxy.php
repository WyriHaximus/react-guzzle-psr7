<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Factory;
use React\Dns\Resolver\Factory as ResolverFactory;
use WyriHaximus\React\GuzzlePsr7\HttpClientAdapter;

$loop = Factory::create();
$resolver = (new ResolverFactory())->createCached('8.8.8.8', $loop); // Specify your own DNS server

(new Client([
    'handler' => HandlerStack::create(new HttpClientAdapter($loop, null, $resolver)),
]))->getAsync('https://api.ipify.org?format=json', [
    'proxy' => 'socks5://127.0.0.1:8080/', // SOCKS proxy, support socks, socks4, socks4a, and socks5 as schema
    //'proxy' => 'http://127.0.0.1:8080/', // HTTP proxy
])->then(function(ResponseInterface $response) { // Success callback
    echo $response->getBody()->getContents(), PHP_EOL;
}, function(Exception $error) { // Error callback
    echo (string)$error, PHP_EOL;
});

$loop->run();
