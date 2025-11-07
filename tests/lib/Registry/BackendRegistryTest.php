<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Registry;

use ArrayIterator;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(BackendRegistry::class)]
final class BackendRegistryTest extends TestCase
{
    private MockObject&BackendInterface $backendMock;

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

    public function testGetBackends(): void
    {
        self::assertSame(['value' => $this->backendMock], $this->registry->getBackends());
    }

    public function testGetBackend(): void
    {
        self::assertSame($this->backendMock, $this->registry->getBackend('value'));
    }

    public function testGetBackendThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Backend for "other_value" item type does not exist.');

        $this->registry->getBackend('other_value');
    }

    public function testHasBackend(): void
    {
        self::assertTrue($this->registry->hasBackend('value'));
    }

    public function testHasBackendWithNoBackend(): void
    {
        self::assertFalse($this->registry->hasBackend('other_value'));
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $backends = [];
        foreach ($this->registry as $identifier => $backend) {
            $backends[$identifier] = $backend;
        }

        self::assertSame($this->registry->getBackends(), $backends);
    }

    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('value', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    public function testOffsetGet(): void
    {
        self::assertSame($this->backendMock, $this->registry['value']);
    }

    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['value'] = $this->backendMock;
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['value']);
    }
}
