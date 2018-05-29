<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class LoadItemsByValue extends Controller
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
     * Returns all items with specified values.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If values are missing or invalid
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $queryValues = trim($request->query->get('values', ''));
        $values = array_map('trim', explode(',', $queryValues));

        if (empty($queryValues) || empty($values)) {
            throw new InvalidArgumentException('List of values is invalid.');
        }

        $items = [];
        foreach ($values as $value) {
            $items[] = $this->backend->loadItem($value);
        }

        return new JsonResponse(
            [
                'items' => $this->itemSerializer->serializeItems($items),
            ]
        );
    }
}
