<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class LocationItem implements ItemInterface, LocationInterface
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var mixed
     */
    private $locationId;

    /**
     * @var mixed
     */
    private $parentId;

    public function __construct($value = null, $locationId = null, $parentId = null)
    {
        $this->value = $value;
        $this->locationId = $locationId;
        $this->parentId = $parentId;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getName(): string
    {
        $name = 'This is a name';

        if ($this->value !== null) {
            $name .= ' (' . $this->value . ')';
        }

        return $name;
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function isSelectable(): bool
    {
        return true;
    }

    public function getLocationId()
    {
        return $this->locationId;
    }

    public function getParentId()
    {
        return $this->parentId;
    }
}
