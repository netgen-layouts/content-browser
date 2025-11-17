<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadItemsByValue;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadItemsByValue::class)]
final class LoadItemsByValueTest extends ApiTestCase
{
    public function testLoadItemsByValue(): void
    {
        $this->backendMock
            ->method('loadItem')
            ->willReturnOnConsecutiveCalls(
                new Item(42, 'Item 42'),
                new Item(43, 'Item 43'),
            );

        $this->browser()
            ->get('/cb/api/test/values?values=42,43')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('items/result');
    }

    public function testLoadItemsByValueWithInvalidValuesList(): void
    {
        $this->browser()
            ->get('/cb/api/test/values?values=')
            ->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'List of values is invalid.');
    }

    public function testLoadItemsByValueWithMissingValuesList(): void
    {
        $this->browser()
            ->get('/cb/api/test/values')
            ->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'List of values is invalid.');
    }
}
