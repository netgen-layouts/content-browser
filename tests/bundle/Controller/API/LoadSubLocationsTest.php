<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadSubLocations;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Location;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadSubLocations::class)]
final class LoadSubLocationsTest extends JsonApiTestCase
{
    public function testLoadSubLocations(): void
    {
        $location = new Location(41, 'Location 41');

        $this->backendMock
            ->method('loadLocation')
            ->with(self::identicalTo('41'))
            ->willReturn($location);

        $this->backendMock
            ->method('getSubLocations')
            ->with(self::identicalTo($location))
            ->willReturn(
                [
                    new Location(42, 'Location 42'),
                    new Location(43, 'Location 43'),
                ],
            );

        $this->client->request('GET', '/cb/api/test/browse/41/locations');

        $this->assertResponse(
            $this->client->getResponse(),
            'browse/locations',
            Response::HTTP_OK,
        );
    }
}
