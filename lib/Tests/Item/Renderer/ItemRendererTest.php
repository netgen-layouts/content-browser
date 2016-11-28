<?php

namespace Netgen\ContentBrowser\Tests\Item\Renderer;

use Netgen\ContentBrowser\Item\Renderer\ItemRenderer;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use Netgen\ContentBrowser\Tests\Stubs\TemplateValueProvider;
use PHPUnit\Framework\TestCase;
use Twig_Environment;

class ItemRendererTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $twigMock;

    /**
     * @var \Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface
     */
    protected $itemRenderer;

    public function setUp()
    {
        $this->twigMock = $this->createMock(Twig_Environment::class);

        $this->itemRenderer = new ItemRenderer(
            $this->twigMock,
            array('value' => new TemplateValueProvider())
        );
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
                $this->equalTo(array('item' => new Item()))
            )
            ->will($this->returnValue('rendered item'));

        $this->assertEquals(
            'rendered item',
            $this->itemRenderer->renderItem(new Item(), 'template.html.twig')
        );
    }
}
