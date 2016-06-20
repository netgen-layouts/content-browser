<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

interface CategoryInterface
{
    /**
     * Returns the category ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the category name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the category parent ID.
     *
     * @return int|string
     */
    public function getParentId();
}
