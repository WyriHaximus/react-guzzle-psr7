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

use GuzzleHttp\Url;

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

    /**
     * Get the correct, case sensitive, header name index
     *
     * @param array  $headers The header collection to search through.
     * @param string $header  The header name to find.
     *
     * @return null|string
     */
    public static function getHeaderIndex(array $headers, $header)
    {
        $return = null;
        foreach ($headers as $name => $value) {
            $return = !strcasecmp($name, $header) ? $name : $return;
        }
        return $return;
    }

    /**
     * (Re)place $header in the $headers collection.
     *
     * @param array  $headers Header collection.
     * @param string $header  Header name to (re)place
     * @param array  $value   Value to put into the header/
     *
     * @return array
     */
    public static function placeHeader(array $headers, $header, array $value)
    {
        if (!static::hasHeader($headers, $header)) {
            $headers[$header] = $value;
            return $headers;
        }

        $headers[static::getHeaderIndex($headers, $header)] = $value;
        return $headers;
    }

    public static function redirectUrl(array $request, array $headers)
    {
        $locationUrl = static::header($headers, 'location');

        if (!is_array($locationUrl)) {
            return static::attemptRedirectUrl($request['url'], $locationUrl);
        }

        foreach ($locationUrl as $to) {
            try {
                $to = (string) Url::fromString($to);
                return static::attemptRedirectUrl($request['url'], $to);
            } catch (\Exception $exception) {}
        }

        new \InvalidArgumentException('Location header can\'t be an array');
    }

    protected static function attemptRedirectUrl($from, $to)
    {
        try {
            return (string) Url::fromString($from)->combine($to);
        } catch (\InvalidArgumentException $exception) {
            if (in_array($to, [
                'http://',
                'https://',
            ])) {
                $url = Url::fromString($from);
                $url->setScheme(str_replace('://', '', $to));
                return (string) $url;
            }

            throw $exception;
        }
    }
}
