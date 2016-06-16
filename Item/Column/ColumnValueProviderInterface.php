<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Column;

use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;

interface ColumnValueProviderInterface
{
    /**
     * Provides the column value.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     *
     * @return mixed
     */
    public function getValue(ValueInterface $value);
}
