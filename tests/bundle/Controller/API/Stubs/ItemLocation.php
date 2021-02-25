<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class ItemLocation implements ItemInterface, LocationInterface
{
    private int $value;

    private string $name;

    private ?int $parentId;

    public function __construct(int $value, string $name, ?int $parentId = null)
    {
        $this->value = $value;
        $this->name = $name;
        $this->parentId = $parentId;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function isSelectable(): bool
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
