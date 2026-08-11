<?php

namespace ComyoMedia\Shipcloud;

use ComyoMedia\Shipcloud\Api\Addresses;
use ComyoMedia\Shipcloud\Api\Carriers;
use ComyoMedia\Shipcloud\Api\PickupRequests;
use ComyoMedia\Shipcloud\Api\ShipmentQuotes;
use ComyoMedia\Shipcloud\Api\Shipments;

class Shipcloud
{
  private string|null $apiKey;

  public function __construct(string|null $apiKey = null)
  {
    $this->apiKey = $apiKey;
  }

  public static function make(string $apiKey)
  {
    return new self($apiKey);
  }

  public function addresses(): Addresses
  {
    return $this->getApiInstance('addresses');
  }

  public function carriers(): Carriers
  {
    return $this->getApiInstance('carriers');
  }

  public function pickupRequests(): PickupRequests
  {
    return $this->getApiInstance('pickupRequests');
  }

  public function shipments(): Shipments
  {
    return $this->getApiInstance('shipments');
  }

  public function shipmentQuotes(): ShipmentQuotes
  {
    return $this->getApiInstance('shipmentQuotes');
  }

  protected function getApiInstance(string $method)
  {
    $class = "\\ComyoMedia\\Shipcloud\\Api\\" . ucwords($method);

    if (class_exists($class)) {
      return new $class($this->apiKey);
    }
    throw new \BadMethodCallException("Undefined method [{$method}] called.");
  }
}
