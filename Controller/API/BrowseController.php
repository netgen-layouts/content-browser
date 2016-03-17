<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Symfony\Component\HttpFoundation\JsonResponse;

class BrowseController extends Controller
{
    public function getSubCategories($itemId)
    {
        $config = $this->get('netgen_content_browser.current_config');
        $backend = $this->get('netgen_content_browser.current_backend');

        $subCategories = $backend->getChildren(
            array(
                'item_id' => $itemId,
                'types' => $config['category_types'],
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
        $config = $this->get('netgen_content_browser.current_config');
        $backend = $this->get('netgen_content_browser.current_backend');

        $children = $backend->getChildren(
            array(
                'item_id' => $itemId,
                'types' => $config['types'],
            )
        );

        $data = array(
            'path' => $this->buildPath($itemId),
            'children' => $this->serializeItems($children),
        );

        return new JsonResponse($data);
    }
}
