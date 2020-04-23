<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function array_map;
use function count;
use function explode;
use function trim;

final class LoadItemsByValue extends AbstractController
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $queryValues = trim($request->query->get('values', ''));
        $values = array_map('trim', explode(',', $queryValues));

        if ($queryValues === '' || count($values) === 0) {
            throw new InvalidArgumentException('List of values is invalid.');
        }

        $items = [];
        foreach ($values as $value) {
            $items[] = $this->itemSerializer->serializeItem(
                $this->backend->loadItem($value)
            );
        }

        return $this->json(['items' => $items]);
    }
}
