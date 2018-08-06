<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Form\Type;

use Netgen\ContentBrowser\Config\ConfigurationInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Registry\BackendRegistryInterface;
use Netgen\ContentBrowser\Registry\ConfigRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBrowserDynamicType extends AbstractType
{
    use ChoicesAsValuesTrait;

    /**
     * @var \Netgen\ContentBrowser\Registry\BackendRegistryInterface
     */
    private $backendRegistry;

    /**
     * @var \Netgen\ContentBrowser\Registry\ConfigRegistryInterface
     */
    private $configRegistry;

    public function __construct(
        BackendRegistryInterface $backendRegistry,
        ConfigRegistryInterface $configRegistry
    ) {
        $this->backendRegistry = $backendRegistry;
        $this->configRegistry = $configRegistry;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['item_types', 'start_location', 'custom_params']);

        $resolver->setAllowedTypes('item_types', 'array');
        $resolver->setAllowedTypes('start_location', ['int', 'string', 'null']);
        $resolver->setAllowedTypes('custom_params', 'array');

        // @deprecated Replace with "string[]" allowed type when support for Symfony 2.8 ends
        $resolver->setAllowedValues(
            'item_types',
            function (array $itemTypes): bool {
                foreach ($itemTypes as $itemType) {
                    if (!is_string($itemType)) {
                        return false;
                    }
                }

                return true;
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

        $resolver->setDefault('item_types', []);
        $resolver->setDefault('start_location', null);

        $resolver->setNormalizer(
            'item_types',
            function (Options $options, array $itemTypes): array {
                $validItemTypes = [];

                foreach ($itemTypes as $itemType) {
                    if ($this->backendRegistry->hasBackend($itemType)) {
                        $validItemTypes[] = $itemType;
                    }
                }

                return $validItemTypes;
            }
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'item_type',
            ChoiceType::class,
            [
                'choices' => $this->getEnabledItemTypes($options['item_types']),
                'choice_translation_domain' => 'ngcb',
            ] + $this->getChoicesAsValuesOption()
        );

        $builder->add('item_value', HiddenType::class);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $item = null;
        $itemValue = $form->get('item_value')->getData();
        $itemType = $form->get('item_type')->getData();

        if (!empty($itemValue) && !empty($itemType)) {
            try {
                $backend = $this->backendRegistry->getBackend($itemType);
                $item = $backend->loadItem($itemValue);
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        $view->vars['item'] = $item;
        $view->vars['start_location'] = $options['start_location'];
        $view->vars['custom_params'] = $options['custom_params'];
    }

    public function getBlockPrefix(): string
    {
        return 'ngcb_dynamic';
    }

    /**
     * Returns the enabled item types based on provided list.
     */
    private function getEnabledItemTypes(array $itemTypes): array
    {
        $allItemTypes = array_flip(
            array_map(
                function (ConfigurationInterface $config): string {
                    return $config->getItemName();
                },
                $this->configRegistry->getConfigs()
            )
        );

        if (empty($itemTypes)) {
            return $allItemTypes;
        }

        return array_filter(
            $allItemTypes,
            function (string $itemType) use ($itemTypes): bool {
                return in_array($itemType, $itemTypes, true);
            }
        );
    }
}
