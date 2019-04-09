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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadSubItems extends AbstractController
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

    /**
     * @var int
     */
    private $defaultLimit;

    public function __construct(
        BackendInterface $backend,
        ItemSerializerInterface $itemSerializer,
        PagerFactoryInterface $pagerFactory,
        int $defaultLimit
    ) {
        $this->backend = $backend;
        $this->itemSerializer = $itemSerializer;
        $this->pagerFactory = $pagerFactory;
        $this->defaultLimit = $defaultLimit;
    }

    /**
     * Returns all items below specified location.
     */
    public function __invoke(LocationInterface $location, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $pager = $this->pagerFactory->buildPager(
            new SubItemsAdapter($this->backend, $location),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', $this->defaultLimit)
        );

        $data = [
            'path' => $this->buildPath($location),
            'parent' => $location instanceof ItemInterface ?
                $this->itemSerializer->serializeItem($location) :
                $this->itemSerializer->serializeLocation($location),
            'children' => [],
            'children_count' => $pager->getNbResults(),
        ];

        foreach ($pager->getCurrentPageResults() as $item) {
            $data['children'][] = $this->itemSerializer->serializeItem($item);
        }

        return $this->json($data);
    }

    /**
     * Builds the path array for specified item.
     */
    private function buildPath(LocationInterface $location): array
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
