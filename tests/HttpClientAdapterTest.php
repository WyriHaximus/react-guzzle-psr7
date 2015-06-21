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

use GuzzleHttp\Psr7\Request;
use Phake;
use React\EventLoop\Factory;
use React\Promise\Deferred;
use React\Promise\FulfilledPromise;
use React\Promise\RejectedPromise;
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
    protected $httpClient;
    protected $adapter;

    public function setUp()
    {
        parent::setUp();

        $this->request = new Request('GET', 'http://example.com', [], '');
        $this->requestArray = [];
        $this->loop = Factory::create();
        $this->requestFactory = Phake::mock('WyriHaximus\React\Guzzle\HttpClient\RequestFactory');
        $this->httpClient = Phake::partialMock(
            'React\HttpClient\Client',
            Phake::mock('React\SocketClient\ConnectorInterface'),
            Phake::mock('React\SocketClient\ConnectorInterface')
        );

        $this->adapter = new HttpClientAdapter($this->loop, $this->httpClient, null, $this->requestFactory);
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->adapter, $this->request, $this->httpClient, $this->requestFactory, $this->loop);
    }

    public function testSend()
    {
        $responseMock = Phake::mock('Psr\Http\Message\ResponseInterface');
        Phake::when($this->requestFactory)->create(
            $this->request,
            [],
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
                $this->httpClient,
                $this->loop
            )
        );

        $this->assertTrue($callbackFired);
    }

    public function testSendFailed()
    {
        $exception = new \Exception(123);
        Phake::when($this->requestFactory)->create(
            $this->request,
            [],
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

        $mock = Phake::partialMock(
            'React\HttpClient\Client',
            Phake::mock('React\SocketClient\ConnectorInterface'),
            Phake::mock('React\SocketClient\ConnectorInterface')
        );
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
