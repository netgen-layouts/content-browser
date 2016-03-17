<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Symfony\Component\HttpFoundation\JsonResponse;

class BrowseController extends Controller
{
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

    public function getChildren($itemId)
    {
        $children = $this->backend->getChildren($itemId);

        $data = array(
            'path' => $this->buildPath($itemId),
            'children' => $this->serializeItems($children),
        );

        return new JsonResponse($data);
    }
}
