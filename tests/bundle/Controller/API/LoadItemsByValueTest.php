<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use Symfony\Component\HttpFoundation\Response;

final class LoadItemsByValueTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadItemsByValue::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadItemsByValue::__invoke
     */
    public function testLoadItemsByValue(): void
    {
        $this->backendMock
            ->expects(self::at(0))
            ->method('loadItem')
            ->will(self::returnValue(new Item(42, 'Item 42')));

        $this->backendMock
            ->expects(self::at(1))
            ->method('loadItem')
            ->will(self::returnValue(new Item(43, 'Item 43')));

        $this->client->request('GET', '/cb/api/v1/test/values?values=42,43');

        self::assertResponse(
            $this->client->getResponse(),
            'v1/items/result',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadItemsByValue::__invoke
     */
    public function testLoadItemsByValueWithInvalidValuesList(): void
    {
        $this->client->request('GET', '/cb/api/v1/test/values?values=');

        self::assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'List of values is invalid.'
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\LoadItemsByValue::__invoke
     */
    public function testLoadItemsByValueWithMissingValuesList(): void
    {
        $this->client->request('GET', '/cb/api/v1/test/values');

        self::assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'List of values is invalid.'
        );
    }
}
