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

use React\HttpClient\Response as HttpResponse;

/**
 * Interface ProgressInterface
 * @package WyriHaximus\React\Guzzle\HttpClient
 */
interface ProgressInterface
{
    /**
     * @param callable $callback
     */
    public function setCallback(callable $callback);

    /**
     * @param HttpResponse $response
     */
    public function onResponse(HttpResponse $response);

    /**
     * @param string $body
     */
    public function onSending($body);

    /**
     *
     */
    public function onSent();

    /**
     * @param string $data
     */
    public function onData($data);
}
