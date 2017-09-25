<?php

namespace Netgen\ContentBrowser\Form\Type;

use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Registry\BackendRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBrowserDynamicType extends AbstractType
{
    /**
     * @var \Netgen\ContentBrowser\Registry\BackendRegistryInterface
     */
    private $backendRegistry;

    /**
     * @var array
     */
    private $availableItemTypes;

    /**
     * Constructor.
     *
     * @param \Netgen\ContentBrowser\Registry\BackendRegistryInterface $backendRegistry
     * @param array $availableItemTypes
     */
    public function __construct(BackendRegistryInterface $backendRegistry, array $availableItemTypes)
    {
        $this->backendRegistry = $backendRegistry;
        $this->availableItemTypes = array_flip($availableItemTypes);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array('item_types', 'start_location'));

        $resolver->setAllowedTypes('item_types', array('array'));
        $resolver->setAllowedTypes('start_location', array('int', 'string', 'null'));

        $resolver->setDefault('item_types', array());
        $resolver->setDefault('start_location', null);

        $resolver->setAllowedValues('item_types', function ($values) {
            foreach ($values as $value) {
                if (!in_array($value, $this->availableItemTypes, true)) {
                    return false;
                }
            }

            return true;
        });
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'item_type',
            ChoiceType::class,
            array(
                'choices' => $this->getEnabledItemTypes($options['item_types']),
                'choices_as_values' => true,
                'choice_translation_domain' => 'ngcb',
            )
        );

        $builder->add('item_id', HiddenType::class);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
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

    public function getBlockPrefix()
    {
        return 'ng_content_browser_dynamic';
    }

    /**
     * Returns the enabled item types based on provided list.
     *
     * @param array $itemTypes
     *
     * @return array
     */
    private function getEnabledItemTypes(array $itemTypes)
    {
        if (empty($itemTypes)) {
            return $this->availableItemTypes;
        }

        return array_filter(
            $this->availableItemTypes,
            function ($itemType) use ($itemTypes) {
                return in_array($itemType, $itemTypes, true);
            }
        );
    }
}
