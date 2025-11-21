<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Item\Renderer;

use Netgen\ContentBrowser\Item\ItemInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;
use Twig\Environment;

use function sprintf;

final class ItemRenderer implements ItemRendererInterface
{
    public function __construct(
        private Environment $twig,
        private LoggerInterface $logger = new NullLogger(),
    ) {}

    public function renderItem(ItemInterface $item, string $template): string
    {
        $renderedItem = '';

        try {
            $renderedItem = $this->twig->render(
                $template,
                [
                    'item' => $item,
                ],
            );
        } catch (Throwable $t) {
            $this->logger->critical(
                sprintf(
                    'An error occurred while rendering an item with "%s" value: %s',
                    $item->value,
                    $t->getMessage(),
                ),
                ['error' => $t],
            );
        }

        return $renderedItem;
    }
}
