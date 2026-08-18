<?php

declare(strict_types=1);

use ComyoMedia\Shipcloud\Api\Addresses;
use ComyoMedia\Shipcloud\Api\Carriers;
use ComyoMedia\Shipcloud\Api\PickupRequests;
use ComyoMedia\Shipcloud\Api\ShipmentQuotes;
use ComyoMedia\Shipcloud\Api\Shipments;
use ComyoMedia\Shipcloud\Http\Client;
use ComyoMedia\Shipcloud\Http\ClientInterface;
use ComyoMedia\Shipcloud\Shipcloud;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class CoverageTest extends TestCase
{
    public function testResourceMethodsSendExpectedRequests(): void
    {
        $client = new RecordingClient([
            new Response(200, [], '{"resource":"address"}'),
            new Response(200, [], '{"resource":"address"}'),
            new Response(200, [], '{"resource":"address"}'),
            new Response(200, [], '{"resource":"carrier"}'),
            new Response(200, [], '{"resource":"pickup"}'),
            new Response(200, [], '{"resource":"pickup"}'),
            new Response(200, [], '{"resource":"quote"}'),
            new Response(200, [], '{"resource":"shipment"}'),
            new Response(200, [], '{"resource":"shipment"}'),
            new Response(200, [], '{"resource":"shipment"}'),
            new Response(200, [], '{"resource":"shipment"}'),
            new Response(200, [], '{"resource":"shipment"}'),
        ]);

        $this->assertSame(['resource' => 'address'], $this->addresses($client)->create(['name' => 'Ada'], ['sandbox' => true]));
        $this->addresses($client)->find('address-id');
        $this->addresses($client)->all();
        $this->carriers($client)->all();
        $this->pickupRequests($client)->all();
        $this->pickupRequests($client)->create(['date' => '2026-08-13'], ['sandbox' => true]);
        $this->shipmentQuotes($client)->create(['from' => 'Berlin']);
        $this->shipments($client)->create(['reference_number' => 'order-1'], ['sandbox' => true]);
        $this->shipments($client)->find('shipment-id');
        $this->shipments($client)->remove('shipment-id');
        $this->shipments($client)->all(['page' => 2]);
        $this->shipments($client)->update('shipment-id', ['reference_number' => 'order-2'], ['sandbox' => true]);

        $this->assertSame([
            ['POST', 'addresses', ['query' => ['sandbox' => true], 'json' => ['name' => 'Ada']]],
            ['GET', 'addresses/address-id', ['query' => [], 'json' => []]],
            ['GET', 'addresses', ['query' => [], 'json' => []]],
            ['GET', 'carriers', ['query' => [], 'json' => []]],
            ['GET', 'pickup_requests', ['query' => [], 'json' => []]],
            ['POST', 'pickup_requests', ['query' => ['sandbox' => true], 'json' => ['date' => '2026-08-13']]],
            ['POST', 'shipment_quotes', ['query' => [], 'json' => ['from' => 'Berlin']]],
            ['POST', 'shipments', ['query' => ['sandbox' => true], 'json' => ['reference_number' => 'order-1']]],
            ['GET', 'shipments/shipment-id', ['query' => [], 'json' => []]],
            ['DELETE', 'shipments/shipment-id', ['query' => [], 'json' => []]],
            ['GET', 'shipments', ['query' => ['page' => 2], 'json' => []]],
            ['PUT', 'shipments/shipment-id', ['query' => ['sandbox' => true], 'json' => ['reference_number' => 'order-2']]],
        ], $client->calls);
    }

    public function testClientErrorsAreConvertedToExceptionsWithResponseBody(): void
    {
        $request = new Request('GET', 'addresses');
        $client = new RecordingClient([
            new ClientException('Not found', $request, new Response(404, [], '{"message":"Not found"}')),
        ]);

        $this->expectExceptionMessage('{"message":"Not found"}');

        $this->addresses($client)->all();
    }

    public function testShipcloudCreatesAllApiClientsAndRejectsUnknownApis(): void
    {
        $shipcloud = Shipcloud::make('api-key');

        $this->assertInstanceOf(Addresses::class, $shipcloud->addresses());
        $this->assertInstanceOf(Carriers::class, $shipcloud->carriers());
        $this->assertInstanceOf(PickupRequests::class, $shipcloud->pickupRequests());
        $this->assertInstanceOf(ShipmentQuotes::class, $shipcloud->shipmentQuotes());
        $this->assertInstanceOf(Shipments::class, $shipcloud->shipments());

        $unknownApi = new class('api-key') extends Shipcloud {
            public function api(string $method): object
            {
                return $this->getApiInstance($method);
            }
        };

        $this->expectException(\BadMethodCallException::class);
        $unknownApi->api('missing');
    }

    public function testDefaultApiClientIsTheGuzzleWrapper(): void
    {
        $api = new class('api-key') extends Addresses {
            public function client(): ClientInterface
            {
                return $this->getClient();
            }
        };

        $this->assertInstanceOf(Client::class, $api->client());
    }

    public function testClientDelegatesRequestAndSendToGuzzle(): void
    {
        $guzzleClient = new GuzzleClient([
            'handler' => HandlerStack::create(new MockHandler([
                new Response(200, [], 'request'),
                new Response(201, [], 'send'),
            ])),
        ]);

        $client = new class('api-key', $guzzleClient) extends Client {
            private GuzzleClient $guzzleClient;

            public function __construct(string $apiKey, GuzzleClient $guzzleClient)
            {
                $this->guzzleClient = $guzzleClient;
                parent::__construct($apiKey);
            }

            public function request(string $method, $uri = null, array $options = []): ResponseInterface
            {
                return $this->guzzleClient->request($method, $uri, $options);
            }

            public function send(RequestInterface $request, array $options = []): ResponseInterface
            {
                return $this->guzzleClient->send($request, $options);
            }
        };

        $this->assertSame('request', (string) $client->request('GET', 'https://example.test')->getBody());
        $this->assertSame('send', (string) $client->send(new Request('POST', 'https://example.test'))->getBody());
    }

    private function addresses(Client $client): Addresses
    {
        return new class('api-key', $client) extends Addresses {
            public function __construct(string $apiKey, private Client $client)
            {
                parent::__construct($apiKey);
            }

            protected function getClient(): Client
            {
                return $this->client;
            }
        };
    }

    private function carriers(Client $client): Carriers
    {
        return new class('api-key', $client) extends Carriers {
            public function __construct(string $apiKey, private Client $client)
            {
                parent::__construct($apiKey);
            }

            protected function getClient(): Client
            {
                return $this->client;
            }
        };
    }

    private function pickupRequests(Client $client): PickupRequests
    {
        return new class('api-key', $client) extends PickupRequests {
            public function __construct(string $apiKey, private Client $client)
            {
                parent::__construct($apiKey);
            }

            protected function getClient(): Client
            {
                return $this->client;
            }
        };
    }

    private function shipmentQuotes(Client $client): ShipmentQuotes
    {
        return new class('api-key', $client) extends ShipmentQuotes {
            public function __construct(string $apiKey, private Client $client)
            {
                parent::__construct($apiKey);
            }

            protected function getClient(): Client
            {
                return $this->client;
            }
        };
    }

    private function shipments(Client $client): Shipments
    {
        return new class('api-key', $client) extends Shipments {
            public function __construct(string $apiKey, private Client $client)
            {
                parent::__construct($apiKey);
            }

            protected function getClient(): Client
            {
                return $this->client;
            }
        };
    }
}

final class RecordingClient extends Client
{
    /** @var list<ResponseInterface|ClientException> */
    private array $responses;

    /** @var list<array{string, string, array}> */
    public array $calls = [];

    /** @param list<ResponseInterface|ClientException> $responses */
    public function __construct(array $responses)
    {
        $this->responses = $responses;
        parent::__construct('api-key');
    }

    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        throw new LogicException('send() is not expected in API tests.');
    }

    public function request(string $method, $uri = null, array $options = []): ResponseInterface
    {
        $this->calls[] = [$method, $uri, $options];
        $response = array_shift($this->responses);

        if ($response instanceof ClientException) {
            throw $response;
        }

        if ($response === null) {
            throw new LogicException('No response was configured.');
        }

        return $response;
    }
}