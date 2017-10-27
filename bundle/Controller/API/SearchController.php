<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Pagerfanta\ItemSearchAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class SearchController extends Controller
{
    /**
     * Performs the search for values by using the specified text.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If search text is empty
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function search(Request $request)
    {
        $data = array(
            'children_count' => 0,
            'children' => array(),
        );

        $searchText = trim($request->query->get('searchText'));
        if (!empty($searchText)) {
            $pager = $this->buildPager(
                new ItemSearchAdapter(
                    $this->backend,
                    $searchText
                ),
                $request
            );

            $data['children_count'] = $pager->getNbResults();
            $data['children'] = $this->itemSerializer->serializeItems(
                $pager->getCurrentPageResults()
            );
        }

        return new JsonResponse($data);
    }
}
