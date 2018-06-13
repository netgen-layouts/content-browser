<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Netgen\ContentBrowser\Pager\PagerFactoryInterface;
use Netgen\ContentBrowser\Pager\SubItemsAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class LoadSubItems extends Controller
{
    /**
     * @var \Netgen\ContentBrowser\Backend\BackendInterface
     */
    private $backend;

    /**
     * @var \Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface
     */
    private $itemSerializer;

    /**
     * @var \Netgen\ContentBrowser\Pager\PagerFactoryInterface
     */
    private $pagerFactory;

    public function __construct(
        BackendInterface $backend,
        ItemSerializerInterface $itemSerializer,
        PagerFactoryInterface $pagerFactory
    ) {
        $this->backend = $backend;
        $this->itemSerializer = $itemSerializer;
        $this->pagerFactory = $pagerFactory;
    }

    /**
     * Returns all items below specified location.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(LocationInterface $location, Request $request)
    {
        $limit = $request->query->get('limit');

        $pager = $this->pagerFactory->buildPager(
            new SubItemsAdapter(
                $this->backend,
                $location
            ),
            $request->query->getInt('page', 1),
            $limit !== null ? (int) $limit : null
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

    /**
     * Builds the path array for specified item.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     *
     * @return array
     */
    private function buildPath(LocationInterface $location)
    {
        $path = [];

        while (true) {
            $path[] = [
                'id' => $location->getLocationId(),
                'name' => $location->getName(),
            ];

            if ($location->getParentId() === null) {
                break;
            }

            try {
                $location = $this->backend->loadLocation($location->getParentId());
            } catch (NotFoundException $e) {
                break;
            }
        }

        return array_reverse($path);
    }
}
