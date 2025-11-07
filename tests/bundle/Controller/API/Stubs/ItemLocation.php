<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class ItemLocation implements ItemInterface, LocationInterface
{
    public function __construct(
        private int $value,
        private string $name,
        private ?int $parentId = null,
    ) {}

    public function getValue(): int
    {
        return $this->value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isVisible(): true
    {
        return true;
    }

    public function isSelectable(): true
    {
        return true;
    }

    public function getLocationId(): int
    {
        return $this->value;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }
}
