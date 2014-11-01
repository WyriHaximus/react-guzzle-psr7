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
class Progress implements ProgressInterface
{
    private $receiveCurrentSize = 0;
    private $receiveFullSize = 0;
    private $sendCurrentSize = 0;
    private $sendFullSize = 0;
    private $response;
    private $data;
    private $event;
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function onSending($body)
    {
        $this->sendFullSize = strlen($body);

        $this->callCallback();

        return $this;
    }

    /**
     * @return $this
     */
    public function onSent()
    {
        $this->sendCurrentSize = $this->sendFullSize;

        $this->callCallback();

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
            $this->receiveFullSize = $headers['Content-Length'];
        }

        $this->callCallback();

        return $this;
    }

    /**
     * @param string $data
     * @return $this
     */
    public function onData($data)
    {
        $this->data = $data;
        $this->receiveCurrentSize += strlen($this->data);

        $this->callCallback();

        return $this;
    }

    protected function callCallback()
    {
        $callback = $this->callback;
        $callback($this->receiveFullSize, $this->receiveCurrentSize, $this->sendFullSize, $this->sendCurrentSize);
    }
}
