<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class LoadSubLocations extends AbstractController
{
    private BackendInterface $backend;

    private ItemSerializerInterface $itemSerializer;

    public function __construct(BackendInterface $backend, ItemSerializerInterface $itemSerializer)
    {
        $this->backend = $backend;
        $this->itemSerializer = $itemSerializer;
    }

    /**
     * Returns all locations below specified location.
     */
    public function __invoke(LocationInterface $location): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $children = [];
        foreach ($this->backend->getSubLocations($location) as $subLocation) {
            $children[] = $this->itemSerializer->serializeLocation($subLocation);
        }

        return $this->json(['children' => $children]);
    }
}
