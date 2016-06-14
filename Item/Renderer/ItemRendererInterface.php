<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Renderer;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

interface ItemRendererInterface
{
    /**
     * Renders the item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     * @param string $template
     *
     * @return string
     */
    public function renderItem(ItemInterface $item, $template);
}
