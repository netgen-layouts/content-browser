<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadSubLocations;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Location;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadSubLocations::class)]
final class LoadSubLocationsTest extends ApiTestCase
{
    public function testLoadSubLocations(): void
    {
        $location = new Location(41, 'Location 41');

        $this->backendStub
            ->method('loadLocation')
            ->willReturn($location);

        $this->backendStub
            ->method('getSubLocations')
            ->willReturn(
                [
                    new Location(42, 'Location 42'),
                    new Location(43, 'Location 43'),
                ],
            );

        $this->browser()
            ->get('/cb/api/test/browse/41/locations')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('browse/locations');
    }
}
