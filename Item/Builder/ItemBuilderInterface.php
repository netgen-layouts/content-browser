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
}
