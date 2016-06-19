<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

interface ColumnValueProviderInterface
{
    /**
     * Provides the column value.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return mixed
     */
    public function getValue(ItemInterface $item);
}
