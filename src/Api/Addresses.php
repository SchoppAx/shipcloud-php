<?php

namespace ComyoMedia\Shipcloud\Api;

class Addresses extends Api
{
  public function create(array $body, array $parameters = []): array
  {
    return $this->post('addresses', $parameters, $body);
  }

  public function find(string $id): array
  {
    return $this->get("addresses/{$id}");
  }

  public function all(): array
  {
    return $this->get('addresses');
  }
}
