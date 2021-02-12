<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use function is_string;

final class RenderItem extends AbstractController
{
    private Configuration $config;

    private ItemRendererInterface $itemRenderer;

    public function __construct(Configuration $config, ItemRendererInterface $itemRenderer)
    {
        $this->config = $config;
        $this->itemRenderer = $itemRenderer;
    }

    /**
     * Renders the provided item.
     */
    public function __invoke(ItemInterface $item): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $renderedItem = '';
        $template = $this->config->getTemplate();
        if (is_string($template) && $this->config->hasPreview()) {
            $renderedItem = $this->itemRenderer->renderItem($item, $template);
        }

        return new Response($renderedItem);
    }
}
