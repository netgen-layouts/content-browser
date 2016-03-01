<?php

namespace Netgen\Bundle\ContentBrowserBundle\Repository;

interface RepositoryInterface
{
    /**
     * Sets the repository config.
     *
     * @param array $config
     */
    public function setConfig(array $config);

    /**
     * Returns the repository config.
     *
     * @return array
     */
    public function getConfig();

    /**
     * Returns all root locations.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface[]
     */
    public function getRootLocations();

    /**
     * Loads the location for specified ID.
     *
     * @param int|string $locationId
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface
     */
    public function getLocation($locationId);

    /**
     * Loads all children of the specified location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface[]
     */
    public function getChildren(LocationInterface $location);

    /**
     * Returns if current location has children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface $location
     *
     * @return bool
     */
    public function hasChildren(LocationInterface $location);

    /**
     * Loads all categories below the specified location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface[]
     */
    public function getCategories(LocationInterface $location);

    /**
     * Returns if current location has child categories.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface $location
     *
     * @return bool
     */
    public function hasChildrenCategories(LocationInterface $location);

    /**
     * Returns if provided location is one of the root locations.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface $location
     *
     * @return bool
     */
    public function isRootLocation(LocationInterface $location);
}
