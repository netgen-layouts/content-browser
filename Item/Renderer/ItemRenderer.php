<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Renderer;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Twig_Environment;

class ItemRenderer implements ItemRendererInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\TemplateValueProviderInterface[]
     */
    protected $templateValueProviders = array();

    /**
     * Constructor.
     *
     * @param \Twig_Environment $twig
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Renderer\TemplateValueProviderInterface[] $templateValueProviders
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
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface $item
     * @param string $template
     *
     * @return string
     */
    public function renderItem(ItemInterface $item, $template)
    {
        return $this->twig->render(
            $template,
            $this->templateValueProviders[$item->getValueType()]
                ->getValues($item->getObject())
        );
    }
}
