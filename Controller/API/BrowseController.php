<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;
use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
use Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemChildrenAdapter;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BrowseController extends Controller
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
            $items[] = $this->itemRepository->loadByValue($value, $this->config['value_type']);
        }

        return new JsonResponse(
            $this->itemSerializer->serializeItems($items)
        );
    }

    /**
     * Returns all children of specified value.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getChildren(ItemInterface $item, Request $request)
    {
        $pager = $this->buildPager(
            new ItemChildrenAdapter(
                $this->itemRepository,
                $item
            ),
            $request
        );

        $data = array(
            'path' => $this->buildPath($item),
            'children_count' => $pager->getNbResults(),
            'children' => $this->itemSerializer->serializeItems(
                $pager->getCurrentPageResults()
            ),
        );

        return new JsonResponse($data);
    }

    /**
     * Returns all subcategories of specified value.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSubCategories(ItemInterface $item)
    {
        $data = array(
            'path' => $this->buildPath($item),
            'children' => $this->itemSerializer->serializeItems(
                $this->itemRepository->getSubCategories($item)
            ),
        );

        return new JsonResponse($data);
    }

    /**
     * Builds the path array for specified item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return array
     */
    protected function buildPath(ItemInterface $item)
    {
        $path = array();

        while (true) {
            $path[] = array(
                'id' => $item->getId(),
                'name' => $item->getName(),
            );

            if (in_array($item->getId(), $this->config['sections'])) {
                break;
            }

            try {
                $item = $this->itemRepository->load($item->getParentId(), $item->getValueType());
            } catch (NotFoundException $e) {
                break;
            }
        }

        return array_reverse($path);
    }
}
