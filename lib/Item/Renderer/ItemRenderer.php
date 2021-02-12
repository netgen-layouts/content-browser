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
    private Environment $twig;

    private LoggerInterface $logger;

    public function __construct(Environment $twig, ?LoggerInterface $logger = null)
    {
        $this->twig = $twig;
        $this->logger = $logger ?? new NullLogger();
    }

    public function renderItem(ItemInterface $item, string $template): string
    {
        $renderedItem = '';

        try {
            $renderedItem = $this->twig->render(
                $template,
                [
                    'item' => $item,
                ]
            );
        } catch (Throwable $t) {
            $this->logger->critical(
                sprintf(
                    'An error occurred while rendering an item with "%s" value: %s',
                    $item->getValue(),
                    $t->getMessage()
                ),
                ['error' => $t]
            );
        }

        return $renderedItem;
    }
}
