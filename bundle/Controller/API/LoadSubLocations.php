<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(LocationInterface $location)
    {
        $data = [
            'children' => $this->itemSerializer->serializeLocations(
                $this->backend->getSubLocations($location)
            ),
        ];

        return new JsonResponse($data);
    }
}
