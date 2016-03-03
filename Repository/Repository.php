<?php

namespace Netgen\Bundle\ContentBrowserBundle\Repository;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\OutOfBoundsException;

class Repository implements RepositoryInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Repository\AdapterInterface
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Sets the repository config.
     *
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * Returns the repository config.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns all root locations.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Repository\Location[]
     */
    public function getRootLocations()
    {
        $rootLocations = array();

        foreach ($this->config['root_locations'] as $rootLocation) {
            $rootLocations[] = $this->getLocation($rootLocation);
        }

        return $rootLocations;
    }

    /**
     * Loads the location for specified ID.
     *
     * @param int|string $locationId
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\OutOfBoundsException If location is outside of root locations
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Repository\Location
     */
    public function getLocation($locationId)
    {
        $location = $this->adapter->loadLocation($locationId);

        if (!$this->isInsideRootLocations($location)) {
            throw new OutOfBoundsException("Location #{$locationId} is not inside root locations.");
        }

        return $location;
    }

    /**
     * Loads all children of the specified location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\Location $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Repository\Location[]
     */
    public function getChildren(Location $location)
    {
        $types = array();
        if (!empty($this->config['children']['types'])) {
            $types = $this->config['children']['types'];
            if (isset($this->config['children']['include_category_types']) && $this->config['children']['include_category_types']) {
                $types = array_merge($types, $this->config['categories']['types']);
            }
        }

        return $this->adapter->loadLocationChildren($location, $types);
    }

    /**
     * Returns if current location has children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\Location $location
     *
     * @return bool
     */
    public function hasChildren(Location $location)
    {
        $types = array();
        if (!empty($this->config['children']['types'])) {
            $types = $this->config['children']['types'];
            if (isset($this->config['children']['include_category_types']) && $this->config['children']['include_category_types']) {
                $types = array_merge($types, $this->config['categories']['types']);
            }
        }

        return $this->adapter->hasChildren($location, $types);
    }

    /**
     * Loads all categories below the specified location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\Location $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Repository\Location[]
     */
    public function getCategories(Location $location)
    {
        return $this->adapter->loadLocationChildren(
            $location,
            $this->config['categories']['types']
        );
    }

    /**
     * Returns if current location has child categories.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\Location $location
     *
     * @return bool
     */
    public function hasChildrenCategories(Location $location)
    {
        return $this->adapter->hasChildren(
            $location,
            $this->config['categories']['types']
        );
    }

    /**
     * Returns if provided location is one of the root locations.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\Location $location
     *
     * @return bool
     */
    public function isRootLocation(Location $location)
    {
        foreach ($this->config['root_locations'] as $rootLocation) {
            if ($location->id == $rootLocation) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns if provided location is inside one of the root locations.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\Location $location
     *
     * @return bool
     */
    public function isInsideRootLocations(Location $location)
    {
        foreach ($this->config['root_locations'] as $rootLocation) {
            if (in_array($rootLocation, $location->path)) {
                return true;
            }
        }

        return false;
    }
}
