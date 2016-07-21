<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Renderer;

use Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRenderer;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Item;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\TemplateValueProvider;
use PHPUnit\Framework\TestCase;
use Twig_Environment;

class ItemRendererTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $twigMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRendererInterface
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
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRenderer::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\ItemRenderer::renderItem
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
