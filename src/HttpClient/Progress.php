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
 * Class Progress
 * @package WyriHaximus\React\Guzzle\HttpClient
 */
class Progress implements ProgressInterface, \ArrayAccess
{
    private $currentSize = 0;
    private $fullSize;
    private $response;
    private $data;
    private $event;

    /**
     * @return bool
     */
    public function isFullSizeKnown()
    {
        return !($this->fullSize === null);
    }

    /**
     * @return float|int
     */
    public function getCompletePercentage()
    {
        if (!$this->isFullSizeKnown() || $this->currentSize == 0) {
            return 0;
        }

        $bit = $this->fullSize / 100;
        return $this->currentSize / $bit;
    }

    /**
     * @param $eventName
     * @return $this
     */
    public function setEvent($eventName)
    {
        $this->event = $eventName;

        return $this;
    }

    /**
     * @param HttpResponse $response
     * @return $this
     */
    public function onResponse(HttpResponse $response)
    {
        $this->response = $response;
        $headers = $this->response->getHeaders();
        if (isset($headers['Content-Length'])) {
            $this->fullSize = $headers['Content-Length'];
        }

        return $this;
    }

    /**
     * @param string $data
     * @return $this
     */
    public function onData($data)
    {
        $this->data = $data;
        $this->currentSize += strlen($this->data);

        return $this;
    }

    /**
     * \ArrayAccess
     */

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        if (in_array($offset, [
            'event',
            'response',
            'data',
            'currentSize',
            'fullSize',
        ])) {
            return true;
        }

        return false;
    }

    /**
     * @param mixed $offset
     * @return null|mixed
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->{$offset};
        }

        return null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {}

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset) {}
}
