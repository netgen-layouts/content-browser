<?php

namespace Netgen\Bundle\ContentBrowserBundle\Value;

interface ValueLoaderInterface
{
    /**
     * Loads the value by its ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If value does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface
     */
    public function load($id);

    /**
     * Loads the value by its internal value.
     *
     * @param int|string $value
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If value does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface
     */
    public function loadByValue($value);

    /**
     * Builds the value from provided value object.
     *
     * @param mixed $valueObject
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface
     */
    public function buildValue($valueObject);
}
