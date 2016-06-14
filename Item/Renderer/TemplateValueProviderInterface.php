<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Renderer;

interface TemplateValueProviderInterface
{
    /**
     * Provides the values for template rendering.
     *
     * @param mixed $valueObject
     *
     * @return array
     */
    public function getValues($valueObject);
}
