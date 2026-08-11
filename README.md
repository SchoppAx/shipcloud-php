# shipcloud-php - shipcloud API v1 PHP 8.2+ library

An easy-to-use PHP package to communicate with [shipcloud's API](http://developers.shipcloud.io).

## Installation

`composer require schoppax/shipcloud-php`

or add `"schoppax/shipcloud-php"` in your "require" object of your project `composer.json`.

## Documentation

[shipcloud API reference](https://developers.shipcloud.io/swagger-ui)


## Examples

Initiate the shipcloud client.
```php
<?php

require 'vendor/autoload.php';

$apiKey = 'api-key';
$shipcloud = new ComyoMedia\Shipcloud\Shipcloud($apiKey);
```

#### Addresses
```php
// Getting a list of addresses
var_dump($shipcloud->addresses()->all());

// Create a new addresses
var_dump($shipcloud->addresses()->create([
  "first_name" => "Serge",
  "last_name" => "Sender",
  "company" => "Sender Corp.",
  "street" => "Sender Str.",
  "street_no" => "99",
  "zip_code" => "20148",
  "city" => "Hamburg",
  "country" => "DE"
]));

// Returns a single address based on its identifier
var_dump($shipcloud->addresses()->find("1c81efb7-9b95-4dd8-92e3-cac1bca3df6f");
```

#### Carrier
```php
// Returns all carriers for the user associated with the api key
var_dump($shipcloud->carriers()->all());
```

#### Pickup requests
```php
// Get all pickup requests for this user
var_dump($shipcloud->pickupRequests()->all());

// Create a pickup request with a carrier, so they come and get the parcels
var_dump($shipcloud->pickupRequests()->create([
  "carrier" => "dpd",
  "pickup_time" => [
    "earliest" => "2018-07-30T09:00:00+02:00",
    "latest" => "2018-07-30T18:00:00+02:00"
  ],
  "pickup_address" => [
    "company" => "Muster-Company",
    "first_name" => "Max",
    "last_name" => "Mustermann",
    "care_of" => null,
    "street" => "Musterstraße",
    "street_no" => "42",
    "zip_code" => "22457",
    "city" => "Hamburg",
    "state" => null,
    "country" => "DE",
    "phone" => "555-555",
    "id" => "286daf26-c845-4dba-ae49-75582fbced00"
  ]
]));
```

#### Shipment Quotes
```php
// Find out how much we will charge you for a specific shipment when using shipcloud carrier contracts.
var_dump($shipcloud->shipmentQuotes()->create([
  "carrier" => "dhl",
  "service" => "standard",
  "to" => [
    "street" => "Beispielstrasse",
    "street_no" => "42",
    "zip_code" => "22100",
    "city" => "Hamburg",
    "country" => "DE"
  ],
  "from" => [
    "street" => "Musterstrasse",
    "street_no" => "23",
    "zip_code" => "20148",
    "city" => "Hamburg",
    "country" => "DE"
  ],
  "packages" => [
    [
      "weight" => 1.5,
      "length" => 20,
      "width" => 20,
      "height" => 20
    ]
  ]
]));
```

#### Shipments
```php
// Returns a list of shipments
var_dump($shipcloud->shipments()->all());

// Create a shipment
var_dump($shipcloud->shipments()->create([
  "to" => [
    "company" => "Receiver Inc.",
    "last_name" => "Mustermann",
    "street" => "Beispielstrasse",
    "street_no" => "42",
    "city" => "Hamburg",
    "zip_code" => "22100",
    "country" => "DE"
  ],
  "packages" => [
    [
      "weight" => 1.5,
      "length" => 20,
      "width" => 20,
      "height" => 20,
      "type" => "parcel"
    ]
  ],
  "carrier" => "dhl",
  "service" => "standard",
  "reference_number" => "ref123456",
  "notification_email" => "person@example.com",
  "create_shipping_label" => true
]));

// Returns a single shipment based on the id
var_dump($shipcloud->shipments()->find("3a186c51d4281acbecf5ed38805b1db92a9d668b");

// Deletes a single shipment.
var_dump($shipcloud->shipments()->remove("3a186c51d4281acbecf5ed38805b1db92a9d668b");

// Updates a single shipment based on the id. Unfortunately you can't update the customs_declaration attribute at the moment.
var_dump($shipcloud->shipments()->update("3a186c51d4281acbecf5ed38805b1db92a9d668b", [
  "to" => [
    "company" => "Receiver Inc.",
    "last_name" => "Mustermann",
    "street" => "Beispielstrasse",
    "street_no" => "42",
    "city" => "Hamburg",
    "zip_code" => "22100",
    "country" => "DE"
  ],
  "packages" => [
    [
      "weight" => 1.5,
      "length" => 20,
      "width" => 20,
      "height" => 20,
      "type" => "parcel"
    ]
  ],
  "carrier" => "dhl",
  "service" => "standard",
  "reference_number" => "ref123456",
  "notification_email" => "person@example.com",
  "create_shipping_label" => true
]);

```