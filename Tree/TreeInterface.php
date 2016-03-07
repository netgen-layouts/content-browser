<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tree;

interface TreeInterface
{
    /**
     * Returns the configured adapter.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\AdapterInterface
     */
    public function getAdapter();

    /**
     * Returns the tree config.
     *
     * @return array
     */
    public function getConfig();

    /**
     * Returns all root locations.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Location[]
     */
    public function getRootLocations();

    /**
     * Loads the location for specified ID.
     *
     * @param int|string $locationId
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\OutOfBoundsException If location is outside of root locations
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Location
     */
    public function getLocation($locationId);

    /**
     * Loads all children of the specified location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Location[]
     */
    public function getChildren(Location $location);

    /**
     * Returns if current location has children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
     *
     * @return bool
     */
    public function hasChildren(Location $location);

    /**
     * Loads all categories below the specified location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Location[]
     */
    public function getSubCategories(Location $location);

    /**
     * Returns if current location has child categories.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
     *
     * @return bool
     */
    public function hasSubCategories(Location $location);

    /**
     * Returns if provided location is one of the root locations.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
     *
     * @return bool
     */
    public function isRootLocation(Location $location);

    /**
     * Returns if provided location is inside one of the root locations.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
     *
     * @return bool
     */
    public function isInsideRootLocations(Location $location);
}
