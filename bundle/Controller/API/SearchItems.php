<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Netgen\ContentBrowser\Pager\ItemSearchAdapter;
use Netgen\ContentBrowser\Pager\PagerFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function trim;

final class SearchItems extends AbstractController
{
    private BackendInterface $backend;

    private ItemSerializerInterface $itemSerializer;

    private PagerFactoryInterface $pagerFactory;

    private int $defaultLimit;

    public function __construct(
        BackendInterface $backend,
        ItemSerializerInterface $itemSerializer,
        PagerFactoryInterface $pagerFactory,
        int $defaultLimit
    ) {
        $this->backend = $backend;
        $this->itemSerializer = $itemSerializer;
        $this->pagerFactory = $pagerFactory;
        $this->defaultLimit = $defaultLimit;
    }

    /**
     * Performs the search for values by using the specified text.
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If search text is empty
     */
    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $searchText = trim((string) ($request->query->get('searchText') ?? ''));
        if ($searchText === '') {
            return $this->json(['children' => [], 'children_count' => 0]);
        }

        $sectionId = trim((string) ($request->query->get('sectionId') ?? ''));
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
