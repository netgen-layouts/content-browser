<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
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

    protected function buildPath($itemId)
    {
        $path = array();

        while ($itemId !== null) {
            $item = $this->itemBuilder->buildItemReference(
                $this->backend->loadItem($itemId)
            );

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

    public function serializeItems(array $items)
    {
        $itemBuilder = $this->itemBuilder;

        $serializedItems = array_map(
            function ($item) use ($itemBuilder) {
                return $this->serializeItem(
                    $this->itemBuilder->buildItem($item)
                );
            },
            $items
        );

        return $serializedItems;
    }

    protected function serializeItem(ItemInterface $item)
    {
        return array(
            'html' => $this->get('twig')->render(
                $this->config['template'],
                $item->getTemplateVariables()
            ),
        ) + $item->jsonSerialize();
    }
}
