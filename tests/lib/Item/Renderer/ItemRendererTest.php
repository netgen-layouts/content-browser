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

    public function setUp()
    {
        $this->twigMock = $this->createMock(Environment::class);

        $this->itemRenderer = new ItemRenderer($this->twigMock);
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Renderer\ItemRenderer::__construct
     * @covers \Netgen\ContentBrowser\Item\Renderer\ItemRenderer::renderItem
     */
    public function testRenderItem()
    {
        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('template.html.twig'),
                $this->equalTo(['item' => new Item()])
            )
            ->will($this->returnValue('rendered item'));

        $this->assertEquals(
            'rendered item',
            $this->itemRenderer->renderItem(new Item(), 'template.html.twig')
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\Renderer\ItemRenderer::renderItem
     */
    public function testRenderItemWithException()
    {
        $this->twigMock
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('template.html.twig'),
                $this->equalTo(['item' => new Item()])
            )
            ->will($this->throwException(new Exception()));

        $this->assertEquals('', $this->itemRenderer->renderItem(new Item(), 'template.html.twig'));
    }
}
