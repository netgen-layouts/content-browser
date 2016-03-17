<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Builder;

interface BuilderInterface
{
    public function buildItem($valueObject);

    public function buildItemReference($valueObject);
}
