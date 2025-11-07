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
    public function __construct(
        private Configuration $config,
        private ItemRendererInterface $itemRenderer,
    ) {}

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
