<?php

namespace ComyoMedia\Shipcloud\Api;

class ShipmentQuotes extends Api
{
  public function create(array $body, $parameters = []): array
  {
    return $this->post('shipment_quotes', $parameters, $body);
  }
}
