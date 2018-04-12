<?php

namespace Netgen\ContentBrowser\Item\Renderer;

use Exception;
use Netgen\ContentBrowser\Item\ItemInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;
use Twig\Environment;

final class ItemRenderer implements ItemRendererInterface
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(Environment $twig, LoggerInterface $logger = null)
    {
        $this->twig = $twig;
        $this->logger = $logger ?: new NullLogger();
    }

    public function renderItem(ItemInterface $item, $template)
    {
        $renderedItem = '';

        try {
            $renderedItem = $this->twig->render(
                $template,
                array(
                    'item' => $item,
                )
            );
        } catch (Throwable $t) {
            $this->logger->critical(
                sprintf(
                    'An error occurred while rendering an item with "%s" value: %s',
                    $item->getValue(),
                    $t->getMessage()
                )
            );
        } catch (Exception $e) {
            $this->logger->critical(
                sprintf(
                    'An error occurred while rendering an item with "%s" value: %s',
                    $item->getValue(),
                    $e->getMessage()
                )
            );
        }

        return $renderedItem;
    }
}
