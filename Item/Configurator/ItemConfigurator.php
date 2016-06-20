<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Configurator;

use Netgen\Bundle\ContentBrowserBundle\Item\ConfiguredItem;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

class ItemConfigurator implements ItemConfiguratorInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Configurator\Handler\ConfiguratorHandlerInterface[]
     */
    protected $handlers = array();

    /**
     * Constructor.
     *
     * @param array $config
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Configurator\Handler\ConfiguratorHandlerInterface[] $handlers
     */
    public function __construct(
        array $config,
        array $handlers = array()
    ) {
        $this->config = $config;
        $this->handlers = $handlers;
    }

    /**
     * Configures the item based on current config.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ConfiguredItemInterface
     */
    public function configureItem(ItemInterface $item)
    {
        $handler = $this->handlers[$item->getType()];

        return new ConfiguredItem($item, $handler, $this->config);
    }
}
