<?php

namespace WyriHaximus\React\GuzzlePsr7;

use Clue\React\Buzz\Message\ResponseException;
use GuzzleHttp\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Dns\Resolver\Factory as DnsFactory;
use React\Dns\Resolver\Resolver as DnsResolver;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;
use React\HttpClient\Client as HttpClient;
use React\HttpClient\Client;
use React\HttpClient\Factory as HttpClientFactory;
use React\Socket\Connector;
use WyriHaximus\React\Guzzle\HttpClient\RequestFactory;

class HttpClientAdapter
{
    /**
     * @var numeric
     */
    const QUEUE_TIMER_INTERVAL = 0.01;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var DnsResolver
     */
    protected $dnsResolver;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @param LoopInterface $loop
     * @param HttpClient $httpClient
     * @param DnsResolver $dnsResolver
     * @param RequestFactory $requestFactory
     */
    public function __construct(
        LoopInterface $loop,
        HttpClient $httpClient = null,
        DnsResolver $dnsResolver = null,
        RequestFactory $requestFactory = null
    ) {
        $this->loop = $loop;

        $this->setDnsResolver($dnsResolver);
        $this->setHttpClient($httpClient);
        $this->setRequestFactory($requestFactory);
    }

    /**
     * @param HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient = null)
    {
        if (!($httpClient instanceof HttpClient)) {
            $this->setDnsResolver($this->dnsResolver);

            if (class_exists('React\HttpClient\Factory')) {
                $factory = new HttpClientFactory();
                $httpClient = $factory->create($this->loop, $this->dnsResolver);
            } else {
                $httpClient = new Client(
                    $this->loop,
                    new Connector(
                        $this->loop,
                        [
                            'dns' => $this->dnsResolver,
                        ]
                    )
                );
            }
        }

        $this->httpClient = $httpClient;
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param DnsResolver $dnsResolver
     */
    public function setDnsResolver(DnsResolver $dnsResolver = null)
    {
        if (!($dnsResolver instanceof DnsResolver)) {
            $dnsResolverFactory = new DnsFactory();
            $dnsResolver = $dnsResolverFactory->createCached('8.8.8.8', $this->loop);
        }

        $this->dnsResolver = $dnsResolver;
    }

    /**
     * @return DnsResolver
     */
    public function getDnsResolver()
    {
        return $this->dnsResolver;
    }

    /**
     * @param RequestFactory $requestFactory
     */
    public function setRequestFactory(RequestFactory $requestFactory = null)
    {
        if (!($requestFactory instanceof RequestFactory)) {
            $requestFactory = new RequestFactory();
        }

        $this->requestFactory = $requestFactory;
    }

    /**
     * @return RequestFactory
     */
    public function getRequestFactory()
    {
        return $this->requestFactory;
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return Promise
     */
    public function __invoke(RequestInterface $request, array $options)
    {
        $ready = false;
        $promise = new Promise(function () use (&$ready) {
            do {
                $this->loop->stop();
                $this->loop->futureTick(function () {
                    $this->loop->stop();
                });
                $this->loop->run();
            } while (!$ready);
            $this->loop->futureTick(function () {
                $this->loop->stop();
                $this->loop->run();
            });
        });

        $this->requestFactory->create($request, $options, $this->dnsResolver, $this->httpClient, $this->loop)->
            then(
                function (ResponseInterface $response) use (&$ready, $promise) {
                    $ready = true;
                    $promise->resolve($response);

                    $this->invokeQueue();
                },
                function ($error) use (&$ready, $promise, $options) {
                    $ready = true;
                    if (isset($options['http_errors']) &&
                        $options['http_errors'] === false &&
                        $error instanceof ResponseException
                    ) {
                        $promise->resolve($error->getResponse());
                    } else {
                        $promise->reject($error);
                    }

                    $this->invokeQueue();
                }
            )
        ;

        /** @var TimerInterface $timer */
        $timer = $this->loop->addPeriodicTimer(static::QUEUE_TIMER_INTERVAL, function () use (&$ready, &$timer) {
            $this->invokeQueue();
            if ($ready) {
                $this->loop->cancelTimer($timer);
            }
        });

        return $promise;
    }

    protected function invokeQueue()
    {
        $this->loop->addTimer(static::QUEUE_TIMER_INTERVAL, function () {
            \GuzzleHttp\Promise\queue()->run();
        });
    }
}
