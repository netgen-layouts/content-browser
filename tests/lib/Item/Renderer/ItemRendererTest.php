<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Item\Renderer;

use Exception;
use Netgen\ContentBrowser\Item\Renderer\ItemRenderer;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

#[CoversClass(ItemRenderer::class)]
final class ItemRendererTest extends TestCase
{
    private Stub&Environment $twigStub;

    private ItemRenderer $itemRenderer;

    protected function setUp(): void
    {
        $this->twigStub = self::createStub(Environment::class);

        $this->itemRenderer = new ItemRenderer($this->twigStub);
    }

    public function testRenderItem(): void
    {
        $item = new Item(42);

        $this->twigStub
            ->method('render')
            ->with(
                self::identicalTo('template.html.twig'),
                self::identicalTo(['item' => $item]),
            )
            ->willReturn('rendered item');

        self::assertSame(
            'rendered item',
            $this->itemRenderer->renderItem($item, 'template.html.twig'),
        );
    }

    public function testRenderItemWithException(): void
    {
        $item = new Item(42);

        $this->twigStub
            ->method('render')
            ->with(
                self::identicalTo('template.html.twig'),
                self::identicalTo(['item' => $item]),
            )
            ->willThrowException(new Exception());

        self::assertSame('', $this->itemRenderer->renderItem($item, 'template.html.twig'));
    }
}
