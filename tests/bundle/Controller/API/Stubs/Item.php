<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;

final class Item implements ItemInterface
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $name;

    /**
     * Constructor.
     *
     * @param mixed $value
     * @param string $name
     */
    public function __construct($value, $name)
    {
        $this->value = $value;
        $this->name = $name;
    }

    /**
     * Returns the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns if the item is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return true;
    }

    /**
     * Returns if the item is selectable.
     *
     * @return bool
     */
    public function isSelectable()
    {
        return true;
    }
}
