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
 * @implements \ArrayAccess
 */
interface ProgressInterface
{
    /**
     * @param string $eventName
     * @return Progress
     */
    public function setEvent($eventName);

    /**
     * @return Progress
     */
    public function onResponse(HttpResponse $response);

    /**
     * @return Progress
     */
    public function onData($data);
}
