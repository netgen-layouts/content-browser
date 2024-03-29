<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Registry;

use ArrayIterator;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BackendRegistryTest extends TestCase
{
    private MockObject $backendMock;

    private BackendRegistry $registry;

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $this->registry = new BackendRegistry(
            [
                'value' => $this->backendMock,
            ],
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::__construct
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::getBackends
     */
    public function testGetBackends(): void
    {
        self::assertSame(['value' => $this->backendMock], $this->registry->getBackends());
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::getBackend
     */
    public function testGetBackend(): void
    {
        self::assertSame($this->backendMock, $this->registry->getBackend('value'));
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::getBackend
     */
    public function testGetBackendThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Backend for "other_value" item type does not exist.');

        $this->registry->getBackend('other_value');
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::hasBackend
     */
    public function testHasBackend(): void
    {
        self::assertTrue($this->registry->hasBackend('value'));
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::hasBackend
     */
    public function testHasBackendWithNoBackend(): void
    {
        self::assertFalse($this->registry->hasBackend('other_value'));
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $backends = [];
        foreach ($this->registry as $identifier => $backend) {
            $backends[$identifier] = $backend;
        }

        self::assertSame($this->registry->getBackends(), $backends);
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('value', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->backendMock, $this->registry['value']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['value'] = $this->backendMock;
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\BackendRegistry::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['value']);
    }
}
