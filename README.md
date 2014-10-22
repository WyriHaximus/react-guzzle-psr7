ReactGuzzleRing
===============

### Installation ###

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `~`.

```
composer require wyrihaximus/react-guzzle-ring 
```

### Example ###

```php
<?php

require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$loop = \React\EventLoop\Factory::create();
$handler = new \WyriHaximus\React\RingPHP\Client\HttpClientAdapter($loop);

$client = new \GuzzleHttp\Client([
    'handler' => $handler,
]);

$client->get('https://github.com/', [
    'future' => true,
])->then(function ($response) {
    var_export($response);
    var_export((string) $response->getBody());
});

$client->get('http://php.net/', [
    'future' => true,
])->then(function ($response) {
    var_export($response);
    var_export((string) $response->getBody());
});

$loop->run();
```
