<?php

namespace Netgen\Bundle\ContentBrowserBundle\Repository;

interface AdapterInterface
{
    /**
     * Returns all available columns and their names
     *
     * @return array
     */
    public function getColumns();

    /**
     * Loads the location for provided ID.
     *
     * @param int|string $locationId
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If location with provided ID was not found
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Repository\Location
     */
    public function loadLocation($locationId);

    /**
     * Loads all children of the provided location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\Location $location
     * @param string[] $types
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Repository\Location[]
     */
    public function loadLocationChildren(Location $location, array $types = array());

    /**
     * Returns true if provided location has children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\Location $location
     * @param string[] $types
     *
     * @return bool
     */
    public function hasChildren(Location $location, array $types = array());
}
