<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Registry;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistry;
use PHPUnit\Framework\TestCase;

class BackendRegistryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new BackendRegistry();

        $this->backendMock = $this->createMock(BackendInterface::class);
        $this->registry->addBackend('value', $this->backendMock);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistry::addBackend
     * @covers \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistry::getBackends
     */
    public function testGetBackends()
    {
        $this->assertEquals(array('value' => $this->backendMock), $this->registry->getBackends());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistry::getBackend
     */
    public function testGetBackend()
    {
        $this->assertEquals($this->backendMock, $this->registry->getBackend('value'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistry::getBackend
     * @expectedException \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException
     */
    public function testGetBackendThrowsInvalidArgumentException()
    {
        $this->registry->getBackend('other_value');
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistry::hasBackend
     */
    public function testHasBackend()
    {
        $this->assertTrue($this->registry->hasBackend('value'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistry::hasBackend
     */
    public function testHasBackendWithNoBackend()
    {
        $this->assertFalse($this->registry->hasBackend('other_value'));
    }
}
