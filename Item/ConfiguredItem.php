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
     * Returns the type.
     *
     * @return int|string
     */
    public function getType()
    {
        return $this->item->getType();
    }

    /**
     * Returns the item name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->item->getName();
    }

    /**
     * Returns the item parent ID.
     *
     * @return int|string
     */
    public function getParentId()
    {
        return !in_array($this->item->getId(), $this->config['sections']) ?
            $this->item->getParentId() :
            null;
    }

    /**
     * Returns the value.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ValueInterface
     */
    public function getValue()
    {
        return $this->item->getValue();
    }

    /**
     * Returns if the item is selectable.
     *
     * @return bool
     */
    public function isSelectable()
    {
        if (!$this->item->getValue() instanceof ValueInterface) {
            return false;
        }

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
