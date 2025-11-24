<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Controller\API\RenderItem;
use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\Item;
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
            ->get('/cb/api/test/render/42')
            ->assertHtml()
            ->assertStatus(Response::HTTP_OK)
            ->assertContentIs('rendered item');
    }

    public function testRenderItemWithDisabledPreview(): void
    {
        $this->browser()
            ->get('/cb/api/test_preview_disabled/render/42')
            ->assertHtml()
            ->assertStatus(Response::HTTP_OK)
            ->assertContentIs('');
    }
}
