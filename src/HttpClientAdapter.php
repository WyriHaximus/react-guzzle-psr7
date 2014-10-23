<?php

namespace WyriHaximus\React\RingPHP;

use GuzzleHttp\Ring\Core;
use GuzzleHttp\Ring\Future\FutureArray;
use WyriHaximus\React\RingPHP\HttpClient\Request;

class HttpClientAdapter
{
    public function __construct($loop)
    {
        $this->loop = $loop;

        $dnsResolverFactory = new \React\Dns\Resolver\Factory();
        $dnsResolver = $dnsResolverFactory->createCached('8.8.8.8', $this->loop);

        $factory = new \React\HttpClient\Factory();

        $this->client = $factory->create($this->loop, $dnsResolver);
    }

    public function __invoke(array $request)
    {
        $httpRequest = new Request($request, $this->client, $this->loop);
        return new FutureArray($httpRequest->send());
    }
}
