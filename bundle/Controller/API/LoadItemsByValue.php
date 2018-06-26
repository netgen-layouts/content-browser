<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If values are missing or invalid
     */
    public function __invoke(Request $request): Response
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
                'items' => iterator_to_array(
                    $this->itemSerializer->serializeItems($items)
                ),
            ]
        );
    }
}