<?php

namespace Netgen\ContentBrowser\Tests\Registry;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use PHPUnit\Framework\TestCase;

class BackendRegistryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendMock;

    /**
     * @var \Netgen\ContentBrowser\Registry\BackendRegistry
     */
    protected $registry;

    public function setUp()
    {
        $this->registry = new BackendRegistry();

        $this->backendMock = $this->createMock(BackendInterface::class);
        $this->registry->addBackend('value', $this->backendMock);
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::addBackend
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::getBackends
     */
    public function testGetBackends()
    {
        $this->assertEquals(array('value' => $this->backendMock), $this->registry->getBackends());
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::getBackend
     */
    public function testGetBackend()
    {
        $this->assertEquals($this->backendMock, $this->registry->getBackend('value'));
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::getBackend
     * @expectedException \Netgen\ContentBrowser\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Backend for "other_value" item type does not exist.
     */
    public function testGetBackendThrowsInvalidArgumentException()
    {
        $this->registry->getBackend('other_value');
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::hasBackend
     */
    public function testHasBackend()
    {
        $this->assertTrue($this->registry->hasBackend('value'));
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::hasBackend
     */
    public function testHasBackendWithNoBackend()
    {
        $this->assertFalse($this->registry->hasBackend('other_value'));
    }
}
