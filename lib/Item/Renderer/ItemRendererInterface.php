<?php

namespace Netgen\ContentBrowser\Item\Renderer;

use Netgen\ContentBrowser\Item\ItemInterface;

interface ItemRendererInterface
{
    /**
     * Renders the item.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     * @param string $template
     *
     * @return string
     */
    public function renderItem(ItemInterface $item, $template);
}
