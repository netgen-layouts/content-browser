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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use function array_reverse;

final class LoadSubItems extends AbstractController
{
    public function __construct(
        private BackendInterface $backend,
        private ItemSerializerInterface $itemSerializer,
        private PagerFactoryInterface $pagerFactory,
        private int $defaultLimit,
    ) {}

    /**
     * Returns all items below specified location.
     */
    public function __invoke(LocationInterface $location, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $pager = $this->pagerFactory->buildPager(
            new SubItemsAdapter($this->backend, $location),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', $this->defaultLimit),
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
     *
     * @return array<array<string, mixed>>
     */
    private function buildPath(LocationInterface $location): array
    {
        $path = [];

        while (true) {
            $path[] = [
                'id' => $location->locationId,
                'name' => $location->name,
            ];

            $parentId = $location->parentId;
            if ($parentId === null) {
                break;
            }

            try {
                $location = $this->backend->loadLocation($parentId);
            } catch (NotFoundException) {
                break;
            }
        }

        return array_reverse($path);
    }
}
