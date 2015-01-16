<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use React\EventLoop\Factory;
use WyriHaximus\React\RingPHP\HttpClientAdapter;

$guzzle = new Client([
    'handler' => new HttpClientAdapter(Factory::create()),
]);

foreach ([
    [
        'name' => 'Nu',
        'url' => 'http://www.nu.nl/',
    ],
    [
        'name' => 'Yahoo!',
        'url' => 'http://www.yahoo.com/',
    ],
    [
        'name' => 'Duck Duck Go',
        'url' => 'http://www.duckduckgo.com/',
    ],
    [
        'name' => 'Blog',
        'url' => 'http://blog.wyrihaximus.net/',
    ],
] as $site) {
    $name = $site['name'];

    $guzzle->get($site['url'], [
        'future' => true,
    ])->then(function() use ($name) {
        echo $name . ' completed' . PHP_EOL;
    }, function(Exception $error) use ($name) {
        echo $name . ' error: ' . $error->getMessage() . PHP_EOL;
    });
}

$loop->run();
