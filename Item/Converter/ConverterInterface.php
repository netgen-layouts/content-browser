<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Converter;

interface ConverterInterface
{
    /**
     * Returns the ID of the value object.
     *
     * @param mixed $valueObject
     *
     * @return int|string
     */
    public function getId($valueObject);

    /**
     * Returns the parent ID of the value object.
     *
     * @param mixed $valueObject
     *
     * @return int|string
     */
    public function getParentId($valueObject);

    /**
     * Returns the value of the value object.
     *
     * @param mixed $valueObject
     *
     * @return int|string
     */
    public function getValue($valueObject);

    /**
     * Returns the name of the value object.
     *
     * @param mixed $valueObject
     *
     * @return string
     */
    public function getName($valueObject);

    /**
     * Returns the selectable flag of the value object.
     *
     * @param mixed $valueObject
     *
     * @return bool
     */
    public function getIsSelectable($valueObject);

    /**
     * Returns the template variables of the value object.
     *
     * @param mixed $valueObject
     *
     * @return array
     */
    public function getTemplateVariables($valueObject);

    /**
     * Returns the columns of the value object.
     *
     * @param mixed $valueObject
     *
     * @return array
     */
    public function getColumns($valueObject);
}
