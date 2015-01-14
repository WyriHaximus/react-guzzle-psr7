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
    public function testHasHeader()
    {
        $this->assertTrue(Utils::hasHeader([
            'foo' => '',
            'bar' => '',
        ], 'foo'));

        $this->assertTrue(Utils::hasHeader([
            'foo' => '',
            'bAr' => '',
        ], 'baR'));

        $this->assertTrue(!Utils::hasHeader([
            'foo' => '',
            'bar' => '',
        ], 'fo'));

        $this->assertTrue(!Utils::hasHeader([
            'foo' => '',
            'bAr' => '',
        ], 'Fo'));
    }

    public function testHeader()
    {
        $this->assertSame('123', Utils::header([
            'foo' => '123',
            'bar' => '456',
        ], 'foo'));

        $this->assertSame('456', Utils::header([
            'foo' => '123',
            'bAr' => '456',
        ], 'baR'));

        $this->assertSame(null, Utils::header([
            'foo' => '',
            'bar' => '',
        ], 'fo'));

        $this->assertSame(null, Utils::header([
            'foo' => '',
            'bAr' => '',
        ], 'Fo'));
    }

    public function testGetHeaderIndex()
    {
        $this->assertSame('foo', Utils::getHeaderIndex([
            'foo' => '123',
            'bar' => '456',
        ], 'foo'));

        $this->assertSame('bAr', Utils::getHeaderIndex([
            'foo' => '123',
            'bAr' => '456',
        ], 'baR'));

        $this->assertSame(null, Utils::getHeaderIndex([
            'foo' => '',
            'bar' => '',
        ], 'fo'));

        $this->assertSame(null, Utils::getHeaderIndex([
            'foo' => '',
            'bAr' => '',
        ], 'Fo'));
    }

    public function testPlaceHeader()
    {
        $this->assertSame([
            'foo' => ['789'],
            'bar' => '456',
        ], Utils::placeHeader([
            'foo' => '123',
            'bar' => '456',
        ], 'foo', ['789']));

        $this->assertSame([
            'foo' => '123',
            'bAr' => ['789'],
        ], Utils::placeHeader([
            'foo' => '123',
            'bAr' => '456',
        ], 'baR', ['789']));

        $this->assertSame([
            'foo' => '',
            'bar' => '',
            'fo' => ['789'],
        ], Utils::placeHeader([
            'foo' => '',
            'bar' => '',
        ], 'fo', ['789']));

        $this->assertSame([
            'foo' => '',
            'bAr' => '',
            'Fo' => ['789'],
        ], Utils::placeHeader([
            'foo' => '',
            'bAr' => '',
        ], 'Fo', ['789']));
    }
}
