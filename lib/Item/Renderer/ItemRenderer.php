<?php

namespace Netgen\ContentBrowser\Item\Renderer;

use Netgen\ContentBrowser\Item\ItemInterface;
use Twig_Environment;

class ItemRenderer implements ItemRendererInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Netgen\ContentBrowser\Item\Renderer\TemplateValueProviderInterface[]
     */
    protected $templateValueProviders = array();

    /**
     * Constructor.
     *
     * @param \Twig_Environment $twig
     * @param \Netgen\ContentBrowser\Item\Renderer\TemplateValueProviderInterface[] $templateValueProviders
     */
    public function __construct(
        Twig_Environment $twig,
        array $templateValueProviders = array()
    ) {
        $this->twig = $twig;
        $this->templateValueProviders = $templateValueProviders;
    }

    /**
     * Renders the item.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     * @param string $template
     *
     * @return string
     */
    public function renderItem(ItemInterface $item, $template)
    {
        return $this->twig->render(
            $template,
            $this->templateValueProviders[$item->getType()]
                ->getValues($item)
        );
    }
}
