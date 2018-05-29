<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Netgen\ContentBrowser\Pagerfanta\ItemSearchAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class SearchItems extends Controller
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
     * Performs the search for values by using the specified text.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If search text is empty
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $data = [
            'children_count' => 0,
            'children' => [],
        ];

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
