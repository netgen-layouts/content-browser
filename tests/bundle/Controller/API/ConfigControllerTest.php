<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Location;
use Symfony\Component\HttpFoundation\Response;

final class ConfigControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\ConfigController::getAvailableColumns
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\ConfigController::getConfig
     */
    public function testGetConfig()
    {
        $this->backendMock
            ->expects($this->any())
            ->method('getDefaultSections')
            ->will(
                $this->returnValue(
                    array(
                        new Location(42, 'Location 42'),
                        new Location(42, 'Location 42'),
                    )
                )
            );

        $this->client->request('GET', '/cb/api/v1/test/config');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/config/result',
            Response::HTTP_OK
        );
    }
}
