<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
use Netgen\Bundle\ContentBrowserBundle\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        $config = $this->repository->getConfig();
        $data = array(
            'name' => $translator->trans('netgen_content_browser.trees.' . $tree . '.name'),
            'root_locations' => $this->serializeLocations(
                $this->repository->getRootLocations()
            ),
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
        $data['html'] = $this->renderView(
            $this->repository->getConfig()['location_template'],
            array(
                'location' => $location,
            )
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
        $this->initRepository($tree);

        $location = $this->repository->getLocation($locationId);
        $locations = $this->repository->getChildren($location);
        $data = $this->serializeLocations($locations);

        return new JsonResponse($data);
    }

    /**
     * Serializes a set of locations.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface[] $locations
     *
     * @return array
     */
    protected function serializeLocations(array $locations)
    {
        $data = array();
        foreach ($locations as $location) {
            $data[] = $this->serializeLocation($location);
        }

        return $data;
    }

    /**
     * Serializes the location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Repository\LocationInterface $location
     *
     * @return array
     */
    protected function serializeLocation(LocationInterface $location)
    {
        return array(
            'id' => $location->getId(),
            'parent_id' => !$this->repository->isRootLocation($location) ?
                $location->getParentId() :
                null,
            'name' => $location->getName(),
            'enabled' => $location->isEnabled(),
            'thumbnail' => $location->getThumbnail(),
            'type' => $location->getType(),
            'visible' => $location->isVisible(),
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
