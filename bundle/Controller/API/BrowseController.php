<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Pagerfanta\SubItemsAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class BrowseController extends Controller
{
    /**
     * Returns all locations below specified location.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSubLocations(LocationInterface $location)
    {
        $data = [
            'children' => $this->itemSerializer->serializeLocations(
                $this->backend->getSubLocations($location)
            ),
        ];

        return new JsonResponse($data);
    }

    /**
     * Returns all items below specified location.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSubItems(LocationInterface $location, Request $request)
    {
        $pager = $this->buildPager(
            new SubItemsAdapter(
                $this->backend,
                $location
            ),
            $request
        );

        $data = [
            'path' => $this->buildPath($location),
            'parent' => $location instanceof ItemInterface ?
                $this->itemSerializer->serializeItem($location) :
                $this->itemSerializer->serializeLocation($location),
            'children_count' => $pager->getNbResults(),
            'children' => $this->itemSerializer->serializeItems(
                $pager->getCurrentPageResults()
            ),
        ];

        return new JsonResponse($data);
    }
}
