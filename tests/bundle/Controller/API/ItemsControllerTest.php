<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Symfony\Component\HttpFoundation\Response;

final class ItemsControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\ItemController::renderItem
     */
    public function testRenderItem()
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Item(42, 'Item 42')));

        $this->client->request('GET', '/cb/api/v1/test/render/42');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);
        $this->assertHeader($response, 'text/html');
        $this->assertEquals('rendered item', $response->getContent());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\ItemController::renderItem
     */
    public function testRenderItemWithDisabledPreview()
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Item(42, 'Item 42')));

        $this->clientContainer->set(
            'netgen_content_browser.config.test',
            new Configuration(
                'test',
                array(
                    'columns' => array(
                        'name' => array(
                            'name' => 'columns.name',
                            'value_provider' => 'name',
                        ),
                    ),
                    'default_columns' => array('name'),
                    'preview' => array(
                        'enabled' => false,
                    ),
                )
            )
        );

        $this->client->request('GET', '/cb/api/v1/test/render/42');

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);
        $this->assertHeader($response, 'text/html');
        $this->assertEquals('', $response->getContent());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\ItemController::getByValues
     */
    public function testRenderItemWithNonExistingItem()
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('Item does not exist.')));

        $this->client->request('GET', '/cb/api/v1/test/render/42');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Item does not exist.'
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\ItemController::getByValues
     */
    public function testGetByValues()
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->will($this->returnValue(new Item(42, 'Item 42')));

        $this->backendMock
            ->expects($this->at(1))
            ->method('loadItem')
            ->will($this->returnValue(new Item(43, 'Item 43')));

        $this->client->request('GET', '/cb/api/v1/test/values?values=42,43');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/items/result',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\ItemController::getByValues
     */
    public function testGetByValuesWithInvalidValuesList()
    {
        $this->client->request('GET', '/cb/api/v1/test/values?values=');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'List of values is invalid.'
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Controller\API\ItemController::getByValues
     */
    public function testGetByValuesWithMissingValuesList()
    {
        $this->client->request('GET', '/cb/api/v1/test/values');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'List of values is invalid.'
        );
    }
}
