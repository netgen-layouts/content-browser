<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Renderer;

use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;

interface TemplateValueProviderInterface
{
    /**
     * Provides the values for template rendering.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     *
     * @return array
     */
    public function getValues(ValueInterface $value);
}
