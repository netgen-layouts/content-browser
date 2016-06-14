<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Builder;

use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;

interface ItemBuilderInterface
{
    /**
     * Builds the item from provided value.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function buildItem(ValueInterface $value);

    /**
     * Builds the item reference from provided value.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemReferenceInterface
     */
    public function buildItemReference(ValueInterface $value);
}
