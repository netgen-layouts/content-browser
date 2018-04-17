<?php

namespace Netgen\ContentBrowser\Form\Type;

use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Registry\BackendRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBrowserType extends AbstractType
{
    /**
     * @var \Netgen\ContentBrowser\Registry\BackendRegistryInterface
     */
    private $backendRegistry;

    public function __construct(BackendRegistryInterface $backendRegistry)
    {
        $this->backendRegistry = $backendRegistry;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['item_type', 'start_location']);

        $resolver->setAllowedTypes('item_type', 'string');
        $resolver->setAllowedTypes('start_location', ['int', 'string', 'null']);

        $resolver->setDefault('start_location', null);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $itemName = null;
        if ($form->getData() !== null) {
            try {
                $backend = $this->backendRegistry->getBackend($options['item_type']);
                $item = $backend->loadItem($form->getData());
                $itemName = $item->getName();
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        $view->vars['item_type'] = $options['item_type'];
        $view->vars['item_name'] = $itemName;
        $view->vars['start_location'] = $options['start_location'];
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'ng_content_browser';
    }
}
