<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Form\Type;

use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Registry\BackendRegistryInterface;
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
     * @var array
     */
    private $availableItemTypes;

    public function __construct(BackendRegistryInterface $backendRegistry, array $availableItemTypes)
    {
        $this->backendRegistry = $backendRegistry;
        $this->availableItemTypes = array_flip($availableItemTypes);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['item_types', 'start_location']);

        $resolver->setAllowedTypes('item_types', ['array']);
        $resolver->setAllowedTypes('start_location', ['int', 'string', 'null']);

        $resolver->setDefault('item_types', []);
        $resolver->setDefault('start_location', null);

        $resolver->setNormalizer(
            'item_types',
            function (Options $options, array $values): array {
                $validValues = [];

                foreach ($values as $value) {
                    if (in_array($value, $this->availableItemTypes, true)) {
                        $validValues[] = $value;
                    }
                }

                return $validValues;
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

        $builder->add('item_id', HiddenType::class);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $itemName = null;
        $itemId = $form->get('item_id')->getData();
        $itemType = $form->get('item_type')->getData();

        if (!empty($itemId) && !empty($itemType)) {
            try {
                $backend = $this->backendRegistry->getBackend($itemType);
                $item = $backend->loadItem($itemId);
                $itemName = $item->getName();
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        $view->vars['item_name'] = $itemName;
        $view->vars['start_location'] = $options['start_location'];
    }

    public function getBlockPrefix(): string
    {
        return 'ng_content_browser_dynamic';
    }

    /**
     * Returns the enabled item types based on provided list.
     */
    private function getEnabledItemTypes(array $itemTypes): array
    {
        if (empty($itemTypes)) {
            return $this->availableItemTypes;
        }

        return array_filter(
            $this->availableItemTypes,
            function (string $itemType) use ($itemTypes): bool {
                return in_array($itemType, $itemTypes, true);
            }
        );
    }
}
