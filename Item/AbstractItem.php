<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;

abstract class AbstractItem
{
    /**
     * Construct object optionally with a set of properties.
     *
     * Read only properties values must be set using $properties as they are not writable anymore
     * after object has been created.
     *
     * @param array $properties
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If one of the properties does not exist in value object
     */
    public function __construct(array $properties = array())
    {
        foreach ($properties as $property => $value) {
            if (!property_exists($this, $property)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Property "%s" does not exist in "%s" class.',
                        $property,
                        get_class($this)
                    )
                );
            }

            $this->$property = $value;
        }
    }
}
