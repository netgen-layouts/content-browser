<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Registry;

use ArrayIterator;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Netgen\ContentBrowser\Registry\ConfigRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigRegistry::class)]
final class ConfigRegistryTest extends TestCase
{
    private Configuration $configuration;

    private ConfigRegistry $registry;

    protected function setUp(): void
    {
        $this->configuration = new Configuration('value', 'Value', []);

        $this->registry = new ConfigRegistry(
            [
                'value' => $this->configuration,
            ],
        );
    }

    public function testGetConfigs(): void
    {
        self::assertSame(['value' => $this->configuration], $this->registry->getConfigs());
    }

    public function testGetConfig(): void
    {
        self::assertSame($this->configuration, $this->registry->getConfig('value'));
    }

    public function testGetConfigThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration for item type "other_value" does not exist.');

        $this->registry->getConfig('other_value');
    }

    public function testHasConfig(): void
    {
        self::assertTrue($this->registry->hasConfig('value'));
    }

    public function testHasConfigWithNoConfig(): void
    {
        self::assertFalse($this->registry->hasConfig('other_value'));
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());
        self::assertSame($this->registry->getConfigs(), [...$this->registry]);
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
        self::assertSame($this->configuration, $this->registry['value']);
    }

    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['value'] = $this->configuration;
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['value']);
    }
}
