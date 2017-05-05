<?php

namespace Netgen\ContentBrowser\Form\Type;

use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Item\ItemRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentBrowserDynamicType extends AbstractType
{
    /**
     * @var \Netgen\ContentBrowser\Item\ItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var array
     */
    protected $availableItemTypes;

    /**
     * Constructor.
     *
     * @param \Netgen\ContentBrowser\Item\ItemRepositoryInterface $itemRepository
     * @param array $availableItemTypes
     */
    public function __construct(ItemRepositoryInterface $itemRepository, array $availableItemTypes)
    {
        $this->itemRepository = $itemRepository;
        $this->availableItemTypes = array_flip($availableItemTypes);
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('item_types'));
        $resolver->setAllowedTypes('item_types', array('array'));
        $resolver->setDefault('item_types', array());

        $resolver->setAllowedValues('item_types', function ($values) {
            foreach ($values as $value) {
                if (!in_array($value, $this->availableItemTypes, true)) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'item_type',
            ChoiceType::class,
            array(
                'choices' => $this->getEnabledItemTypes($options['item_types']),
                'choices_as_values' => true,
                'choice_translation_domain' => 'ngcb',
                'label' => false,
                'attr' => array(
                    'class' => 'js-config-name',
                ),
            )
        );

        $builder->add(
            'item_id',
            HiddenType::class,
            array(
                'attr' => array(
                    'class' => 'js-value',
                ),
            )
        );
    }

    /**
     * Builds the form view.
     *
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $itemName = null;
        $itemId = $form->get('item_id')->getData();
        $itemType = $form->get('item_type')->getData();

        if (!empty($itemId) && !empty($itemType)) {
            try {
                $item = $this->itemRepository->loadItem($itemId, $itemType);

                $itemName = $item->getName();
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        $view->vars['item_name'] = $itemName;
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefixes default to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
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
    protected function getEnabledItemTypes(array $itemTypes)
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
