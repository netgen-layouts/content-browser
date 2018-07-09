<?php

declare(strict_types=1);

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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['item_type', 'start_location']);

        $resolver->setAllowedTypes('item_type', 'string');
        $resolver->setAllowedTypes('start_location', ['int', 'string', 'null']);

        $resolver->setAllowedValues(
            'item_type',
            function (string $itemType): bool {
                return $this->backendRegistry->hasBackend($itemType);
            }
        );

        $resolver->setDefault('start_location', null);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $item = null;
        if ($form->getData() !== null) {
            try {
                $backend = $this->backendRegistry->getBackend($options['item_type']);
                $item = $backend->loadItem($form->getData());
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        $view->vars['item'] = $item;
        $view->vars['item_type'] = $options['item_type'];
        $view->vars['start_location'] = $options['start_location'];
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'ngcb';
    }
}
