<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use React\EventLoop\Factory;
use function React\Promise\resolve;
use WyriHaximus\React\GuzzlePsr7\HttpClientAdapter;
use function Clue\React\Block\await;

$loop = Factory::create();

echo await(resolve((new Client([
    'handler' => HandlerStack::create(new HttpClientAdapter($loop)),
]))->getAsync('http://blog.wyrihaximus.net/')), $loop)->getBody()->getContents(), PHP_EOL;

