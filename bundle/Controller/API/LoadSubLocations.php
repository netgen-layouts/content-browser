<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class LoadSubLocations extends Controller
{
    /**
     * @var \Netgen\ContentBrowser\Backend\BackendInterface
     */
    private $backend;

    /**
     * @var \Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface
     */
    private $itemSerializer;

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
        $children = [];
        foreach ($this->backend->getSubLocations($location) as $subLocation) {
            $children[] = $this->itemSerializer->serializeLocation($subLocation);
        }

        return new JsonResponse(['children' => $children]);
    }
}
