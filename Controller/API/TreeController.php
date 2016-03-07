<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
use Netgen\Bundle\ContentBrowserBundle\Tree\TreeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Netgen\Bundle\ContentBrowserBundle\Tree\Location;
use Symfony\Component\HttpFoundation\JsonResponse;

class TreeController extends BaseController
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Tree\TreeInterface
     */
    protected $tree;

    /**
     * Returns tree config.
     *
     * @param string $tree
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getConfig($tree)
    {
        $translator = $this->get('translator');
        $this->initTree($tree);

        $rootLocations = array();
        foreach ($this->tree->getRootLocations() as $location) {
            $locationData = $this->serializeLocation($location);
            $locationData['has_children'] = $this->tree->hasChildrenCategories($location);
            $rootLocations[] = $locationData;
        }

        $config = $this->tree->getConfig();
        $data = array(
            'name' => $translator->trans('netgen_content_browser.trees.' . $tree . '.name'),
            'root_locations' => $rootLocations,
            'min_selected' => $config['min_selected'],
            'max_selected' => $config['max_selected'],
            'default_columns' => $config['default_columns'],
            'available_columns' => $this->tree->getAvailableColumns(),
        );

        return new JsonResponse($data);
    }

    /**
     * Loads all children of the specified location.
     *
     * @param string $tree
     * @param int|string $locationId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getLocationChildren($tree, $locationId)
    {
        $this->initTree($tree);

        $location = $this->tree->getLocation($locationId);
        $children = $this->tree->getChildren($location);

        $childrenData = array();
        foreach ($children as $child) {
            $childrenData[] = $this->serializeLocation(
                $child,
                $this->tree->hasChildren($child)
            );
        }

        $data = array(
            'path' => $this->getLocationPath($location),
            'children' => $childrenData,
        );

        return new JsonResponse($data);
    }

    /**
     * Loads all children of the specified location.
     *
     * @param string $tree
     * @param int|string $locationId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getLocationCategories($tree, $locationId)
    {
        $this->initTree($tree);

        $location = $this->tree->getLocation($locationId);
        $children = $this->tree->getCategories($location);

        $childrenData = array();
        foreach ($children as $child) {
            $childrenData[] = $this->serializeLocation(
                $child,
                $this->tree->hasChildrenCategories($child)
            );
        }

        $data = array(
            'path' => $this->getLocationPath($location),
            'children' => $childrenData,
        );

        return new JsonResponse($data);
    }

    /**
     * Generates the location path.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
     *
     * @return array
     */
    protected function getLocationPath(Location $location)
    {
        $path = array();
        foreach ($location->path as $pathLocationId) {
            $pathItemLocation = $this->tree->getLocation($pathLocationId);
            if (!$this->tree->isInsideRootLocations($pathItemLocation)) {
                continue;
            }

            $path[] = array(
                'id' => $pathItemLocation->id,
                'name' => $pathItemLocation->name,
            );
        }

        return $path;
    }

    /**
     * Serializes the location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Tree\Location $location
     * @param bool $hasChildren
     *
     * @return array
     */
    protected function serializeLocation(Location $location, $hasChildren = false)
    {
        return array(
            'id' => $location->id,
            'parent_id' => !$this->tree->isRootLocation($location) ?
                $location->parentId :
                null,
            'name' => $location->name,
            'enabled' => $location->isEnabled,
            'has_children' => (bool)$hasChildren,
            'html' => $this->renderView(
                $this->tree->getConfig()['location_template'],
                array(
                    'location' => $location,
                )
            ),
        ) + $location->additionalColumns;
    }

    /**
     * Builds the tree.
     *
     * @param string $tree
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If tree does not exist
     */
    protected function initTree($tree)
    {
        if ($this->tree instanceof TreeInterface) {
            return;
        }

        if (!$this->has('netgen_content_browser.tree.' . $tree)) {
            throw new NotFoundException("Tree '{$tree}' does not exist.");
        }

        $this->tree = $this->get('netgen_content_browser.tree.' . $tree);
    }
}
