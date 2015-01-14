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

use GuzzleHttp\Message\MessageFactory;
use React\EventLoop\LoopInterface;
use React\HttpClient\Client as ReactHttpClient;
use React\HttpClient\Request as HttpRequest;
use React\HttpClient\Response as HttpResponse;
use React\Promise\Deferred;
use React\Stream\Stream as ReactStream;
use Exception;

/**
 * Class Request
 *
 * @package WyriHaximus\React\Guzzle\HttpClient
 */
class Request
{
    /**
     * @var ReactHttpClient
     */
    protected $httpClient;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var HttpResponse
     */
    protected $httpResponse;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var string
     */
    protected $buffer = '';

    /**
     * @var Stream
     */
    protected $stream;

    /**
     * @var \Exception
     */
    protected $error = '';

    /**
     * @var \React\EventLoop\Timer\TimerInterface
     */
    protected $connectionTimer;

    /**
     * @var \React\EventLoop\Timer\TimerInterface
     */
    protected $requestTimer;

    /**
     * @var ProgressInterface
     */
    protected $progress;

    /**
     * @var Deferred
     */
    protected $deferred;

    /**
     * @var array
     */
    protected $request;

    /**
     * @var array
     */
    protected $requestDefaults = [
        'client' => [
            'stream' => false,
            'connect_timeout' => 0,
            'timeout' => 0,
            'delay' => 0,
        ],
    ];

    /**
     * @var bool
     */
    protected $connectionTimedOut = false;

    /**
     * @param array $request
     * @param ReactHttpClient $httpClient
     * @param LoopInterface $loop
     * @param ProgressInterface $progress
     */
    public function __construct(
        array $request,
        ReactHttpClient $httpClient,
        LoopInterface $loop,
        ProgressInterface $progress = null
    ) {
        $this->request = array_replace_recursive($this->requestDefaults, $request);
        $this->httpClient = $httpClient;
        $this->loop = $loop;
        $this->messageFactory = new MessageFactory();

        if ($progress instanceof ProgressInterface) {
            $this->progress = $progress;
        } elseif (isset($this->request['client']['progress']) && is_callable($this->request['client']['progress'])) {
            $this->progress = new Progress($this->request['client']['progress']);
        } else {
            $this->progress = new Progress(function () {
            });
        }
    }

    /**
     * @return \React\Promise\Promise
     */
    public function send()
    {
        $this->deferred = new Deferred();

        $this->loop->addTimer(
            (int)$this->request['client']['delay'] / 1000,
            function () {
                $this->tickRequest();
            }
        );

        return $this->deferred->promise();
    }

    /**
     *
     */
    protected function tickRequest()
    {
        $this->loop->futureTick(function () {
            $request = $this->setupRequest();
            $this->setupListeners($request);

            $this->progress->onSending($this->request['body']);

            $this->setConnectionTimeout($request);
            $request->end((string)$this->request['body']);
            $this->setRequestTimeout($request);
        });
    }

    /**
     * @return HttpRequest mixed
     */
    protected function setupRequest()
    {
        $headers = [];
        foreach ($this->request['headers'] as $key => $values) {
            $headers[$key] = implode(';', $values);
        }
        return $this->httpClient->request($this->request['http_method'], $this->request['url'], $headers);
    }

    /**
     * @param HttpRequest $request
     */
    protected function setupListeners(HttpRequest $request)
    {
        $request->on(
            'headers-written',
            function () {
                $this->onHeadersWritten();
            }
        );
        $request->on(
            'drain',
            function () {
                $this->progress->onSent();
            }
        );
        $request->on(
            'response',
            function (HttpResponse $response) use ($request) {
                $this->onResponse($response, $request);
            }
        );
        $request->on(
            'error',
            function ($error) {
                $this->onError($error);
            }
        );
        $request->on(
            'end',
            function () {
                $this->onEnd();
            }
        );
    }

    /**
     * @param HttpRequest $request
     */
    public function setConnectionTimeout(HttpRequest $request)
    {
        if ($this->request['client']['connect_timeout'] > 0) {
            $this->connectionTimer = $this->loop->addTimer(
                $this->request['client']['connect_timeout'],
                function () use ($request) {
                    $request->closeError(new \Exception('Connection time out'));
                }
            );
        }
    }

    /**
     * @param HttpRequest $request
     */
    public function setRequestTimeout(HttpRequest $request)
    {
        if ($this->request['client']['timeout'] > 0) {
            $this->requestTimer = $this->loop->addTimer(
                $this->request['client']['timeout'],
                function () use ($request) {
                    $request->close(new \Exception('Transaction time out'));
                }
            );
        }
    }

    protected function onHeadersWritten()
    {
        if ($this->connectionTimer !== null) {
            $this->loop->cancelTimer($this->connectionTimer);
        }
    }

    /**
     * @param HttpResponse $response
     * @param HttpRequest  $request
     */
    protected function onResponse(HttpResponse $response, HttpRequest $request)
    {
        $this->httpResponse = $response;
        if (!empty($this->request['client']['save_to'])) {
            $this->saveTo();
            return;
        }

        $this->handleResponse($request);
    }

    protected function saveTo()
    {
        $saveTo = $this->request['client']['save_to'];

        $writeStream = fopen($saveTo, 'w');
        stream_set_blocking($writeStream, 0);
        $saveToStream = new ReactStream($writeStream, $this->loop);

        $saveToStream->on(
            'end',
            function () {
                $this->onEnd();
            }
        );

        $this->httpResponse->pipe($saveToStream);
    }

    /**
     * @param string $data
     */
    protected function onData($data)
    {
        $this->progress->onData($data);
    }

    /**
     * @param \Exception $error
     */
    protected function onError(\Exception $error)
    {
        $this->error = $error;
    }

    /**
     *
     */
    protected function onEnd()
    {
        if ($this->requestTimer !== null) {
            $this->loop->cancelTimer($this->requestTimer);
        }

        $this->loop->futureTick(function () {
            if ($this->httpResponse === null) {
                $this->deferred->reject($this->error);
            }
        });
    }

    /**
     *
     */
    protected function handleResponse($request)
    {
        $this->progress->onResponse($this->httpResponse);

        $headers = $this->httpResponse->getHeaders();
        if (Utils::hasHeader($headers, 'location')) {
            $this->followRedirect(Utils::header($headers, 'location'));
            return;
        }

        $this->createStream($request);

        $response = [
            'effective_url' => $this->request['url'],
            'body' => $this->stream,
            'headers' => $this->httpResponse->getHeaders(),
            'status' => $this->httpResponse->getCode(),
            'reason' => $this->httpResponse->getReasonPhrase(),
        ];

        $this->loop->futureTick(function () use ($response) {
            $this->deferred->resolve($response);
        });
    }

    protected function createStream($request)
    {
        $this->stream = new Stream([
            'response' => $this->httpResponse,
            'request' => $request,
            'loop' => $this->loop,
        ]);
    }
    /**
     * @param string $location
     */
    protected function followRedirect($location)
    {
        $request = $this->request;
        $request['client']['redirect']['max']--;
        if ($request['client']['redirect']['max'] <= 0) {
            $this->deferred->reject(new Exception('Exceeded maximum redirects'));
            return;
        }
        if ($request['client']['redirect']['referer']) {
            $request['headers']['Referer'] = [
                $request['url'],
            ];
        }
        if (!$request['client']['redirect']['strict']) {
            $request['http_method'] = 'GET';
        }
        $request['url'] = $location;
        $request['headers'] = Utils::placeHeader($request['headers'], 'Host', [
            parse_url($request['url'], PHP_URL_HOST),
        ]);

        (new Request($request, $this->httpClient, $this->loop))->send()->then(function ($response) {
            $this->deferred->resolve($response);
        }, function ($error) {
            $this->deferred->reject($error);
        });
    }
}
