<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\ItemLocation;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Location;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Symfony\Component\HttpFoundation\Response;

final class LoadSubItemsTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadSubItems::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadSubItems::__invoke
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadSubItems::buildPath
     */
    public function testLoadSubItems(): void
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('loadLocation')
            ->with($this->equalTo(41))
            ->will($this->returnValue(new Location(41, 'Location 41', 40)));

        $this->backendMock
            ->expects($this->at(1))
            ->method('loadLocation')
            ->with($this->equalTo(40))
            ->will($this->returnValue(new Location(40, 'Location 40')));

        $this->backendMock
            ->expects($this->any())
            ->method('getSubItemsCount')
            ->with($this->equalTo(new Location(41, 'Location 41', 40)))
            ->will($this->returnValue(2));

        $this->backendMock
            ->expects($this->any())
            ->method('getSubItems')
            ->with($this->equalTo(new Location(41, 'Location 41', 40)))
            ->will(
                $this->returnValue(
                    [
                        new Item(42, 'Item 42'),
                        new Item(43, 'Item 43'),
                    ]
                )
            );

        $this->client->request('GET', '/cb/api/v1/test/browse/41/items');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/browse/items',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadSubItems::__invoke
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadSubItems::buildPath
     */
    public function testLoadSubItemsWithItemAsLocation(): void
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('loadLocation')
            ->with($this->equalTo(41))
            ->will($this->returnValue(new ItemLocation(41, 'Item 41', 40)));

        $this->backendMock
            ->expects($this->at(1))
            ->method('loadLocation')
            ->with($this->equalTo(40))
            ->will($this->returnValue(new ItemLocation(40, 'Item 40')));

        $this->backendMock
            ->expects($this->any())
            ->method('getSubItemsCount')
            ->with($this->equalTo(new ItemLocation(41, 'Item 41', 40)))
            ->will($this->returnValue(2));

        $this->backendMock
            ->expects($this->any())
            ->method('getSubItems')
            ->with($this->equalTo(new ItemLocation(41, 'Item 41', 40)))
            ->will(
                $this->returnValue(
                    [
                        new Item(42, 'Item 42'),
                        new Item(43, 'Item 43'),
                    ]
                )
            );

        $this->client->request('GET', '/cb/api/v1/test/browse/41/items');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/browse/items_as_locations',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadSubItems::__invoke
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadSubItems::buildPath
     */
    public function testLoadSubItemsWithMissingParent(): void
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('loadLocation')
            ->with($this->equalTo(41))
            ->will($this->returnValue(new ItemLocation(41, 'Item 41', 40)));

        $this->backendMock
            ->expects($this->at(1))
            ->method('loadLocation')
            ->with($this->equalTo(40))
            ->will($this->throwException(new NotFoundException()));

        $this->backendMock
            ->expects($this->any())
            ->method('getSubItemsCount')
            ->with($this->equalTo(new ItemLocation(41, 'Item 41', 40)))
            ->will($this->returnValue(2));

        $this->backendMock
            ->expects($this->any())
            ->method('getSubItems')
            ->with($this->equalTo(new ItemLocation(41, 'Item 41', 40)))
            ->will(
                $this->returnValue(
                    [
                        new Item(42, 'Item 42'),
                        new Item(43, 'Item 43'),
                    ]
                )
            );

        $this->client->request('GET', '/cb/api/v1/test/browse/41/items');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/browse/items_with_missing_parent',
            Response::HTTP_OK
        );
    }
}
