<?php

namespace Netgen\Bundle\ContentBrowserBundle\Repository;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;

class Location
{
    /**
     * Construct object optionally with a set of properties.
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If one of the properties does not exist in value object
     *
     * @param array $properties
     */
    public function __construct(array $properties = array())
    {
        foreach ($properties as $property => $value) {
            if (!property_exists($this, $property)) {
                throw new InvalidArgumentException(
                    'Property "' . $property . '" does not exist in "' . get_class($this) . '" class.'
                );
            }

            $this->$property = $value;
        }
    }

    /**
     * @var int|string
     */
    public $id;

    /**
     * @var int|string
     */
    public $parentId;

    /**
     * @var array
     */
    public $path;

    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $isEnabled = true;

    /**
     * @var string
     */
    public $thumbnail;

    /**
     * @var string
     */
    public $type;

    /**
     * @var bool
     */
    public $isVisible = true;

    /**
     * @var string
     */
    public $owner;

    /**
     * @var \DateTime
     */
    public $modified;

    /**
     * @var \DateTime
     */
    public $published;

    /**
     * @var int
     */
    public $priority;

    /**
     * @var string
     */
    public $section;
}
