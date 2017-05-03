<?php

namespace Netgen\ContentBrowser\Item\Renderer;

use Exception;
use Netgen\ContentBrowser\Item\ItemInterface;
use Twig_Environment;

class ItemRenderer implements ItemRendererInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
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
            return '';
        }
    }
}
