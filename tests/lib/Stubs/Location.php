<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Item\LocationInterface;

final class Location implements LocationInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int|null
     */
    private $parentId;

    /**
     * @param int $id
     * @param int|null $parentId
     */
    public function __construct($id, $parentId = null)
    {
        $this->id = $id;
        $this->parentId = $parentId;
    }

    public function getLocationId()
    {
        return $this->id;
    }

    public function getName()
    {
        return 'This is a name';
    }

    public function getParentId()
    {
        return $this->parentId;
    }
}
