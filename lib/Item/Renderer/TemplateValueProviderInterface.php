<?php

namespace Netgen\ContentBrowser\Item\Renderer;

use Netgen\ContentBrowser\Item\ItemInterface;

interface TemplateValueProviderInterface
{
    /**
     * Provides the values for template rendering.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return array
     */
    public function getValues(ItemInterface $item);
}
