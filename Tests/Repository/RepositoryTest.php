<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Repository;

use Netgen\Bundle\ContentBrowserBundle\Repository\Repository;
use Netgen\Bundle\ContentBrowserBundle\Repository\AdapterInterface;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Repository\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapterMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Repository\Repository
     */
    protected $repository;

    public function setUp()
    {
        $this->adapterMock = $this->getMockBuilder(AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = new Repository($this->adapterMock);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Repository\Repository::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Repository\Repository::getConfig
     * @covers \Netgen\Bundle\ContentBrowserBundle\Repository\Repository::setConfig
     */
    public function testGetGetConfig()
    {
        $config = array('config');
        $this->repository->setConfig($config);
        self::assertEquals($config, $this->repository->getConfig());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Repository\Repository::getRootLocations
     */
    public function testGetRootLocations()
    {
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Repository\Repository::getLocation
     */
    public function testGetLocation()
    {
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Repository\Repository::getChildren
     */
    public function testGetChildren()
    {
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Repository\Repository::isRootLocation
     */
    public function testIsRootLocation()
    {
    }
}
