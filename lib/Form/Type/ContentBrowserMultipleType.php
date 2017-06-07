<?php

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

class ContentBrowserMultipleType extends AbstractType
{
    /**
     * @var \Netgen\ContentBrowser\Registry\BackendRegistryInterface
     */
    protected $backendRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\ContentBrowser\Registry\BackendRegistryInterface $backendRegistry
     */
    public function __construct(BackendRegistryInterface $backendRegistry)
    {
        $this->backendRegistry = $backendRegistry;
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'entry_type' => HiddenType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
            )
        );

        $resolver->setRequired(array('item_type', 'min', 'max', 'start_location'));

        $resolver->setAllowedTypes('item_type', 'string');
        $resolver->setAllowedTypes('min', array('int', 'null'));
        $resolver->setAllowedTypes('max', array('int', 'null'));
        $resolver->setAllowedTypes('start_location', array('int', 'string', 'null'));

        $resolver->setDefault('min', null);
        $resolver->setDefault('max', null);
        $resolver->setDefault('start_location', null);

        $resolver->setNormalizer(
            'max',
            function (Options $options, $value) {
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

    /**
     * Builds the form view.
     *
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $itemNames = array();
        if ($form->getData() !== null) {
            $itemNames = $this->getItemNames($form->getData(), $options['item_type']);
        }

        $view->vars['item_type'] = $options['item_type'];
        $view->vars['item_names'] = $itemNames;

        $view->vars['min'] = $options['min'];
        $view->vars['max'] = $options['max'];
        $view->vars['start_location'] = $options['start_location'];
    }

    /**
     * Returns the name of the parent type.
     */
    public function getParent()
    {
        return CollectionType::class;
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
        return 'ng_content_browser_multiple';
    }

    /**
     * Returns the array of names for all provided item IDs.
     *
     * @param mixed $itemIds
     * @param string $itemType
     *
     * @return array
     */
    protected function getItemNames($itemIds, $itemType)
    {
        $itemNames = array();

        foreach ((array) $itemIds as $itemId) {
            try {
                $backend = $this->backendRegistry->getBackend($itemType);
                $item = $backend->loadItem($itemId);
                $itemNames[$item->getValue()] = $item->getName();
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        return $itemNames;
    }
}
