<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Controller\API\RenderItem;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
use Netgen\ContentBrowser\Config\Configuration;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(RenderItem::class)]
final class RenderItemTest extends ApiTestCase
{
    public function testRenderItem(): void
    {
        $this->backendMock
            ->method('loadItem')
            ->with(self::identicalTo('42'))
            ->willReturn(new Item(42, 'Item 42'));

        $this->browser()
            ->withConfig(
                new Configuration(
                    'test',
                    'Test',
                    [
                        'columns' => [
                            'name' => [
                                'name' => 'columns.name',
                                'value_provider' => 'name',
                            ],
                        ],
                        'default_columns' => ['name'],
                        'preview' => [
                            'enabled' => true,
                            'template' => 'template.html.twig',
                        ],
                    ],
                ),
            )
            ->get('/cb/api/test/render/42')
            ->assertHtml()
            ->assertStatus(Response::HTTP_OK)
            ->assertContentIs('rendered item');
    }

    public function testRenderItemWithDisabledPreview(): void
    {
        $this->browser()
            ->withConfig(
                new Configuration(
                    'test',
                    'Test',
                    [
                        'columns' => [
                            'name' => [
                                'name' => 'columns.name',
                                'value_provider' => 'name',
                            ],
                        ],
                        'default_columns' => ['name'],
                        'preview' => [
                            'enabled' => false,
                        ],
                    ],
                ),
            )
            ->get('/cb/api/test/render/42')
            ->assertHtml()
            ->assertStatus(Response::HTTP_OK)
            ->assertContentIs('');
    }
}
