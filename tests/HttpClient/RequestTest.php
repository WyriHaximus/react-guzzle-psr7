<?php

/**
 * This file is part of ReactGuzzle.
 *
 ** (c) 2014 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WyriHaximus\React\Tests\Guzzle\HttpClient;

use Phake;

/**
 * Class RequestTest
 * @package WyriHaximus\React\Tests\Guzzle\HttpClient
 */
class RequestTest extends \PHPUnit_Framework_TestCase {

	public function testSend() {
		$requestArray = [];

		$loop = Phake::mock('React\EventLoop\LoopInterface');

		$client = Phake::mock('React\HttpClient\Client');
		$request = Phake::partialMock('WyriHaximus\React\RingPHP\HttpClient\Request', $requestArray, $client, $loop);

		$this->assertInstanceOf('React\Promise\PromiseInterface', $request->send());

		Phake::verify($loop)->addTimer(0, $this->isType('callable'));
	}

	public function testSetConnectionTimeout() {
		$requestArray = [
            'client' => [
                'connect_timeout' => 123,
            ],
        ];

		$loop = Phake::mock('React\EventLoop\LoopInterface');
		Phake::when($loop)->addTimer($this->isType('int'), $this->isType('callable'))->thenReturn(true);

		$client = Phake::mock('React\HttpClient\Client');
		$request = Phake::partialMock('WyriHaximus\React\RingPHP\HttpClient\Request', $requestArray, $client, $loop);

		$httpClientRequest = Phake::mock('React\HttpClient\Request');
		$request->setConnectionTimeout($httpClientRequest);

		Phake::verify($loop)->addTimer(123, $this->isType('callable'));
	}

	public function testSetRequestTimeout() {
        $requestArray = [
            'client' => [
                'timeout' => 321,
            ],
        ];

		$loop = Phake::mock('React\EventLoop\LoopInterface');
		Phake::when($loop)->addTimer($this->isType('int'), $this->isType('callable'))->thenReturn(true);

		$client = Phake::mock('React\HttpClient\Client');
		$request = Phake::partialMock('WyriHaximus\React\RingPHP\HttpClient\Request', $requestArray, $client, $loop);

		$httpClientRequest = Phake::mock('React\HttpClient\Request');
		$request->setRequestTimeout($httpClientRequest);

		Phake::verify($loop)->addTimer(321, $this->isType('callable'));
	}
    
}
