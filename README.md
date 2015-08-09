react-guzzle-psr7
===============

[![Build Status](https://travis-ci.org/WyriHaximus/react-guzzle-psr7.png)](https://travis-ci.org/WyriHaximus/react-guzzle-psr7)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/react-guzzle-psr7/v/stable.png)](https://packagist.org/packages/WyriHaximus/react-guzzle-psr7)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/react-guzzle-psr7/downloads.png)](https://packagist.org/packages/WyriHaximus/react-guzzle-psr7)
[![Coverage Status](https://coveralls.io/repos/WyriHaximus/react-guzzle-psr7/badge.png)](https://coveralls.io/r/WyriHaximus/react-guzzle-psr7)
[![License](https://poser.pugx.org/wyrihaximus/react-guzzle-psr7/license.png)](https://packagist.org/packages/wyrihaximus/react-guzzle-psr7)

ReactPHP HttpClient Adapter for Guzzle6, for Guzzle5 check [ReactGuzzleRing](https://github.com/WyriHaximus/ReactGuzzleRing)

### Installation ###

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `~`.

```
composer require wyrihaximus/react-guzzle-psr7 
```

### Examples ###

#### Asynchronous ####

```php
<?php

require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$loop = \React\EventLoop\Factory::create();
$handler = new \WyriHaximus\React\GuzzlePsr7\HttpClientAdapter($loop);

$client = new \GuzzleHttp\Client([
    'handler' => \GuzzleHttp\HandlerStack::create($handler),
]);

$client->getAsync('http://github.com/')->then(function ($response) {
    var_export($response);
    var_export((string) $response->getBody());
});

$client->getAsync('http://php.net/')->then(function ($response) {
    var_export($response);
    var_export((string) $response->getBody());
});

$loop->run();
```

#### Synchronous ####

```php
<?php

require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$loop = \React\EventLoop\Factory::create();
$handler = new \WyriHaximus\React\GuzzlePsr7\HttpClientAdapter($loop);

$client = new \GuzzleHttp\Client([
    'handler' => \GuzzleHttp\HandlerStack::create($handler),
]);

var_export((string) $client->get('http://github.com/')->getBody());
```

See the [examples](https://github.com/WyriHaximus/react-guzzle-psr7/tree/master/examples) directory for more ways to use this handler.

## Contributing ##

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License ##

Copyright 2015 [Cees-Jan Kiewiet](http://wyrihaximus.net/)

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
