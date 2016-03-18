<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

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
            'children' => $this->serializeItems($subCategories),
        );

        return new JsonResponse($data);
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
            'children' => $this->serializeItems($pager->getCurrentPageResults()),
        );

        return new JsonResponse($data);
    }
}
