<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

use Netgen\Bundle\ContentBrowserBundle\Item\Configurator\Handler\ConfiguratorHandlerInterface;

class ConfiguredItem implements ConfiguredItemInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    protected $item;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Configurator\Handler\ConfiguratorHandlerInterface
     */
    protected $handler;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Configurator\Handler\ConfiguratorHandlerInterface $handler
     * @param array $config
     */
    public function __construct(ItemInterface $item, ConfiguratorHandlerInterface $handler, array $config)
    {
        $this->item = $item;
        $this->handler = $handler;
        $this->config = $config;
    }

    /**
     * Returns if the item is selectable.
     *
     * @return bool
     */
    public function isSelectable()
    {
        return $this->handler->isSelectable($this->item, $this->config);
    }

    /**
     * Returns the item template.
     *
     * @return bool
     */
    public function getTemplate()
    {
        return $this->config['template'];
    }
}
