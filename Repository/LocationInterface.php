<?php

namespace Netgen\Bundle\ContentBrowserBundle\Repository;

interface LocationInterface
{
    /**
     * Returns location ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns location parent ID.
     *
     * @return int|string
     */
    public function getParentId();

    /**
     * Returns location name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns if location can be selected or not.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Returns location thumbnail.
     *
     * @return string
     */
    public function getThumbnail();

    /**
     * Returns location type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns if location is visible.
     *
     * @return bool
     */
    public function isVisible();
}
