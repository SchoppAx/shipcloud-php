<?php

declare(strict_types=1);

use ComyoMedia\Shipcloud\Api\Shipments;
use ComyoMedia\Shipcloud\Shipcloud;
use PHPUnit\Framework\TestCase;

final class ShipcloudTest extends TestCase
{
    public function testMakeReturnsConfiguredClient(): void
    {
        $shipcloud = Shipcloud::make('test-key');

        $this->assertInstanceOf(Shipcloud::class, $shipcloud);
        $this->assertInstanceOf(Shipments::class, $shipcloud->shipments());
    }
}
