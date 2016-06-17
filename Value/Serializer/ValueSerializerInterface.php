<?php

namespace Netgen\Bundle\ContentBrowserBundle\Value\Serializer;

use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;

interface ValueSerializerInterface
{
    /**
     * Serializes the value to the array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     *
     * @return array
     */
    public function serializeValue(ValueInterface $value);

    /**
     * Serializes the list of values to the array.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface[] $values
     *
     * @return array
     */
    public function serializeValues(array $values);
}
