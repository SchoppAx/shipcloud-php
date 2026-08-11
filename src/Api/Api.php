<?php

namespace ComyoMedia\Shipcloud\Api;

use ComyoMedia\Shipcloud\Http\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

abstract class Api implements ApiInterface
{
  protected string $apiKey;

  public function __construct(string $apiKey)
  {
    $this->apiKey = $apiKey;
  }

  public function get(string $uri = '', array $parameters = []): array
  {
    $body = (string) $this->execute('get', $uri, $parameters)->getBody();
    return json_decode($body, true);
  }

  public function post(string $uri = '', array $parameters = [], array $body = []): array
  {
    $body = (string) $this->execute('post', $uri, $parameters, $body)->getBody();
    return json_decode($body, true);
  }

  public function delete(string $uri = '', array $parameters = [], array $body = []): array
  {
    $body = (string) $this->execute('delete', $uri, $parameters, $body)->getBody();
    return json_decode($body, true);
  }

  public function execute(string $httpMethod, string $uri, array $parameters = [], array $body = []): ResponseInterface
  {
    try {
      $client = $this->getClient();

      return $client->{$httpMethod}($uri, [
        'query' => $parameters,
        'json'  => $body
      ]);
    } catch (ClientException $e) {
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      throw new \Exception($responseBodyAsString);
    }
  }

  protected function getClient(): Client
  {
    return new Client($this->apiKey);
  }
}
