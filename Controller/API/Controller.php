<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Config\ConfigurationInterface;
use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
use Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

abstract class Controller extends BaseController
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializerInterface
     */
    protected $itemSerializer;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\ConfigurationInterface
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface $itemRepository
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializerInterface $itemSerializer
     * @param \Netgen\Bundle\ContentBrowserBundle\Config\ConfigurationInterface $config
     */
    public function __construct(
        ItemRepositoryInterface $itemRepository,
        ItemSerializerInterface $itemSerializer,
        ConfigurationInterface $config
    ) {
        $this->itemRepository = $itemRepository;
        $this->itemSerializer = $itemSerializer;
        $this->config = $config;
    }

    /**
     * Builds the pager from provided adapter.
     *
     * @param \Pagerfanta\Adapter\AdapterInterface $adapter
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Pagerfanta\Pagerfanta
     */
    protected function buildPager(AdapterInterface $adapter, Request $request)
    {
        $maxLimit = $this->getParameter('netgen_content_browser.browser.default_limit');
        $currentPage = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', $maxLimit);

        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($limit > 0 && $limit <= $maxLimit ? $limit : $maxLimit);
        $pager->setCurrentPage($currentPage > 0 ? $currentPage : 1);

        return $pager;
    }

    /**
     * Builds the path array for specified item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return array
     */
    protected function buildPath(LocationInterface $location)
    {
        $path = array();

        while (true) {
            $path[] = array(
                'id' => $location->getLocationId(),
                'name' => $location->getName(),
            );

            if (in_array($location->getLocationId(), $this->config->getSections())) {
                break;
            }

            if ($location->getParentId() === null) {
                break;
            }

            try {
                $location = $this->itemRepository->loadLocation(
                    $location->getParentId(),
                    $location->getType()
                );
            } catch (NotFoundException $e) {
                break;
            }
        }

        return array_reverse($path);
    }
}
