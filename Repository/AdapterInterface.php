<?php

namespace Netgen\Bundle\ContentBrowserBundle\Repository;

interface AdapterInterface
{
    /**
     * Loads the location for provided ID.
     *
     * @param int|string $locationId
     * @param array $rootLocationIds
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If location with provided ID was not found
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\OutOfBoundsException If location is outside of provided root locations
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface
     */
    public function loadLocation($locationId, $rootLocationIds);

    /**
     * Loads all children of the provided location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface[]
     */
    public function loadLocationChildren(LocationInterface $location);
}
