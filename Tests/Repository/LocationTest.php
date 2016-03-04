<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Repository;

use Netgen\Bundle\ContentBrowserBundle\Repository\Location;

class LocationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Repository\Location::__construct
     */
    public function testSetProperties()
    {
        $location = new Location(
            array(
                'id' => 42,
                'parentId' => 84,
            )
        );

        self::assertEquals(42, $location->id);
        self::assertEquals(84, $location->parentId);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Repository\Location::__construct
     * @expectedException \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException
     */
    public function testSetNonExistingProperties()
    {
        $location = new Location(
            array(
                'someNonExistingProperty' => 42,
            )
        );
    }
}
