<?php

/**
 * This file is part of ReactGuzzleRing.
 *
 ** (c) 2014 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WyriHaximus\React\Tests\RingPHP\HttpClient;

use WyriHaximus\React\RingPHP\HttpClient\Utils;

/**
 * Class UtilsTest
 *
 * @package WyriHaximus\React\Tests\Guzzle\HttpClient
 */
class UtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider \WyriHaximus\React\Tests\RingPHP\HttpClient\UtilsProvider::providerHasHeader
     */
    public function testHasHeader($boolean, $headers, $header)
    {
        $this->assertSame($boolean, Utils::hasHeader($headers, $header));
    }

    /**
     * @dataProvider \WyriHaximus\React\Tests\RingPHP\HttpClient\UtilsProvider::providerHeader
     */
    public function testHeader($expected, $input)
    {
        $this->assertSame($expected, Utils::header($input[0], $input[1]));
    }

    /**
     * @dataProvider \WyriHaximus\React\Tests\RingPHP\HttpClient\UtilsProvider::providerGetHeaderIndex
     */
    public function testGetHeaderIndex($expected, $input)
    {
        $this->assertSame($expected, Utils::getHeaderIndex($input[0], $input[1]));
    }

    /**
     * @dataProvider \WyriHaximus\React\Tests\RingPHP\HttpClient\UtilsProvider::providerPlaceHeader
     */
    public function testPlaceHeader($expected, $input)
    {
        $this->assertSame($expected, Utils::placeHeader($input[0], $input[1], $input[2]));
    }

    /**
     * @dataProvider \WyriHaximus\React\Tests\RingPHP\HttpClient\UtilsProvider::providerRedirectUrl
     */
    public function testRedirectUrl($expected, $request, $headers)
    {
        $this->assertSame($expected, Utils::redirectUrl($request, $headers));
    }
}
