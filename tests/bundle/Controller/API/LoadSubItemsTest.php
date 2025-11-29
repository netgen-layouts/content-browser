<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadSubItems;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\ItemLocation;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Location;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Item\LocationInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadSubItems::class)]
final class LoadSubItemsTest extends ApiTestCase
{
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
            ->method('getSubItemsCount')
            ->with(self::identicalTo($location))
            ->willReturn(2);

        $this->backendMock
            ->method('getSubItems')
            ->with(self::identicalTo($location))
            ->willReturn(
                [
                    new Item(42, 'Item 42'),
                    new Item(43, 'Item 43'),
                ],
            );

        $this->browser()
            ->get('/cb/api/test/browse/41/items')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('browse/items');
    }

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
            ->method('getSubItemsCount')
            ->with(self::identicalTo($location))
            ->willReturn(2);

        $this->backendMock
            ->method('getSubItems')
            ->with(self::identicalTo($location))
            ->willReturn(
                [
                    new Item(42, 'Item 42'),
                    new Item(43, 'Item 43'),
                ],
            );

        $this->browser()
            ->get('/cb/api/test/browse/41/items')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('browse/items_as_locations');
    }

    public function testLoadSubItemsWithMissingParent(): void
    {
        $location = new ItemLocation(41, 'Item 41', 40);

        $this->backendMock
            ->method('loadLocation')
            ->willReturnCallback(
                static function (int|string $id) use ($location): LocationInterface {
                    if ($id === '41') {
                        return $location;
                    }

                    throw new NotFoundException('Location not found.');
                },
            );

        $this->backendMock
            ->method('getSubItemsCount')
            ->with(self::identicalTo($location))
            ->willReturn(2);

        $this->backendMock
            ->method('getSubItems')
            ->with(self::identicalTo($location))
            ->willReturn(
                [
                    new Item(42, 'Item 42'),
                    new Item(43, 'Item 43'),
                ],
            );

        $this->browser()
            ->get('/cb/api/test/browse/41/items')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('browse/items_with_missing_parent');
    }
}
