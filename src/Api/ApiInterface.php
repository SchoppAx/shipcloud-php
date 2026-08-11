<?php

declare(strict_types=1);

namespace ComyoMedia\Shipcloud\Api;

use Psr\Http\Message\ResponseInterface;

interface ApiInterface
{
  public function get(string $url = '', array $parameters = []): array;

  public function post(string $url = '', array $parameters = [], array $body = []): array;

  public function put(string $url = '', array $parameters = [], array $body = []): array;

  public function delete(string $url = '', array $parameters = [], array $body = []): array;

  public function execute(string $httpMethod, string $url, array $parameters = [], array $body = []): ResponseInterface;
}
