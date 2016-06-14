<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Column;

interface ColumnValueProviderInterface
{
    /**
     * Provides the column value.
     *
     * @param mixed $valueObject
     *
     * @return mixed
     */
    public function getValue($valueObject);
}
