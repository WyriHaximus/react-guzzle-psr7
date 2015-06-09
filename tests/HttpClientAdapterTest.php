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

    protected $requestArray;
    protected $loop;
    protected $requestFactory;
    protected $httpClient;
    protected $adapter;

    public function setUp()
    {
        parent::setUp();

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
        $deferred = new Deferred();
        $this->loop->futureTick(function () use ($deferred) {
            $deferred->resolve();
        });

        Phake::when($this->requestFactory)->create($this->requestArray, $this->httpClient, $this->loop)->thenReturn(
            $deferred->promise()
        );

        $adapter = $this->adapter;
        $futureArray = $adapter($this->requestArray);
        $this->assertInstanceOf('GuzzleHttp\Ring\Future\FutureArray', $futureArray);
        $callbackFired = false;
        $futureArray->then(function () use (&$callbackFired) {
            $callbackFired = true;
        });
        $futureArray->wait();

        Phake::inOrder(
            Phake::verify($this->requestFactory, Phake::times(1))->create(
                $this->requestArray,
                $this->httpClient,
                $this->loop
            )
        );

        $this->assertTrue($callbackFired);
    }

    public function testSendFailed()
    {
        Phake::when($this->requestFactory)->create($this->requestArray, $this->httpClient, $this->loop)->thenReturn(
            new RejectedPromise(123)
        );

        $adapter = $this->adapter;
        $futureArray = $adapter($this->requestArray);
        $this->assertInstanceOf('GuzzleHttp\Ring\Future\FutureArray', $futureArray);
        $callbackFired = false;
        $futureArray->then(function ($error) use (&$callbackFired) {
            $this->assertEquals([
                'error' => 123,
            ], $error);
            $callbackFired = true;
        });

        Phake::inOrder(
            Phake::verify($this->requestFactory, Phake::times(1))->create(
                $this->requestArray,
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
