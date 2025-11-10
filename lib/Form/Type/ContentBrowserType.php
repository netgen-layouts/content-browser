<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Form\Type;

use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_any;
use function is_array;
use function is_scalar;

final class ContentBrowserType extends AbstractType
{
    public function __construct(
        private BackendRegistry $backendRegistry,
    ) {}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('item_type')
            ->required()
            ->allowedTypes('string')
            ->allowedValues(
                fn (string $itemType): bool => $this->backendRegistry->hasBackend($itemType),
            )->info('It must be a valid item type.');

        $resolver
            ->define('start_location')
            ->required()
            ->default(null)
            ->allowedTypes('int', 'string', 'null');

        $resolver
            ->define('custom_params')
            ->required()
            ->default([])
            ->allowedTypes('array')
            ->allowedValues(
                static function (array $customParams): bool {
                    foreach ($customParams as $customParam) {
                        if (!is_scalar($customParam) && !is_array($customParam)) {
                            return false;
                        }

                        if (
                            is_array($customParam) &&
                            array_any($customParam, static fn (mixed $innerCustomParam): bool => !is_scalar($innerCustomParam))
                        ) {
                            return false;
                        }
                    }

                    return true;
                },
            )->info('It must be an array of scalar values or arrays of scalar values.');
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $item = null;
        if ($form->getData() !== null) {
            try {
                $backend = $this->backendRegistry->getBackend($options['item_type']);
                $item = $backend->loadItem($form->getData());
            } catch (NotFoundException) {
                // Do nothing
            }
        }

        $view->vars['item'] = $item;
        $view->vars['item_type'] = $options['item_type'];
        $view->vars['start_location'] = $options['start_location'];
        $view->vars['custom_params'] = $options['custom_params'];
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
