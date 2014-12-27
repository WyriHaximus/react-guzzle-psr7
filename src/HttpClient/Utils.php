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

/**
 * Class Utils
 *
 * @package WyriHaximus\React\Guzzle\HttpClient
 */
class Utils
{
    /**
     * Check if $header exists in $headers.
     *
     * @param array  $headers Array container the headers to be searched.
     * @param string $header  Header to search for.
     *
     * @return boolean
     */
    public static function hasHeader(array $headers, $header)
    {
        $return = false;
        foreach ($headers as $name => $value) {
            $return = !strcasecmp($name, $header) ? true : $return;
        }
        return $return;
    }

    /**
     * Return value for $header in $headers.
     *
     * @param array  $headers Array container the headers to be searched.
     * @param string $header  Header to search for.
     *
     * @return null
     */
    public static function header(array $headers, $header)
    {
        $return = null;
        foreach ($headers as $name => $value) {
            $return = !strcasecmp($name, $header) ? $value : $return;
        }
        return $return;
    }
}
