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
        $location = new Location(41, 'Location 41', 40);

        $this->backendMock
            ->expects(self::at(0))
            ->method('loadLocation')
            ->with(self::identicalTo('41'))
            ->will(self::returnValue($location));

        $this->backendMock
            ->expects(self::at(1))
            ->method('loadLocation')
            ->with(self::identicalTo(40))
            ->will(self::returnValue(new Location(40, 'Location 40')));

        $this->backendMock
            ->expects(self::any())
            ->method('getSubItemsCount')
            ->with(self::identicalTo($location))
            ->will(self::returnValue(2));

        $this->backendMock
            ->expects(self::any())
            ->method('getSubItems')
            ->with(self::identicalTo($location))
            ->will(
                self::returnValue(
                    [
                        new Item(42, 'Item 42'),
                        new Item(43, 'Item 43'),
                    ]
                )
            );

        $this->client->request('GET', '/cb/api/v1/test/browse/41/items');

        self::assertResponse(
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
        $location = new ItemLocation(41, 'Item 41', 40);

        $this->backendMock
            ->expects(self::at(0))
            ->method('loadLocation')
            ->with(self::identicalTo('41'))
            ->will(self::returnValue($location));

        $this->backendMock
            ->expects(self::at(1))
            ->method('loadLocation')
            ->with(self::identicalTo(40))
            ->will(self::returnValue(new ItemLocation(40, 'Item 40')));

        $this->backendMock
            ->expects(self::any())
            ->method('getSubItemsCount')
            ->with(self::identicalTo($location))
            ->will(self::returnValue(2));

        $this->backendMock
            ->expects(self::any())
            ->method('getSubItems')
            ->with(self::identicalTo($location))
            ->will(
                self::returnValue(
                    [
                        new Item(42, 'Item 42'),
                        new Item(43, 'Item 43'),
                    ]
                )
            );

        $this->client->request('GET', '/cb/api/v1/test/browse/41/items');

        self::assertResponse(
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
        $location = new ItemLocation(41, 'Item 41', 40);

        $this->backendMock
            ->expects(self::at(0))
            ->method('loadLocation')
            ->with(self::identicalTo('41'))
            ->will(self::returnValue($location));

        $this->backendMock
            ->expects(self::at(1))
            ->method('loadLocation')
            ->with(self::identicalTo(40))
            ->will(self::throwException(new NotFoundException()));

        $this->backendMock
            ->expects(self::any())
            ->method('getSubItemsCount')
            ->with(self::identicalTo($location))
            ->will(self::returnValue(2));

        $this->backendMock
            ->expects(self::any())
            ->method('getSubItems')
            ->with(self::identicalTo($location))
            ->will(
                self::returnValue(
                    [
                        new Item(42, 'Item 42'),
                        new Item(43, 'Item 43'),
                    ]
                )
            );

        $this->client->request('GET', '/cb/api/v1/test/browse/41/items');

        self::assertResponse(
            $this->client->getResponse(),
            'v1/browse/items_with_missing_parent',
            Response::HTTP_OK
        );
    }
}
