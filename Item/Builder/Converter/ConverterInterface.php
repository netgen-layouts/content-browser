<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Builder\Converter;

use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;

interface ConverterInterface
{
    /**
     * Returns the selectable flag of the value.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     *
     * @return bool
     */
    public function getIsSelectable(ValueInterface $value);
}
