<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tree;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\OutOfBoundsException;

class Tree implements TreeInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Tree\AdapterInterface
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\AdapterInterface $adapter
     * @param array $config
     */
    public function __construct(
        AdapterInterface $adapter,
        array $config
    ) {
        $this->adapter = $adapter;
        $this->config = $config;
    }

    /**
     * Returns the configured adapter.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Returns the tree config.
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
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Location[]
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
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Location
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
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Location[]
     */
    public function getChildren(Location $location)
    {
        return $this->adapter->loadLocationChildren(
            $location,
            $this->getChildrenTypes()
        );
    }

    /**
     * Returns if current location has children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
     *
     * @return bool
     */
    public function hasChildren(Location $location)
    {
        return $this->adapter->hasChildren(
            $location,
            $this->getChildrenTypes()
        );
    }

    /**
     * Loads all categories below the specified location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Location[]
     */
    public function getSubCategories(Location $location)
    {
        return $this->adapter->loadLocationChildren(
            $location,
            $this->getCategoryTypes()
        );
    }

    /**
     * Returns if current location has child categories.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
     *
     * @return bool
     */
    public function hasSubCategories(Location $location)
    {
        return $this->adapter->hasChildren(
            $location,
            $this->getCategoryTypes()
        );
    }

    /**
     * Returns if provided location is one of the root locations.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
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
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
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

    /**
     * Returns types used to filter the children list
     *
     * @return array
     */
    protected function getChildrenTypes()
    {
        $types = array();
        if (!empty($this->config['children']['types'])) {
            $types = $this->config['children']['types'];

            if (!empty($this->config['children']['include_category_types'])) {
                $types = array_merge($types, $this->getCategoryTypes());
            }
        }

        return $types;
    }

    /**
     * Returns types used to filter the categories list
     *
     * @return array
     */
    protected function getCategoryTypes()
    {
        if (!empty($this->config['categories']['types']))
        {
            return $this->config['categories']['types'];
        }

        return array();
    }
}
