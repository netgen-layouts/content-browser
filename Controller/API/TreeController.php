<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
use Netgen\Bundle\ContentBrowserBundle\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Netgen\Bundle\ContentBrowserBundle\Repository\Location;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;

class TreeController extends BaseController
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Repository\RepositoryInterface
     */
    protected $repository;

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
        $this->initRepository($tree);

        $rootLocations = array();
        foreach ($this->repository->getRootLocations() as $location) {
            $locationData = $this->serializeLocation($location);
            $locationData['has_children'] = $this->repository->hasChildrenCategories($location);
            $rootLocations[] = $locationData;
        }

        $config = $this->repository->getConfig();
        $data = array(
            'name' => $translator->trans('netgen_content_browser.trees.' . $tree . '.name'),
            'root_locations' => $rootLocations,
            'min_selected' => $config['min_selected'],
            'max_selected' => $config['max_selected'],
            'default_columns' => $config['default_columns'],
            'available_columns' => array(
                'id' => $translator->trans('netgen_content_browser.columns.id'),
                'parent_id' => $translator->trans('netgen_content_browser.columns.parent_id'),
                'name' => $translator->trans('netgen_content_browser.columns.name'),
                'thumbnail' => $translator->trans('netgen_content_browser.columns.thumbnail'),
                'type' => $translator->trans('netgen_content_browser.columns.type'),
                'visible' => $translator->trans('netgen_content_browser.columns.visible'),
                'owner' => $translator->trans('netgen_content_browser.columns.owner'),
                'modified' => $translator->trans('netgen_content_browser.columns.modified'),
                'published' => $translator->trans('netgen_content_browser.columns.published'),
                'priority' => $translator->trans('netgen_content_browser.columns.priority'),
                'section' => $translator->trans('netgen_content_browser.columns.section'),
            ),
        );

        return new JsonResponse($data);
    }

    /**
     * Loads the specified location.
     *
     * @param string $tree
     * @param int|string $locationId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getLocation($tree, $locationId)
    {
        $this->initRepository($tree);

        $location = $this->repository->getLocation($locationId);
        $data = $this->serializeLocation($location);

        $data['has_children'] = $this->repository->hasChildren($location);

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
        $this->initRepository($tree);

        $location = $this->repository->getLocation($locationId);
        $locations = $this->repository->getChildren($location);

        $data = array();
        foreach ($locations as $location) {
            $locationData = $this->serializeLocation($location);
            $locationData['has_children'] = $this->repository->hasChildren($location);
            $data[] = $locationData;
        }

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
        $this->initRepository($tree);

        $location = $this->repository->getLocation($locationId);
        $locations = $this->repository->getCategories($location);

        $data = array();
        foreach ($locations as $location) {
            $locationData = $this->serializeLocation($location);
            $locationData['has_children'] = $this->repository->hasChildrenCategories($location);
            $data[] = $locationData;
        }

        return new JsonResponse($data);
    }

    /**
     * Serializes the location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\Location $location
     *
     * @return array
     */
    protected function serializeLocation(Location $location)
    {
        return array(
            'id' => $location->id,
            'parent_id' => !$this->repository->isRootLocation($location) ?
                $location->parentId :
                null,
            'name' => $location->name,
            'enabled' => $location->isEnabled,
            'thumbnail' => $location->thumbnail,
            'type' => $location->type,
            'visible' => $location->isVisible,
            'owner' => $location->owner,
            'modified' => $location->modified->format(DateTime::ISO8601),
            'published' => $location->published->format(DateTime::ISO8601),
            'priority' => $location->priority,
            'section' => $location->section,
            'html' => $this->renderView(
                $this->repository->getConfig()['location_template'],
                array(
                    'location' => $location,
                )
            )
        );
    }

    /**
     * Builds the repository from provided tree config.
     *
     * @param string $tree
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If tree config does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Repository\RepositoryInterface
     */
    protected function initRepository($tree)
    {
        if ($this->repository instanceof RepositoryInterface) {
            return;
        }

        $trees = $this->getParameter('netgen_content_browser.trees');

        if (!isset($trees[$tree])) {
            throw new NotFoundException("Tree {$tree} not found.");
        }

        $this->repository = $this->get('netgen_content_browser.repository');
        $this->repository->setConfig($trees[$tree]);
    }
}
