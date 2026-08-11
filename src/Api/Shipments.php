<?php

namespace ComyoMedia\Shipcloud\Api;

class Shipments extends Api
{
  public function create(array $body, array $parameters = []): array
  {
    return $this->post('shipments', $parameters, $body);
  }

  public function find(string $id): array
  {
    return $this->get("shipments/{$id}");
  }

  public function remove(string $id): array
  {
    return $this->delete("shipments/{$id}");
  }

  public function all(array $parameters = []): array
  {
    return $this->get('shipments', $parameters);
  }

  public function update(string $id, array $body = [], array $parameters = []): array
  {
    return $this->put("shipments/{$id}", $parameters, $body);
  }
}
