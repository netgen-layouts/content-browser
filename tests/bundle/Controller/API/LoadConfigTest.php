<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Location;
use Symfony\Component\HttpFoundation\Response;

final class LoadConfigTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadConfig::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadConfig::__invoke
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadConfig::getAvailableColumns
     */
    public function testLoadConfig(): void
    {
        $this->backendMock
            ->expects($this->any())
            ->method('getSections')
            ->will(
                $this->returnValue(
                    [
                        new Location(42, 'Location 42'),
                        new Location(42, 'Location 42'),
                    ]
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
