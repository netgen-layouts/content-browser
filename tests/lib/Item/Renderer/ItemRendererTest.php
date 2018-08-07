<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Item\Renderer;

use Exception;
use Netgen\ContentBrowser\Item\Renderer\ItemRenderer;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class ItemRendererTest extends TestCase
{
    /**
     * @var \Twig\Environment&\PHPUnit\Framework\MockObject\MockObject
     */
    private $twigMock;

    /**
     * @var \Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface
     */
    private $itemRenderer;

    public function setUp(): void
    {
        $this->twigMock = $this->createMock(Environment::class);

        $this->itemRenderer = new ItemRenderer($this->twigMock);
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Renderer\ItemRenderer::__construct
     * @covers \Netgen\ContentBrowser\Item\Renderer\ItemRenderer::renderItem
     */
    public function testRenderItem(): void
    {
        $item = new Item();

        $this->twigMock
            ->expects(self::once())
            ->method('render')
            ->with(
                self::identicalTo('template.html.twig'),
                self::identicalTo(['item' => $item])
            )
            ->will(self::returnValue('rendered item'));

        self::assertSame(
            'rendered item',
            $this->itemRenderer->renderItem($item, 'template.html.twig')
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Renderer\ItemRenderer::renderItem
     */
    public function testRenderItemWithException(): void
    {
        $item = new Item();

        $this->twigMock
            ->expects(self::once())
            ->method('render')
            ->with(
                self::identicalTo('template.html.twig'),
                self::identicalTo(['item' => $item])
            )
            ->will(self::throwException(new Exception()));

        self::assertSame('', $this->itemRenderer->renderItem($item, 'template.html.twig'));
    }
}
