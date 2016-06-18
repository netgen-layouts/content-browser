<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Stubs;

use Netgen\Bundle\ContentBrowserBundle\Item\Builder\Converter\ConverterInterface;
use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;

class ConverterStub implements ConverterInterface
{
    /**
     * Returns the selectable flag of the value.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     *
     * @return bool
     */
    public function getIsSelectable(ValueInterface $value)
    {
        return true;
    }
}
