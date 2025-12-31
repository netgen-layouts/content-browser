<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Form\Type;

use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_any;
use function is_array;
use function is_scalar;

abstract class AbstractContentBrowserMultipleType extends AbstractType
{
    final public function __construct(
        private BackendRegistry $backendRegistry,
    ) {}

    final public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'entry_type' => $this->getEntryType(),
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
            ],
        );

        $resolver
            ->define('item_type')
            ->required()
            ->allowedTypes('string')
            ->allowedValues(
                fn (string $itemType): bool => $this->backendRegistry->hasBackend($itemType),
            )->info('It must be a valid item type.');

        $resolver
            ->define('min')
            ->required()
            ->default(null)
            ->allowedTypes('int', 'null');

        $resolver
            ->define('max')
            ->required()
            ->default(null)
            ->allowedTypes('int', 'null')
            ->normalize(
                static fn (Options $options, ?int $value): ?int => match (true) {
                    $value === null || $options['min'] === null => $value,
                    $value < $options['min'] => $options['min'],
                    default => $value,
                },
            );

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

    final public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['items'] = [];
        $view->vars['item_type'] = $options['item_type'];

        if ($form->getData() !== null) {
            $view->vars['items'] = $this->getItems((array) $form->getData(), $options['item_type']);
        }

        $view->vars['min'] = $options['min'];
        $view->vars['max'] = $options['max'];
        $view->vars['start_location'] = $options['start_location'];
        $view->vars['custom_params'] = $options['custom_params'];
    }

    final public function getParent(): string
    {
        return CollectionType::class;
    }

    final public function getBlockPrefix(): string
    {
        return 'ngcb_multiple';
    }

    /**
     * @return class-string<\Symfony\Component\Form\FormTypeInterface>
     */
    abstract protected function getEntryType(): string;

    /**
     * Returns the array of items for all provided item values.
     *
     * @param array<int|string> $itemValues
     *
     * @return \Netgen\ContentBrowser\Item\ItemInterface[]
     */
    private function getItems(array $itemValues, string $itemType): array
    {
        $items = [];

        foreach ($itemValues as $itemValue) {
            try {
                $backend = $this->backendRegistry->getBackend($itemType);
                $item = $backend->loadItem($itemValue);
                $items[$item->value] = $item;
            } catch (NotFoundException) {
                // Do nothing
            }
        }

        return $items;
    }
}
