<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
use Netgen\Bundle\ContentBrowserBundle\Item\Builder\BuilderInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface
     */
    protected $backend;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Builder\BuilderInterface
     */
    protected $itemBuilder;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface $backend
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Builder\BuilderInterface $itemBuilder
     * @param array $config
     */
    public function __construct(
        BackendInterface $backend,
        BuilderInterface $itemBuilder,
        array $config
    ) {
        $this->backend = $backend;
        $this->itemBuilder = $itemBuilder;
        $this->config = $config;
    }

    /**
     * Builds the path array for specified item.
     *
     * @param int|string $itemId
     *
     * @return array
     */
    protected function buildPath($itemId)
    {
        $path = array();

        while ($itemId !== null) {
            try {
                $item = $this->itemBuilder->buildItemReference(
                    $this->backend->loadItem($itemId)
                );
            } catch (NotFoundException $e) {
                break;
            }

            array_unshift(
                $path,
                array(
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                )
            );

            $itemId = $item->getParentId();
        }

        return $path;
    }

    /**
     * Builds the specified items and serializes them to an array.
     *
     * @param array $items
     *
     * @return array
     */
    public function serializeItems(array $items)
    {
        $serializedItems = array_map(
            function ($item) {
                return $this->serializeItem(
                    $this->itemBuilder->buildItem($item)
                );
            },
            $items
        );

        return $serializedItems;
    }

    /**
     * Serializes specified item to an array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     *
     * @return array
     */
    protected function serializeItem(ItemInterface $item)
    {
        $data = $item->jsonSerialize();

        $data['html'] = $this->get('twig')->render(
            $this->config['template'],
            $item->getTemplateVariables()
        );

        return $data;
    }
}
