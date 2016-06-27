<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item;

use Netgen\Bundle\ContentBrowserBundle\Item\ConfiguredItem;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\ConfiguratorHandler;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Item;
use PHPUnit\Framework\TestCase;

class ConfiguredItemTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ConfiguredItem
     */
    protected $configuredItem;

    public function setUp()
    {
        $this->configuredItem = new ConfiguredItem(
            new Item(),
            new ConfiguratorHandler(),
            array('template' => 'template.html.twig')
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ConfiguredItem::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ConfiguredItem::isSelectable
     */
    public function testIsSelectable()
    {
        self::assertTrue($this->configuredItem->isSelectable());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\ConfiguredItem::getTemplate
     */
    public function testGetTemplate()
    {
        self::assertEquals('template.html.twig', $this->configuredItem->getTemplate());
    }
}
