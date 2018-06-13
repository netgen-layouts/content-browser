<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\Renderer;

use Netgen\ContentBrowser\Item\ItemInterface;

interface ItemRendererInterface
{
    /**
     * Renders the item. In case the rendering error is occurred, an empty string is returned.
     */
    public function renderItem(ItemInterface $item, string $template): string;
}
