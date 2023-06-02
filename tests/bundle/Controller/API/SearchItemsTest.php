<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use Netgen\ContentBrowser\Backend\SearchResult;
use Symfony\Component\HttpFoundation\Response;

final class SearchItemsTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\SearchItems::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\SearchItems::__invoke
     */
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

        $this->client->request('GET', '/cb/api/test/search?searchText=test');

        $this->assertResponse(
            $this->client->getResponse(),
            'search/result',
            Response::HTTP_OK,
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\SearchItems::__invoke
     */
    public function testSearchItemsWithEmptySearchText(): void
    {
        $this->client->request('GET', '/cb/api/test/search?searchText=');

        $this->assertResponse(
            $this->client->getResponse(),
            'search/empty_result',
            Response::HTTP_OK,
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\SearchItems::__invoke
     */
    public function testSearchItemsWithMissingSearchText(): void
    {
        $this->client->request('GET', '/cb/api/test/search');

        $this->assertResponse(
            $this->client->getResponse(),
            'search/empty_result',
            Response::HTTP_OK,
        );
    }
}
