<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Configurator;

use Netgen\Bundle\ContentBrowserBundle\Item\Configurator\ItemConfigurator;
use Netgen\Bundle\ContentBrowserBundle\Item\ConfiguredItemInterface;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\ConfiguratorHandler;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Item;
use PHPUnit\Framework\TestCase;

class ItemConfiguratorTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Configurator\ItemConfigurator
     */
    protected $configurator;

    public function setUp()
    {
        $this->configurator = new ItemConfigurator(
            array('config' => 'value'),
            array('value' => new ConfiguratorHandler())
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Configurator\ItemConfigurator::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Item\Configurator\ItemConfigurator::configureItem
     */
    public function testConfigureItem()
    {
        self::assertInstanceOf(
            ConfiguredItemInterface::class,
            $this->configurator->configureItem(new Item(12))
        );
    }
}
