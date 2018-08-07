<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Location;
use Symfony\Component\HttpFoundation\Response;

final class LoadSubLocationsTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadSubLocations::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadSubLocations::__invoke
     */
    public function testLoadSubLocations(): void
    {
        $location = new Location(41, 'Location 41');

        $this->backendMock
            ->expects(self::at(0))
            ->method('loadLocation')
            ->with(self::identicalTo('41'))
            ->will(self::returnValue($location));

        $this->backendMock
            ->expects(self::at(1))
            ->method('getSubLocations')
            ->with(self::identicalTo($location))
            ->will(
                self::returnValue(
                    [
                        new Location(42, 'Location 42'),
                        new Location(43, 'Location 43'),
                    ]
                )
            );

        $this->client->request('GET', '/cb/api/v1/test/browse/41/locations');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/browse/locations',
            Response::HTTP_OK
        );
    }
}
