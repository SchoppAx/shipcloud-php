<?php

namespace ComyoMedia\Shipcloud\Api;

class Addresses extends Api
{
    public function create($body, $parameters = [])
    {
        return $this->post('addresses', $parameters, $body);
    }

    public function find($id)
    {
        return $this->get("addresses/{$id}");
    }

    public function all($parameters = [])
    {
        return $this->get('addresses', $parameters);
    }
}