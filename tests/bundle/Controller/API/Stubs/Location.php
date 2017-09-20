<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs;

use Netgen\ContentBrowser\Item\LocationInterface;

class Location implements LocationInterface
{
    /**
     * @var mixed
     */
    private $locationId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $parentId;

    /**
     * Constructor.
     *
     * @param mixed $locationId
     * @param string $name
     * @param mixed $parentId
     */
    public function __construct($locationId, $name, $parentId = null)
    {
        $this->locationId = $locationId;
        $this->name = $name;
        $this->parentId = $parentId;
    }

    /**
     * Returns the location ID.
     *
     * @return int|string
     */
    public function getLocationId()
    {
        return $this->locationId;
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
     * Returns the parent ID.
     *
     * @return int|string
     */
    public function getParentId()
    {
        return $this->parentId;
    }
}
