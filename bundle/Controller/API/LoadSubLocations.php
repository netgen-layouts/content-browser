<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

final class LoadSubLocations extends AbstractController
{
    public function __construct(
        private BackendInterface $backend,
        private ItemSerializerInterface $itemSerializer,
    ) {}

    /**
     * Returns all locations below specified location.
     */
    public function __invoke(LocationInterface $location): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $children = [];
        foreach ($this->backend->getSubLocations($location) as $subLocation) {
            $children[] = $this->itemSerializer->serializeLocation($subLocation);
        }

        return $this->json(['children' => $children]);
    }
}
