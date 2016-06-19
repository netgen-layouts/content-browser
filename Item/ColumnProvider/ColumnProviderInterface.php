<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\ColumnProvider;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

interface ColumnProviderInterface
{
    /**
     * Provides the columns for selected item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return array
     */
    public function provideColumns(ItemInterface $item);
}
