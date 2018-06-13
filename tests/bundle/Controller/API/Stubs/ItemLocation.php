<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class ItemLocation implements ItemInterface, LocationInterface
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
     * @var mixed
     */
    private $parentId;

    /**
     * @param mixed $value
     * @param string $name
     * @param mixed $parentId
     */
    public function __construct($value, $name, $parentId = null)
    {
        $this->value = $value;
        $this->name = $name;
        $this->parentId = $parentId;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isVisible()
    {
        return true;
    }

    public function isSelectable()
    {
        return true;
    }

    public function getLocationId()
    {
        return $this->value;
    }

    public function getParentId()
    {
        return $this->parentId;
    }
}
