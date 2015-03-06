ReactGuzzleRing
===============

[![Build Status](https://travis-ci.org/WyriHaximus/ReactGuzzleRing.png)](https://travis-ci.org/WyriHaximus/ReactGuzzleRing)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/react-guzzle-ring/v/stable.png)](https://packagist.org/packages/WyriHaximus/react-guzzle-ring)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/react-guzzle-ring/downloads.png)](https://packagist.org/packages/WyriHaximus/react-guzzle-ring)
[![Coverage Status](https://coveralls.io/repos/WyriHaximus/ReactGuzzleRing/badge.png)](https://coveralls.io/r/WyriHaximus/ReactGuzzleRing)
[![License](https://poser.pugx.org/wyrihaximus/react-guzzle-ring/license.png)](https://packagist.org/packages/wyrihaximus/react-guzzle-ring)

ReactPHP HttpClient Adapter for Guzzle5, for Guzzle4 check [ReactGuzzle](https://github.com/WyriHaximus/ReactGuzzle)

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
$handler = new \WyriHaximus\React\RingPHP\HttpClientAdapter($loop);

$client = new \GuzzleHttp\Client([
    'handler' => $handler,
]);

$client->get('http://github.com/', [ // This will redirect to https://github.com/
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

See the [examples](https://github.com/WyriHaximus/ReactGuzzleRing/tree/master/examples) directory for more ways to use this handler.

## Contributing ##

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License ##

Copyright 2014 [Cees-Jan Kiewiet](http://wyrihaximus.net/)

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
