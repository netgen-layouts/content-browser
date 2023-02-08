<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\ItemLocation;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Location;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Item\LocationInterface;
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
            ->method('loadLocation')
            ->willReturnMap(
                [
                    ['41', $location],
                    [40, new Location(40, 'Location 40')],
                ],
            );

        $this->backendMock
            ->expects(self::any())
            ->method('getSubItemsCount')
            ->with(self::identicalTo($location))
            ->willReturn(2);

        $this->backendMock
            ->expects(self::any())
            ->method('getSubItems')
            ->with(self::identicalTo($location))
            ->willReturn(
                [
                    new Item(42, 'Item 42'),
                    new Item(43, 'Item 43'),
                ],
            );

        $this->client->request('GET', '/cb/api/test/browse/41/items');

        $this->assertResponse(
            $this->client->getResponse(),
            'browse/items',
            Response::HTTP_OK,
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
            ->method('loadLocation')
            ->willReturnMap(
                [
                    ['41', $location],
                    [40, new ItemLocation(40, 'Item 40')],
                ],
            );

        $this->backendMock
            ->expects(self::any())
            ->method('getSubItemsCount')
            ->with(self::identicalTo($location))
            ->willReturn(2);

        $this->backendMock
            ->expects(self::any())
            ->method('getSubItems')
            ->with(self::identicalTo($location))
            ->willReturn(
                [
                    new Item(42, 'Item 42'),
                    new Item(43, 'Item 43'),
                ],
            );

        $this->client->request('GET', '/cb/api/test/browse/41/items');

        $this->assertResponse(
            $this->client->getResponse(),
            'browse/items_as_locations',
            Response::HTTP_OK,
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
            ->method('loadLocation')
            ->willReturnCallback(
                static function ($id) use ($location): LocationInterface {
                    if ($id === '41') {
                        return $location;
                    }

                    throw new NotFoundException();
                },
            );

        $this->backendMock
            ->expects(self::any())
            ->method('getSubItemsCount')
            ->with(self::identicalTo($location))
            ->willReturn(2);

        $this->backendMock
            ->expects(self::any())
            ->method('getSubItems')
            ->with(self::identicalTo($location))
            ->willReturn(
                [
                    new Item(42, 'Item 42'),
                    new Item(43, 'Item 43'),
                ],
            );

        $this->client->request('GET', '/cb/api/test/browse/41/items');

        $this->assertResponse(
            $this->client->getResponse(),
            'browse/items_with_missing_parent',
            Response::HTTP_OK,
        );
    }
}
