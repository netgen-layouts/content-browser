<?php

namespace Netgen\Bundle\ContentBrowserBundle\Value;

interface ValueInterface
{
    /**
     * Returns the value ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the value type.
     *
     * @return int|string
     */
    public function getValueType();

    /**
     * Returns the item value.
     *
     * @return int|string
     */
    public function getValue();

    /**
     * Returns the item name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the item parent ID.
     *
     * @return int|string
     */
    public function getParentId();

    /**
     * Returns the value object.
     *
     * @return mixed
     */
    public function getValueObject();
}
