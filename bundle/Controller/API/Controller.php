<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Config\ConfigurationInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class Controller extends BaseController
{
    /**
     * @var \Netgen\ContentBrowser\Backend\BackendInterface
     */
    protected $backend;

    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    protected $config;

    /**
     * @var \Netgen\ContentBrowser\Item\Serializer\ItemSerializerInterface
     */
    protected $itemSerializer;

    /**
     * @var \Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface
     */
    protected $itemRenderer;

    public function __construct(
        BackendInterface $backend,
        ConfigurationInterface $config,
        ItemSerializerInterface $itemSerializer,
        ItemRendererInterface $itemRenderer
    ) {
        $this->backend = $backend;
        $this->config = $config;
        $this->itemSerializer = $itemSerializer;
        $this->itemRenderer = $itemRenderer;
    }

    /**
     * Initializes the controller by setting the container and performing basic access checks.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function initialize(ContainerInterface $container)
    {
        $this->setContainer($container);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
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
        $maxLimit = $this->getParameter('netgen_content_browser.browser.max_limit');
        $defaultLimit = $this->getParameter('netgen_content_browser.browser.default_limit');

        $currentPage = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', $defaultLimit);

        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($limit > 0 && $limit <= $maxLimit ? $limit : $maxLimit);
        $pager->setCurrentPage($currentPage > 0 ? $currentPage : 1);

        return $pager;
    }

    /**
     * Builds the path array for specified item.
     *
     * @param \Netgen\ContentBrowser\Item\LocationInterface $location
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

            if ($location->getParentId() === null) {
                break;
            }

            try {
                $location = $this->backend->loadLocation($location->getParentId());
            } catch (NotFoundException $e) {
                break;
            }
        }

        return array_reverse($path);
    }
}
