<?php

/**
 * This file is part of ReactGuzzleRing.
 *
 ** (c) 2015 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WyriHaximus\React\RingPHP\HttpClient;

use GuzzleHttp\Stream\StreamInterface;
use GuzzleHttp\Stream\Utils as GuzzleUtils;

/**
 * Class Stream
 *
 * @package WyriHaximus\React\RingPHP\HttpClient
 */
class Stream implements StreamInterface
{
    protected $stream;
    protected $eof = false;
    protected $size = 0;
    protected $buffer = '';
    protected $loop;

    public function __construct(array $options)
    {
        $options['response']->on(
            'data',
            function ($data) {
                $this->buffer .= $data;
                $this->size = $this->buffer;
            }
        );
        $options['request']->on(
            'end',
            function () {
                $this->eof = true;
            }
        );

        $this->loop = $options['loop'];
    }

    public function eof()
    {
        return $this->eof;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function isReadable()
    {
        return true;
    }

    public function tell()
    {
        return false;
    }

    public function write($string)
    {
        return false;
    }

    public function isWritable()
    {
        return false;
    }

    public function isSeekable()
    {
        return false;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        return false;
    }

    public function read($length)
    {
        $this->toTickOrNotToTick();

        if (strlen($this->buffer) <= $length) {
            $buffer = $this->buffer;
            $this->buffer = '';
            return $buffer;
        }

        $buffer = substr($this->buffer, 0, $length);
        $this->buffer = substr($this->buffer, $length);
        return $buffer;
    }

    public function getContents()
    {
        $buffer = '';
        while (!$this->eof()) {
            $buffer .= $this->read(1000000);
        }
        return $buffer;
    }

    public function __toString()
    {
        return GuzzleUtils::copyToString($this->stream);
    }

    public function getMetadata($key = null)
    {
        $metadata = [
            'timed_out'     => '',
            'blocked'       => false,
            'eof'           => $this->eof,
            'unread_bytes'  => '',
            'stream_type'   => '',
            'wrapper_type'  => '',
            'wrapper_data'  => '',
            'mode'          => '',
            'seekable'      => false,
            'uri'           => '',
        ];

        if (!$key) {
            return $metadata;
        }

        return isset($metadata[$key]) ? $metadata[$key] : null;
    }

    public function attach($stream)
    {
    }

    public function detach()
    {
    }

    public function close()
    {
    }

    protected function toTickOrNotToTick()
    {
        if (strlen($this->buffer) == 0) {
            $this->loop->tick();
        }
    }
}
