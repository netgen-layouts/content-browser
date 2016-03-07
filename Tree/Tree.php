<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tree;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\OutOfBoundsException;
use Symfony\Component\Translation\TranslatorInterface;

class Tree implements TreeInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Tree\AdapterInterface
     */
    protected $adapter;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\AdapterInterface $adapter
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param array $config
     */
    public function __construct(
        AdapterInterface $adapter,
        TranslatorInterface $translator,
        array $config
    ) {
        $this->adapter = $adapter;
        $this->translator = $translator;
        $this->config = $config;
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
     * Returns the available columns.
     *
     * @return array
     */
    public function getAvailableColumns()
    {
        return array(
            'id' => $this->translator->trans('netgen_content_browser.columns.id'),
            'parent_id' => $this->translator->trans('netgen_content_browser.columns.parent_id'),
            'name' => $this->translator->trans('netgen_content_browser.columns.name'),
        ) + $this->adapter->getColumns();
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
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
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
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Tree\Location[]
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
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
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
}
