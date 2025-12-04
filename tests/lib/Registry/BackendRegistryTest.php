<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Registry;

use ArrayIterator;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(BackendRegistry::class)]
final class BackendRegistryTest extends TestCase
{
    private Stub&BackendInterface $backendStub;

    private BackendRegistry $registry;

    protected function setUp(): void
    {
        $this->backendStub = self::createStub(BackendInterface::class);

        $this->registry = new BackendRegistry(
            [
                'value' => $this->backendStub,
            ],
        );
    }

    public function testGetBackends(): void
    {
        self::assertSame(['value' => $this->backendStub], $this->registry->getBackends());
    }

    public function testGetBackend(): void
    {
        self::assertSame($this->backendStub, $this->registry->getBackend('value'));
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
        self::assertSame($this->registry->getBackends(), [...$this->registry]);
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
        self::assertSame($this->backendStub, $this->registry['value']);
    }

    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['value'] = $this->backendStub;
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['value']);
    }
}
