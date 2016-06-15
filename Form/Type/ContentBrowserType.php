<?php

namespace Netgen\Bundle\ContentBrowserBundle\Form\Type;

use Netgen\Bundle\ContentBrowserBundle\Registry\ValueLoaderRegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentBrowserType extends HiddenType
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Registry\ValueLoaderRegistryInterface
     */
    protected $valueLoaderRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Registry\ValueLoaderRegistryInterface $valueLoaderRegistry
     */
    public function __construct(ValueLoaderRegistryInterface $valueLoaderRegistry)
    {
        $this->valueLoaderRegistry = $valueLoaderRegistry;
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('value_type', 'config_name'));

        $resolver->setAllowedTypes('value_type', 'string');
        $resolver->setAllowedTypes('config_name', 'string');
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
        parent::buildView($view, $form, $options);

        $view->vars = array(
            'value_type' => $options['value_type'],
            'config_name' => $options['config_name'],
            'value_name' => $this->valueLoaderRegistry->getValueLoader($options['value_type'])
                ->loadByValue($form->getData()
            )->getName(),
        ) + $view->vars;
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
        return 'ng_content_browser';
    }
}
