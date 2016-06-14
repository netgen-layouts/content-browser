<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;
use Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ItemChildrenAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BrowseController extends Controller
{
    /**
     * Returns all subcategories of specified item.
     *
     * @param int|string $itemId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSubCategories($itemId)
    {
        $subCategories = $this->backend->getChildren(
            $itemId,
            array(
                'types' => $this->config['category_types'],
            )
        );

        $data = array(
            'path' => $this->buildPath($itemId),
            'children' => $this->itemSerializer->serializeValues(
                $subCategories
            ),
        );

        return new JsonResponse($data);
    }

    /**
     * Returns all items with specified value IDs.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If value IDs are missing or invalid.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getValues(Request $request)
    {
        $values = explode(',', $request->query->get('values'));
        if (!is_array($values) || empty($values)) {
            throw new InvalidArgumentException('List of values is invalid.');
        }

        $valueObjects = array();
        foreach ($values as $value) {
            $valueObjects[] = $this->valueLoader->loadByValue($value);
        }

        return new JsonResponse(
            $this->itemSerializer->serializeValues($valueObjects)
        );
    }

    /**
     * Returns all children of specified item.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int|string $itemId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getChildren(Request $request, $itemId)
    {
        $pager = $this->buildPager(
            new ItemChildrenAdapter(
                $this->backend,
                $itemId
            ),
            $request
        );

        $data = array(
            'path' => $this->buildPath($itemId),
            'children_count' => $pager->getNbResults(),
            'children' => $this->itemSerializer->serializeValues(
                $pager->getCurrentPageResults()
            ),
        );

        return new JsonResponse($data);
    }
}
