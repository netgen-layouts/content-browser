<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\ItemLocation;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Location;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Symfony\Component\HttpFoundation\Response;

final class BrowseControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\BrowseController::getSubLocations
     */
    public function testGetSubLocations()
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('loadLocation')
            ->with($this->equalTo(41))
            ->will($this->returnValue(new Location(41, 'Location 41')));

        $this->backendMock
            ->expects($this->at(1))
            ->method('getSubLocations')
            ->with($this->equalTo(new Location(41, 'Location 41')))
            ->will(
                $this->returnValue(
                    array(
                        new Location(42, 'Location 42'),
                        new Location(43, 'Location 43'),
                    )
                )
            );

        $this->client->request('GET', '/cb/api/v1/test/browse/41/locations');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/browse/locations',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\BrowseController::buildPager
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\BrowseController::buildPath
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\BrowseController::getSubItems
     */
    public function testGetSubItems()
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
                    array(
                        new Item(42, 'Item 42'),
                        new Item(43, 'Item 43'),
                    )
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\BrowseController::buildPager
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\BrowseController::buildPath
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\BrowseController::getSubItems
     */
    public function testGetSubItemsWithItemAsLocation()
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
                    array(
                        new Item(42, 'Item 42'),
                        new Item(43, 'Item 43'),
                    )
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\BrowseController::buildPager
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\BrowseController::buildPath
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\BrowseController::getSubItems
     */
    public function testGetSubItemsWithMissingParent()
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
                    array(
                        new Item(42, 'Item 42'),
                        new Item(43, 'Item 43'),
                    )
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
