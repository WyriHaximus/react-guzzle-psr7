<?php

namespace WyriHaximus\React\RingPHP\Client;

use GuzzleHttp\Ring\Core;
use GuzzleHttp\Ring\Future\FutureArray;
use React\HttpClient\Response;
use React\Promise\Deferred;

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
        $deferred = new Deferred();

        $body = '';
        $httpResponse = null;

        $httpRequest = $this->client->request($request['http_method'], $request['url']);
        $httpRequest->on('response', function (Response $response) use (&$body, &$httpResponse) {
            $httpResponse = $response;
            $response->on('data', function ($data) use (&$body) {
                $body .= $data;
            });
        });
        $httpRequest->on('end', function() use (&$done, &$body, $request, &$httpResponse, $deferred) {
            $response = [
                'body' => $body,
                'headers' => $httpResponse->getHeaders(),
                'status' => $httpResponse->getCode(),
                'reason' => $httpResponse->getReasonPhrase(),
            ];

            Core::rewindBody($response);

            $deferred->resolve($response);
        });
        $httpRequest->end();

        return new FutureArray($deferred->promise());
    }

}
