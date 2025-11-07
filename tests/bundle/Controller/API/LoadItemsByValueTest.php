<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadItemsByValue;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadItemsByValue::class)]
final class LoadItemsByValueTest extends JsonApiTestCase
{
    public function testLoadItemsByValue(): void
    {
        $this->backendMock
            ->method('loadItem')
            ->willReturnOnConsecutiveCalls(
                new Item(42, 'Item 42'),
                new Item(43, 'Item 43'),
            );

        $this->client->request('GET', '/cb/api/test/values?values=42,43');

        $this->assertResponse(
            $this->client->getResponse(),
            'items/result',
            Response::HTTP_OK,
        );
    }

    public function testLoadItemsByValueWithInvalidValuesList(): void
    {
        $this->client->request('GET', '/cb/api/test/values?values=');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'List of values is invalid.',
        );
    }

    public function testLoadItemsByValueWithMissingValuesList(): void
    {
        $this->client->request('GET', '/cb/api/test/values');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'List of values is invalid.',
        );
    }
}
