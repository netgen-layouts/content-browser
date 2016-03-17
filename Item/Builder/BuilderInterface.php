<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Builder;

interface BuilderInterface
{
    /**
     * Builds the item from provided value object.
     *
     * @param mixed $valueObject
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function buildItem($valueObject);

    /**
     * Builds the item reference from provided value object.
     *
     * @param mixed $valueObject
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemReferenceInterface
     */
    public function buildItemReference($valueObject);
}
