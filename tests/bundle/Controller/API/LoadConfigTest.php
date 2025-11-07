<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadConfig;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Location;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadConfig::class)]
final class LoadConfigTest extends JsonApiTestCase
{
    public function testLoadConfig(): void
    {
        $this->backendMock
            ->method('getSections')
            ->willReturn(
                [
                    new Location(42, 'Location 42'),
                    new Location(42, 'Location 42'),
                ],
            );

        $this->client->request('GET', '/cb/api/test/config');

        $this->assertResponse(
            $this->client->getResponse(),
            'config/result',
            Response::HTTP_OK,
        );
    }
}
