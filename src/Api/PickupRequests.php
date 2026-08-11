<?php

namespace ComyoMedia\Shipcloud\Api;

class PickupRequests extends Api
{
  public function all(): array
  {
    return $this->get('pickup_requests');
  }
  public function create(array $body, $parameters = []): array
  {
    return $this->post('pickup_requests', $parameters, $body);
  }
}
