<?php

/**
 * This file is part of ReactGuzzle.
 *
 ** (c) 2014 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WyriHaximus\React\Tests\GuzzlePsr7;

use Clue\React\Buzz\Message\ResponseException;
use GuzzleHttp\Psr7\Request;
use Phake;
use React\Dns\Config\Config as DnsConfig;
use React\Dns\Resolver\Factory as ResolverFactory;
use React\EventLoop\Factory;
use React\Promise\Deferred;
use React\Promise\FulfilledPromise;
use React\Promise\RejectedPromise;
use RingCentral\Psr7\Response;
use WyriHaximus\React\GuzzlePsr7\HttpClientAdapter;

/**
 * Class HttpClientAdapterTest
 *
 * @package WyriHaximus\React\Tests\Guzzle
 */
class HttpClientAdapterTest extends \PHPUnit_Framework_TestCase
{

    protected $request;
    protected $requestArray;
    protected $loop;
    protected $requestFactory;
    protected $dnsResolver;
    protected $httpClient;
    protected $adapter;
    private $errors;

    public function setUp()
    {
        parent::setUp();

        $this->errors = [];
        set_error_handler([$this, 'errorHandler']);

        $this->request = new Request('GET', 'http://example.com', [], '');
        $this->requestArray = [];
        $this->loop = Factory::create();
        $this->requestFactory = Phake::mock('WyriHaximus\React\Guzzle\HttpClient\RequestFactory');
        $config = DnsConfig::loadSystemConfigBlocking();
        $nameserver = $config->nameservers ? reset($config->nameservers) : '8.8.8.8';
        $this->dnsResolver = (new ResolverFactory())->createCached($nameserver, $this->loop);
        if (class_exists('React\HttpClient\Factory')) {
            $this->httpClient = Phake::partialMock(
                'React\HttpClient\Client',
                Phake::mock('React\SocketClient\ConnectorInterface'),
                Phake::mock('React\SocketClient\ConnectorInterface')
            );
        } else {
            $this->httpClient = Phake::partialMock(
                'React\HttpClient\Client',
                $this->loop,
                Phake::mock('React\Socket\ConnectorInterface')
            );
        }

        $this->adapter = new HttpClientAdapter($this->loop, $this->httpClient, null, $this->requestFactory);
    }

    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $this->errors[] = [$errno, $errstr, $errfile, $errline, $errcontext];
    }

    public function tearDown()
    {
        parent::tearDown();

        restore_error_handler();

        unset($this->adapter, $this->request, $this->httpClient, $this->requestFactory, $this->dnsResolver, $this->loop);
    }

    public function testSend()
    {
        $responseMock = Phake::mock('Psr\Http\Message\ResponseInterface');
        Phake::when($this->requestFactory)->create(
            $this->request,
            [],
            $this->dnsResolver,
            $this->httpClient,
            $this->loop
        )->thenReturn(
            new FulfilledPromise($responseMock)
        );

        $callbackFired = false;
        $adapter = $this->adapter;
        $adapter($this->request, [])->then(function ($response) use (&$callbackFired, $responseMock) {
            $this->assertSame($responseMock, $response);
            $callbackFired = true;
        });

        $this->loop->run();

        Phake::inOrder(
            Phake::verify($this->requestFactory, Phake::times(1))->create(
                $this->request,
                $this->requestArray,
                $this->dnsResolver,
                $this->httpClient,
                $this->loop
            )
        );

        $this->assertTrue($callbackFired);
    }

    public function testSendSync()
    {
        $deferred = new Deferred();
        $responseMock = Phake::mock('Psr\Http\Message\ResponseInterface');
        Phake::when($this->requestFactory)->create(
            $this->request,
            [],
            $this->dnsResolver,
            $this->httpClient,
            $this->loop
        )->thenReturn(
            $deferred->promise()
        );

        $this->loop->futureTick(function () use ($deferred, $responseMock) {
            $deferred->resolve($responseMock);
        });

        $callbackFired = false;
        $adapter = $this->adapter;
        $promise = $adapter($this->request, []);
        $promise->then(function ($response) use (&$callbackFired, $responseMock) {
            $this->assertSame($responseMock, $response);
            $callbackFired = true;
        });

        $promise->wait();

        Phake::inOrder(
            Phake::verify($this->requestFactory, Phake::times(1))->create(
                $this->request,
                $this->requestArray,
                $this->dnsResolver,
                $this->httpClient,
                $this->loop
            )
        );

        $this->assertTrue($callbackFired);

        self::assertCount(1, $this->errors);
        self::assertStringStartsWith('Using Promise::wait with the ReactPHP handler is deprecated', $this->errors[0][1]);
    }

    public function testSendFailed()
    {
        $exception = new \Exception(123);
        Phake::when($this->requestFactory)->create(
            $this->request,
            [],
            $this->dnsResolver,
            $this->httpClient,
            $this->loop
        )->thenReturn(
            new RejectedPromise($exception)
        );

        $callbackFired = false;
        $adapter = $this->adapter;
        $adapter($this->request, [])->then(null, function ($error) use (&$callbackFired, &$exception) {
            $this->assertSame($exception, $error);
            $callbackFired = true;
        });

        $this->loop->run();

        Phake::inOrder(
            Phake::verify($this->requestFactory, Phake::times(1))->create(
                $this->request,
                [],
                $this->dnsResolver,
                $this->httpClient,
                $this->loop
            )
        );

        $this->assertTrue($callbackFired);
    }

    public function testSendFailedFollowsHttpErrors()
    {
        $response = new Response(404);
        $exception = new ResponseException($response);
        Phake::when($this->requestFactory)->create(
            $this->request,
            [
                'http_errors' => false,
            ],
            $this->dnsResolver,
            $this->httpClient,
            $this->loop
        )->thenReturn(
            new RejectedPromise($exception)
        );

        $callbackFired = false;
        $adapter = $this->adapter;
        $adapter($this->request, [
            'http_errors' => false,
        ])->then(function ($rsp) use (&$callbackFired, &$response) {
            $this->assertSame($response, $rsp);
            $callbackFired = true;
        });

        $this->loop->run();

        Phake::inOrder(
            Phake::verify($this->requestFactory, Phake::times(1))->create(
                $this->request,
                [
                    'http_errors' => false,
                ],
                $this->dnsResolver,
                $this->httpClient,
                $this->loop
            )
        );

        $this->assertTrue($callbackFired);
    }

    public function testSetDnsResolver()
    {
        $this->adapter->setDnsResolver();
        $this->assertInstanceOf('React\Dns\Resolver\Resolver', $this->adapter->getDnsResolver());

        $mock = Phake::partialMock(
            'React\Dns\Resolver\Resolver',
            Phake::mock('React\Dns\Query\ExecutorInterface'),
            Phake::mock('React\Dns\Query\ExecutorInterface')
        );
        $this->adapter->setDnsResolver($mock);
        $this->assertSame($mock, $this->adapter->getDnsResolver());
    }

    public function testSetHttpClient()
    {
        $this->adapter->setHttpClient();
        $this->assertInstanceOf('React\HttpClient\Client', $this->adapter->getHttpClient());
        if (class_exists('React\HttpClient\Factory')) {
            $mock = Phake::partialMock(
                'React\HttpClient\Client',
                Phake::mock('React\SocketClient\ConnectorInterface'),
                Phake::mock('React\SocketClient\ConnectorInterface')
            );
        } else {
            $mock = Phake::partialMock(
                'React\HttpClient\Client',
                Factory::create(),
                Phake::mock('React\Socket\ConnectorInterface')
            );
        }

        $this->adapter->setHttpClient($mock);
        $this->assertSame($mock, $this->adapter->getHttpClient());
    }

    public function testSetRequestFactory()
    {
        $this->adapter->setRequestFactory();
        $this->assertInstanceOf(
            'WyriHaximus\React\Guzzle\HttpClient\RequestFactory',
            $this->adapter->getRequestFactory()
        );

        $mock = Phake::mock('WyriHaximus\React\Guzzle\HttpClient\RequestFactory');
        $this->adapter->setRequestFactory($mock);
        $this->assertSame($mock, $this->adapter->getRequestFactory());
    }
}
