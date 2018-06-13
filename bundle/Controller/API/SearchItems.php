<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Netgen\ContentBrowser\Pager\ItemSearchAdapter;
use Netgen\ContentBrowser\Pager\PagerFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @var \Netgen\ContentBrowser\Pager\PagerFactoryInterface
     */
    private $pagerFactory;

    public function __construct(
        BackendInterface $backend,
        ItemSerializerInterface $itemSerializer,
        PagerFactoryInterface $pagerFactory
    ) {
        $this->backend = $backend;
        $this->itemSerializer = $itemSerializer;
        $this->pagerFactory = $pagerFactory;
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
    public function __invoke(Request $request): Response
    {
        $data = [
            'children_count' => 0,
            'children' => [],
        ];

        $searchText = trim($request->query->get('searchText', ''));
        if (!empty($searchText)) {
            $limit = $request->query->get('limit');

            $pager = $this->pagerFactory->buildPager(
                new ItemSearchAdapter(
                    $this->backend,
                    $searchText
                ),
                $request->query->getInt('page', 1),
                $limit !== null ? (int) $limit : null
            );

            $data['children_count'] = $pager->getNbResults();
            $data['children'] = $this->itemSerializer->serializeItems(
                $pager->getCurrentPageResults()
            );
        }

        return new JsonResponse($data);
    }
}
