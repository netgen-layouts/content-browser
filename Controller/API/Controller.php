<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    protected function buildPath($itemId)
    {
        $backend = $this->get('netgen_content_browser.current_backend');
        $itemBuilder = $this->get('netgen_content_browser.item_builder');

        $path = array();

        while ($itemId !== null) {
            $item = $itemBuilder->buildItemReference(
                $backend->loadItem($itemId)
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
        $config = $this->get('netgen_content_browser.current_config');
        $itemBuilder = $this->get('netgen_content_browser.item_builder');

        $serializedItems = array_map(
            function ($item) use ($config, $itemBuilder) {
                return $this->serializeItem(
                    $config,
                    $itemBuilder->buildItem($item)
                );
            },
            $items
        );

        return $serializedItems;
    }

    protected function serializeItem($config, ItemInterface $item)
    {
        return array(
            'html' => $this->get('twig')->render(
                $config['template'],
                $item->getTemplateVariables()
            ),
        ) + $item->jsonSerialize();
    }
}
