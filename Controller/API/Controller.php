<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializerInterface;
use Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistryInterface;
use Netgen\Bundle\ContentBrowserBundle\Registry\ValueLoaderRegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

abstract class Controller extends BaseController
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistryInterface
     */
    protected $backendRegistry;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Registry\ValueLoaderRegistryInterface
     */
    protected $valueLoaderRegistry;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializerInterface
     */
    protected $itemSerializer;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface
     */
    protected $backend;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Value\ValueLoaderInterface
     */
    protected $valueLoader;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistryInterface $backendRegistry
     * @param \Netgen\Bundle\ContentBrowserBundle\Registry\ValueLoaderRegistryInterface $valueLoaderRegistry
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Serializer\ItemSerializerInterface $itemSerializer
     * @param array $config
     */
    public function __construct(
        BackendRegistryInterface $backendRegistry,
        ValueLoaderRegistryInterface $valueLoaderRegistry,
        ItemSerializerInterface $itemSerializer,
        array $config
    ) {
        $this->backendRegistry = $backendRegistry;
        $this->valueLoaderRegistry = $valueLoaderRegistry;
        $this->itemSerializer = $itemSerializer;
        $this->config = $config;

        $this->backend = $this->backendRegistry->getBackend($this->config['value_type']);
        $this->valueLoader = $this->valueLoaderRegistry->getValueLoader($this->config['value_type']);
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
        $currentPage = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', 0);

        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($limit > 0 ? $limit : $this->config['default_limit']);
        $pager->setCurrentPage($currentPage > 0 ? $currentPage : 1);

        return $pager;
    }

    /**
     * Builds the path array for specified value.
     *
     * @param int|string $valueId
     *
     * @return array
     */
    protected function buildPath($valueId)
    {
        $path = array();

        while ($valueId !== null) {
            $value = $this->valueLoader->load($valueId);

            array_unshift(
                $path,
                array(
                    'id' => $value->getId(),
                    'name' => $value->getName(),
                )
            );

            if (in_array($valueId, $this->config['root_items'])) {
                break;
            }

            $valueId = $value->getParentId();
        }

        return $path;
    }
}
