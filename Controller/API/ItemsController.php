<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;
use Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Netgen\Bundle\ContentBrowserBundle\Pagerfanta\SubItemsAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ItemsController extends Controller
{
    /**
     * Returns all value objects with specified values.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If values are missing or invalid.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getValues(Request $request)
    {
        $values = explode(',', $request->query->get('values'));
        if (!is_array($values) || empty($values)) {
            throw new InvalidArgumentException('List of values is invalid.');
        }

        $items = array();
        foreach ($values as $value) {
            $items[] = $this->itemRepository->loadItem($value, $this->config['value_type']);
        }

        return new JsonResponse(
            $this->itemSerializer->serializeItems($items)
        );
    }

    /**
     * Returns all children of specified category.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSubItems(CategoryInterface $category, Request $request)
    {
        $pager = $this->buildPager(
            new SubItemsAdapter(
                $this->itemRepository,
                $category
            ),
            $request
        );

        $data = array(
            'path' => $this->buildPath($category),
            'parent' => $category instanceof ItemInterface ?
                $this->itemSerializer->serializeItem($category) :
                $this->itemSerializer->serializeCategory($category),
            'children_count' => $pager->getNbResults(),
            'children' => $this->itemSerializer->serializeItems(
                $pager->getCurrentPageResults()
            ),
        );

        return new JsonResponse($data);
    }
}
