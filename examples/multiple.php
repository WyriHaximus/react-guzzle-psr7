<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use React\EventLoop\Factory;
use WyriHaximus\React\RingPHP\HttpClientAdapter;

// Create eventloop
$loop = Factory::create();

$guzzle = new Client([
    'handler' => new HttpClientAdapter($loop),
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
] as $site) {
    $name = $site['name'];

    $guzzle->get($site['url'], [
        'future' => true,
    ])->then(function($response) use ($name) {
        echo $name . ' completed' . PHP_EOL;
    }, function($error) use ($name) {
        echo $name . ' error' . PHP_EOL;
    });
}

$loop->run();
