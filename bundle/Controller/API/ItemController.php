<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\ItemInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ItemController extends Controller
{
    /**
     * Renders the provided item.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderItem(ItemInterface $item)
    {
        $renderedItem = '';
        if ($this->config->hasPreview()) {
            $renderedItem = $this->itemRenderer->renderItem($item, $this->config->getTemplate());
        }

        return new Response($renderedItem);
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
    public function getByValues(Request $request)
    {
        $queryValues = trim($request->query->get('values', ''));
        $values = array_map('trim', explode(',', $queryValues));

        if (empty($queryValues) || empty($values)) {
            throw new InvalidArgumentException('List of values is invalid.');
        }

        $items = array();
        foreach ($values as $value) {
            $items[] = $this->backend->loadItem($value);
        }

        return new JsonResponse(
            array(
                'items' => $this->itemSerializer->serializeItems($items),
            )
        );
    }
}
