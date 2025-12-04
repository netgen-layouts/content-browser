<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadConfig;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Location;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadConfig::class)]
final class LoadConfigTest extends ApiTestCase
{
    public function testLoadConfig(): void
    {
        $this->backendStub
            ->method('getSections')
            ->willReturn(
                [
                    new Location(42, 'Location 42'),
                    new Location(42, 'Location 42'),
                ],
            );

        $this->browser()
            ->get('/cb/api/test/config')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('config/result');
    }
}
