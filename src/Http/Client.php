<?php

namespace ComyoMedia\Shipcloud\Http;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client extends GuzzleClient implements ClientInterface
{

  public function __construct(string $apiKey)
  {
    parent::__construct([
      'base_uri' => 'https://api.shipcloud.io/v1/',
      'auth' => [$apiKey, ''],
      'headers' => [
        'Content-Type' => 'application/json'
      ]
    ]);
  }

  public function send(RequestInterface $request, array $options = []): ResponseInterface
  {
    return parent::send($request, $options);
  }
}
