<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Builder;

interface BuilderInterface
{
    public function buildItem($itemType, $valueObject);

    public function buildItemReference($itemType, $valueObject);
}
