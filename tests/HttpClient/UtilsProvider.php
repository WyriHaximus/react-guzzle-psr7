<?php

/**
 * This file is part of ReactGuzzleRing.
 *
 ** (c) 2015 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WyriHaximus\React\Tests\RingPHP\HttpClient;

/**
 * Class UtilsProvider
 *
 * @package WyriHaximus\React\Tests\Guzzle\HttpClient
 */
class UtilsProvider extends \PHPUnit_Framework_TestCase
{
    public function providerHasHeader()
    {
        return [
            [
                true,
                [
                    'foo' => '',
                    'bar' => '',
                ],
                'foo',
            ],
            [
                true,
                [
                    'foo' => '',
                    'bAr' => '',
                ],
                'baR',
            ],
            [
                false,
                [
                    'foo' => '',
                    'bar' => '',
                ],
                'fo',
            ],
            [
                false,
                [
                    'foo' => '',
                    'bAr' => '',
                ],
                'Fo',
            ],
        ];
    }

    public function providerHeader()
    {
        return [
            [
                '123',
                [
                    [
                        'foo' => '123',
                        'bar' => '456',
                    ],
                    'foo',
                ],
            ],
            [
                '456',
                [
                    [
                        'foo' => '123',
                        'bAr' => '456',
                    ],
                    'baR',
                ],
            ],
            [
                null,
                [
                    [
                        'foo' => '',
                        'bar' => '',
                    ],
                    'fo',
                ],
            ],
            [
                null,
                [
                    [
                        'foo' => '',
                        'bAr' => '',
                    ],
                    'Fo',
                ],
            ],
        ];
    }

    public function providerGetHeaderIndex()
    {
        return [
            [
                'foo',
                [
                    [
                        'foo' => '123',
                        'bar' => '456',
                    ],
                    'foo',
                ],
            ],
            [
                'bAr',
                [
                    [
                        'foo' => '123',
                        'bAr' => '456',
                    ],
                    'baR',
                ],
            ],
            [
                null,
                [
                    [
                        'foo' => '',
                        'bar' => '',
                    ],
                    'fo',
                ],
            ],
            [
                null,
                [
                    [
                        'foo' => '',
                        'bAr' => '',
                    ],
                    'Fo',
                ],
            ],
        ];
    }

    public function providerPlaceHeader()
    {
        return [
            [
                [
                    'foo' => [
                        '789',
                    ],
                    'bar' => '456',
                ],
                [
                    [
                        'foo' => '123',
                        'bar' => '456',
                    ],
                    'foo',
                    [
                        '789',
                    ],
                ],
            ],
            [
                [
                    'foo' => '123',
                    'bAr' => [
                        '789',
                    ],
                ],
                [
                    [
                        'foo' => '123',
                        'bAr' => '456',
                    ],
                    'baR',
                    [
                        '789',
                    ],
                ],
            ],
            [
                [
                    'foo' => '',
                    'bar' => '',
                    'fo' => [
                        '789',
                    ],
                ],
                [
                    [
                        'foo' => '',
                        'bar' => '',
                    ],
                    'fo',
                    [
                        '789',
                    ],
                ],
            ],
            [
                [
                    'foo' => '',
                    'bAr' => '',
                    'Fo' => [
                        '789',
                    ],
                ],
                [
                    [
                        'foo' => '',
                        'bAr' => '',
                    ],
                    'Fo',
                    [
                        '789',
                    ],
                ],
            ],
        ];
    }



    public function providerRedirectUrl()
    {
        return [
            /**
             * Simple HTTP to HTTPS redirect nothing fancy.
             */
            [
                'https://example.com/',
                [
                    'url' => 'http://example.com/',
                    'headers' => [
                        'Host' => [
                            'example.com',
                        ],
                    ],
                ],
                [
                    'Location' => 'https://example.com/',
                ],
            ],

            /**
             * Simple HTTP to HTTPS redirect with only the protocol
             */
            [
                'https://example.com/',
                [
                    'url' => 'http://example.com/',
                    'headers' => [
                        'Host' => [
                            'example.com',
                        ],
                    ],
                ],
                [
                    'Location' => 'https://',
                ],
            ],

            /**
             * Absolute URL redirect
             */
            [
                'https://example.com/foo.bar',
                [
                    'url' => 'https://example.com/',
                    'headers' => [
                        'Host' => [
                            'example.com',
                        ],
                    ],
                ],
                [
                    'Location' => '/foo.bar',
                ],
            ],

            /**
             * Relative URL redirect
             */
            [
                'https://example.com/foo.bar',
                [
                    'url' => 'https://example.com/pizza/foo.bar',
                    'headers' => [
                        'Host' => [
                            'example.com',
                        ],
                    ],
                ],
                [
                    'Location' => '../foo.bar',
                ],
            ],

            /**
             * Different hostname redirect
             */
            [
                'https://www.example.com/',
                [
                    'url' => 'https://example.com/',
                    'headers' => [
                        'Host' => [
                            'example.com',
                        ],
                    ],
                ],
                [
                    'Location' => 'https://www.example.com/',
                ],
            ],
        ];
    }
}
