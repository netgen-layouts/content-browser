<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Netgen\ContentBrowser\Pager\ItemSearchAdapter;
use Netgen\ContentBrowser\Pager\PagerFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use function mb_trim;

final class SearchItems extends AbstractController
{
    public function __construct(
        private BackendInterface $backend,
        private ItemSerializerInterface $itemSerializer,
        private PagerFactoryInterface $pagerFactory,
        private int $defaultLimit,
    ) {}

    /**
     * Performs the search for values by using the specified text.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $searchText = mb_trim($request->query->getString('searchText'));
        if ($searchText === '') {
            return $this->json(['children' => [], 'children_count' => 0]);
        }

        $sectionId = mb_trim($request->query->getString('sectionId'));
        $section = $sectionId !== '' ?
            $this->backend->loadLocation($sectionId) :
            null;

        $searchQuery = new SearchQuery($searchText, $section);

        $pager = $this->pagerFactory->buildPager(
            new ItemSearchAdapter($this->backend, $searchQuery),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', $this->defaultLimit),
        );

        $data = [
            'children' => [],
            'children_count' => $pager->getNbResults(),
        ];

        foreach ($pager->getCurrentPageResults() as $item) {
            $data['children'][] = $this->itemSerializer->serializeItem($item);
        }

        return $this->json($data);
    }
}
