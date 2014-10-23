<?php

/**
 * This file is part of ReactGuzzleRing.
 *
 ** (c) 2014 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WyriHaximus\React\RingPHP\HttpClient;

use React\EventLoop\LoopInterface;
use React\HttpClient\Client as HttpClient;

/**
 * Class RequestFactory
 *
 * @package WyriHaximus\React\Guzzle\HttpClient
 */
class RequestFactory
{
    public function create(array $request, HttpClient $httpClient, LoopInterface $loop)
    {
        return new Request($request, $httpClient, $loop);
    }
}
