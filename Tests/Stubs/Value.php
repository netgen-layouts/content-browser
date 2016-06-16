<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Stubs;

use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;

class Value implements ValueInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $value;

    /**
     * Constructor.
     *
     * @param int $id
     * @param int $value
     */
    public function __construct($id, $value)
    {
        $this->id = $id;
        $this->value = $value;
    }

    /**
     * Returns the value ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value type.
     *
     * @return int|string
     */
    public function getValueType()
    {
        return 'value';
    }

    /**
     * Returns the item value.
     *
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the item name.
     *
     * @return string
     */
    public function getName()
    {
        return 'This is a name';
    }

    /**
     * Returns the item parent ID.
     *
     * @return int|string
     */
    public function getParentId()
    {
        return 45;
    }

    /**
     * Returns the value object.
     *
     * @return int|string
     */
    public function getValueObject()
    {
    }
}
