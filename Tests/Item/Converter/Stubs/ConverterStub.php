<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Item\Converter\Stubs;

use Netgen\Bundle\ContentBrowserBundle\Item\Converter\ConverterInterface;

class ConverterStub implements ConverterInterface
{
    /**
     * Returns the ID of the value object.
     *
     * @param mixed $valueObject
     *
     * @return int|string
     */
    public function getId($valueObject)
    {
        return 24;
    }

    /**
     * Returns the parent ID of the value object.
     *
     * @param mixed $valueObject
     *
     * @return int|string
     */
    public function getParentId($valueObject)
    {
        return 42;
    }

    /**
     * Returns the value of the value object.
     *
     * @param mixed $valueObject
     *
     * @return int|string
     */
    public function getValue($valueObject)
    {
        return 23;
    }

    /**
     * Returns the name of the value object.
     *
     * @param mixed $valueObject
     *
     * @return string
     */
    public function getName($valueObject)
    {
        return 'Some item';
    }

    /**
     * Returns the selectable flag of the value object.
     *
     * @param mixed $valueObject
     *
     * @return bool
     */
    public function getIsSelectable($valueObject)
    {
        return true;
    }

    /**
     * Returns the template variables of the value object.
     *
     * @param mixed $valueObject
     *
     * @return array
     */
    public function getTemplateVariables($valueObject)
    {
        return array('var' => 'value');
    }

    /**
     * Returns the columns of the value object.
     *
     * @param mixed $valueObject
     *
     * @return array
     */
    public function getColumns($valueObject)
    {
        return array(
            'column1' => 'value1',
            'column2' => 'value2',
        );
    }
}
