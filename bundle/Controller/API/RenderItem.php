<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Config\ConfigurationInterface;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Symfony\Component\HttpFoundation\Response;

final class RenderItem extends Controller
{
    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    private $config;

    /**
     * @var \Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface
     */
    private $itemRenderer;

    public function __construct(ConfigurationInterface $config, ItemRendererInterface $itemRenderer)
    {
        $this->config = $config;
        $this->itemRenderer = $itemRenderer;
    }

    /**
     * Renders the provided item.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(ItemInterface $item)
    {
        $renderedItem = '';
        if ($this->config->hasPreview()) {
            $renderedItem = $this->itemRenderer->renderItem($item, $this->config->getTemplate());
        }

        return new Response($renderedItem);
    }
}
