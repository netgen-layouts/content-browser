<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

interface ValueInterface
{
    /**
     * Returns the value ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the value name.
     *
     * @return string
     */
    public function getName();
}
