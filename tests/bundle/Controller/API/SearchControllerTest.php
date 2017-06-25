<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use Symfony\Component\HttpFoundation\Response;

class SearchControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\Controller::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\Controller::initialize
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\SearchController::search
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\Controller::buildPager
     */
    public function testSearch()
    {
        $this->backendMock
            ->expects($this->any())
            ->method('search')
            ->will(
                $this->returnValue(
                    array(
                        new Item(42, 'Item 42'),
                        new Item(43, 'Item 43'),
                    )
                )
            );

        $this->backendMock
            ->expects($this->any())
            ->method('searchCount')
            ->will(
                $this->returnValue(2)
            );

        $this->client->request('GET', '/cb/api/v1/test/search?searchText=test');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/search/result',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\SearchController::search
     */
    public function testSearchWithEmptySearchText()
    {
        $this->client->request('GET', '/cb/api/v1/test/search?searchText=');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'Search text cannot be empty.'
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\SearchController::search
     */
    public function testSearchWithMissingSearchText()
    {
        $this->client->request('GET', '/cb/api/v1/test/search');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'Search text cannot be empty.'
        );
    }
}
