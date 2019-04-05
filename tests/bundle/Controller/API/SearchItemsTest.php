<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use Symfony\Component\HttpFoundation\Response;

final class SearchItemsTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\Controller::initialize
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\SearchItems::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\SearchItems::__invoke
     */
    public function testSearchItems(): void
    {
        $this->backendMock
            ->expects(self::any())
            ->method('search')
            ->willReturn(
                [
                    new Item(42, 'Item 42'),
                    new Item(43, 'Item 43'),
                ]
            );

        $this->backendMock
            ->expects(self::any())
            ->method('searchCount')
            ->willReturn(2);

        $this->client->request('GET', '/cb/api/v1/test/search?searchText=test');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/search/result',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\SearchItems::__invoke
     */
    public function testSearchItemsWithEmptySearchText(): void
    {
        $this->client->request('GET', '/cb/api/v1/test/search?searchText=');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/search/empty_result',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\SearchItems::__invoke
     */
    public function testSearchItemsWithMissingSearchText(): void
    {
        $this->client->request('GET', '/cb/api/v1/test/search');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/search/empty_result',
            Response::HTTP_OK
        );
    }
}
