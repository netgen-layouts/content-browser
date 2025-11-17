<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Controller\API\SearchItems;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use Netgen\ContentBrowser\Backend\SearchResult;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(SearchItems::class)]
final class SearchItemsTest extends ApiTestCase
{
    public function testSearchItems(): void
    {
        $this->backendMock
            ->method('searchItems')
            ->willReturn(
                new SearchResult(
                    [
                        new Item(42, 'Item 42'),
                        new Item(43, 'Item 43'),
                    ],
                ),
            );

        $this->backendMock
            ->method('searchItemsCount')
            ->willReturn(2);

        $this->browser()
            ->get('/cb/api/test/search?searchText=test')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('search/result');
    }

    public function testSearchItemsWithEmptySearchText(): void
    {
        $this->browser()
            ->get('/cb/api/test/search?searchText=')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('search/empty_result');
    }

    public function testSearchItemsWithMissingSearchText(): void
    {
        $this->browser()
            ->get('/cb/api/test/search')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('search/empty_result');
    }
}
