<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

interface LocationInterface
{
    /**
     * Returns the location ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the type.
     *
     * @return int|string
     */
    public function getType();

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the parent ID.
     *
     * @return int|string
     */
    public function getParentId();
}
