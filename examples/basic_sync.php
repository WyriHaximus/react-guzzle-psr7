<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use React\EventLoop\Factory;
use WyriHaximus\React\GuzzlePsr7\HttpClientAdapter;

$loop = Factory::create();

echo (new Client([
    'handler' => HandlerStack::create(new HttpClientAdapter($loop)),
]))->get('http://blog.wyrihaximus.net/')->getBody()->getContents(), PHP_EOL;

