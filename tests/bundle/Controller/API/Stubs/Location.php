<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs;

use Netgen\ContentBrowser\Item\LocationInterface;

final class Location implements LocationInterface
{
    /**
     * @var mixed
     */
    private $locationId;

    private string $name;

    /**
     * @var mixed
     */
    private $parentId;

    /**
     * @param mixed $locationId
     * @param mixed $parentId
     */
    public function __construct($locationId, string $name, $parentId = null)
    {
        $this->locationId = $locationId;
        $this->name = $name;
        $this->parentId = $parentId;
    }

    public function getLocationId()
    {
        return $this->locationId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParentId()
    {
        return $this->parentId;
    }
}
