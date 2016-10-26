<?php

namespace Netgen\Bundle\ContentBrowserBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentBrowserMultipleType extends ContentBrowserType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            array(
                'entry_type' => HiddenType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
            )
        );

        $resolver->setRequired(array('min', 'max'));

        $resolver->setAllowedTypes('min', array('int', 'null'));
        $resolver->setAllowedTypes('max', array('int', 'null'));

        $resolver->setDefault('min', null);
        $resolver->setDefault('max', null);

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
        parent::buildView($view, $form, $options);

        $view->vars['min'] = $options['min'];
        $view->vars['max'] = $options['max'];
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
}
