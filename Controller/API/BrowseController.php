<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Netgen\Bundle\ContentBrowserBundle\Pagerfanta\SubItemsAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BrowseController extends Controller
{
    /**
     * Returns all locations below specified location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSubLocations(LocationInterface $location)
    {
        $data = array(
            'children' => $this->itemSerializer->serializeLocations(
                $this->itemRepository->getSubLocations($location)
            ),
        );

        return new JsonResponse($data);
    }

    /**
     * Returns all items below specified location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSubItems(LocationInterface $location, Request $request)
    {
        $pager = $this->buildPager(
            new SubItemsAdapter(
                $this->itemRepository,
                $location
            ),
            $request
        );

        $data = array(
            'path' => $this->buildPath($location),
            'parent' => $location instanceof ItemInterface ?
                $this->itemSerializer->serializeItem($location) :
                $this->itemSerializer->serializeLocation($location),
            'children_count' => $pager->getNbResults(),
            'children' => $this->itemSerializer->serializeItems(
                $pager->getCurrentPageResults()
            ),
        );

        return new JsonResponse($data);
    }
}
