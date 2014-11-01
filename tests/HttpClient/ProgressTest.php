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
use React\HttpClient\Response;
use WyriHaximus\React\RingPHP\HttpClient\Progress;

/**
 * Class ProgressTest
 * @package WyriHaximus\React\Tests\Guzzle\HttpClient
 */
class ProgressTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $i = 0;
        $callbackFired = false;

        (
            new Progress(
                function ($a, $b, $c, $d) use (&$callbackFired, &$i) {
                    switch ($i) {
                        case 0:
                            $this->assertSame([0, 0, 7, 0], [$a, $b, $c, $d]);
                            break;
                        case 1:
                            $this->assertSame([0, 0, 7, 7], [$a, $b, $c, $d]);
                            break;
                        case 2:
                            $this->assertSame([1337, 0, 7, 7], [$a, $b, $c, $d]);
                            break;
                        case 3:
                            $this->assertSame([1337, 7, 7, 7], [$a, $b, $c, $d]);
                            break;
                        case 4:
                            $this->assertSame([1337, 37, 7, 7], [$a, $b, $c, $d]);
                            break;
                        case 5:
                            $this->assertSame([1337, 337, 7, 7], [$a, $b, $c, $d]);
                            break;
                        case 6:
                            $this->assertSame([1337, 1337, 7, 7], [$a, $b, $c, $d]);
                            break;
                    }
                    $i++;
                    $callbackFired = true;
                }
            )
        )
            ->onSending('foo:bar')
            ->onSent()->onResponse(
                new Response(
                    Phake::mock('React\Stream\Stream'),
                    '',
                    '',
                    200,
                    '',
                    [
                        'Content-Length' => 1337,
                    ]
                )
            )
            ->onData(str_pad('', 7, 'a'))
            ->onData(str_pad('', 30, 'a'))
            ->onData(str_pad('', 300, 'a'))
            ->onData(str_pad('', 1000, 'a'))
        ;

        $this->assertTrue($callbackFired);
    }
}
