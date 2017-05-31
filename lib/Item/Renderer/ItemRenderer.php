<?php

namespace Netgen\ContentBrowser\Item\Renderer;

use Exception;
use Netgen\ContentBrowser\Item\ItemInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig_Environment;

class ItemRenderer implements ItemRendererInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param \Twig_Environment $twig
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(Twig_Environment $twig, LoggerInterface $logger = null)
    {
        $this->twig = $twig;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Renders the item. In case the rendering error is occurred, an empty string is returned.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     * @param string $template
     *
     * @return string
     */
    public function renderItem(ItemInterface $item, $template)
    {
        try {
            return $this->twig->render(
                $template,
                array(
                    'item' => $item,
                )
            );
        } catch (Exception $e) {
            $this->logger->error(
                sprintf(
                    'An error occurred while rendering an item with "%s" value: %s',
                    $item->getValue(),
                    $e->getMessage()
                )
            );

            return '';
        }
    }
}
