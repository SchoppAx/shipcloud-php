<?php

namespace ComyoMedia\Shipcloud\Api;

class PickupRequests extends Api
{
  public function create(array $body, $parameters = []): array
  {
    return $this->post('pickup_requests', $parameters, $body);
  }
}
