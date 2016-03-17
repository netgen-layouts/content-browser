<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $searchText = $request->query->get('searchText');
        if (empty($searchText)) {
            throw new InvalidArgumentException('Search text cannot be empty');
        }

        $backend = $this->get('netgen_content_browser.current_backend');

        $children = $backend->search(
            array(
                'search_text' => $searchText,
            )
        );

        $data = array(
            'children' => $this->serializeItems($children),
        );

        return new JsonResponse($data);
    }
}
