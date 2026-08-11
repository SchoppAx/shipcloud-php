<?php

namespace ComyoMedia\Shipcloud\Api;

class Carriers extends Api
{
  public function all(): array
  {
    return $this->get('carriers');
  }
}
