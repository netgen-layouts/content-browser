<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Renderer;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;

interface TemplateValueProviderInterface
{
    /**
     * Provides the values for template rendering.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return array
     */
    public function getValues(ItemInterface $item);
}
