<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Stubs;

use Netgen\Bundle\ContentBrowserBundle\Item\Builder\Converter\ConverterInterface;
use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;

class ConverterStub implements ConverterInterface
{
    /**
     * Returns the value type this converter supports.
     *
     * @return string
     */
    public function getValueType()
    {
        return 'value';
    }

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
