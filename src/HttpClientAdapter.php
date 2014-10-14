<?php

namespace WyriHaximus\React\Guzzle\Ring\Client;

//use GuzzleHttp\Ring\Future;

use React\HttpClient\Response;

class HttpClientAdapter
{
    public function __construct(array $options = [])
    {

        $this->loop = \React\EventLoop\Factory::create();

        $dnsResolverFactory = new \React\Dns\Resolver\Factory();
        $dnsResolver = $dnsResolverFactory->createCached('8.8.8.8', $this->loop);

        $factory = new \React\HttpClient\Factory();

        $this->client = $factory->create($this->loop, $dnsResolver);
    }

    public function __invoke(array $request)
    {
        $body = '';
        $httpResponse = null;

        $httpRequest = $this->client->request('GET', 'http://127.0.0.1:8000/');
        $httpRequest->on('response', function (Response $response) use (&$body, &$httpResponse) {
            $httpResponse = $response;
            $response->on('data', function ($data) use (&$body) {
                $body .= $data;
            });
        });
        $httpRequest->on('error', function($error) use ($request) {
            $then = $request['then'];
            $then($error);
        });
        $httpRequest->on('end', function() use (&$done, &$body, $request, &$httpResponse) {
            $then = $request['then'];
            if ($httpResponse === null) {
                $then($httpResponse);
                $then($body);
                return;
            }
            $then([
                'body' => $body,
                'headers' => $httpResponse->getHeaders(),
                'status' => $httpResponse->getCode(),
                'reason' => $httpResponse->getReasonPhrase(),
            ]);
        });
        $httpRequest->end();

        $this->loop->run();
    }

}
