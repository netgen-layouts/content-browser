<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Form\Type;

use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Registry\BackendRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBrowserMultipleType extends AbstractType
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
        $resolver->setDefaults(
            [
                'entry_type' => HiddenType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
            ]
        );

        $resolver->setRequired(['item_type', 'min', 'max', 'start_location', 'custom_params']);

        $resolver->setAllowedTypes('item_type', 'string');
        $resolver->setAllowedTypes('min', ['int', 'null']);
        $resolver->setAllowedTypes('max', ['int', 'null']);
        $resolver->setAllowedTypes('start_location', ['int', 'string', 'null']);
        $resolver->setAllowedTypes('custom_params', 'array');

        $resolver->setAllowedValues(
            'item_type',
            function (string $itemType): bool {
                return $this->backendRegistry->hasBackend($itemType);
            }
        );

        $resolver->setAllowedValues(
            'custom_params',
            function (array $customParams): bool {
                foreach ($customParams as $customParam) {
                    if (!is_scalar($customParam) && !is_array($customParam)) {
                        return false;
                    }

                    if (is_array($customParam)) {
                        foreach ($customParam as $innerCustomParam) {
                            if (!is_scalar($innerCustomParam)) {
                                return false;
                            }
                        }
                    }
                }

                return true;
            }
        );

        $resolver->setDefault('min', null);
        $resolver->setDefault('max', null);
        $resolver->setDefault('start_location', null);

        $resolver->setNormalizer(
            'max',
            function (Options $options, ?int $value): ?int {
                if ($value === null || $options['min'] === null) {
                    return $value;
                }

                if ($value < $options['min']) {
                    return $options['min'];
                }

                return $value;
            }
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['items'] = [];
        $view->vars['item_type'] = $options['item_type'];

        if ($form->getData() !== null) {
            $view->vars['items'] = $this->getItems($form->getData(), $options['item_type']);
        }

        $view->vars['min'] = $options['min'];
        $view->vars['max'] = $options['max'];
        $view->vars['start_location'] = $options['start_location'];
        $view->vars['custom_params'] = $options['custom_params'];
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'ngcb_multiple';
    }

    /**
     * Returns the array of items for all provided item values.
     *
     * @param mixed $itemValues
     * @param string $itemType
     *
     * @return array
     */
    private function getItems($itemValues, string $itemType): array
    {
        $items = [];

        foreach ((array) $itemValues as $itemValue) {
            try {
                $backend = $this->backendRegistry->getBackend($itemType);
                $item = $backend->loadItem($itemValue);
                $items[$item->getValue()] = $item;
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        return $items;
    }
}
