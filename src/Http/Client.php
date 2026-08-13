<?php

namespace ComyoMedia\Shipcloud\Http;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client implements ClientInterface
{
  private GuzzleClient $client;

  public function __construct(string $apiKey)
  {
    $this->client = new GuzzleClient([
      'base_uri' => 'https://api.shipcloud.io/v1/',
      'auth' => [$apiKey, ''],
      'headers' => [
        'Content-Type' => 'application/json'
      ]
    ]);
  }

  public function send(RequestInterface $request, array $options = []): ResponseInterface
  {
    return $this->client->send($request, $options);
  }

  public function request(string $method, string $uri, array $options = []): ResponseInterface
  {
    return $this->client->request($method, $uri, $options);
  }
}
