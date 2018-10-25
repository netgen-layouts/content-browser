<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Registry;

use ArrayIterator;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Netgen\ContentBrowser\Registry\ConfigRegistry;
use PHPUnit\Framework\TestCase;

final class ConfigRegistryTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Config\Configuration
     */
    private $configuration;

    /**
     * @var \Netgen\ContentBrowser\Registry\ConfigRegistry
     */
    private $registry;

    public function setUp(): void
    {
        $this->configuration = new Configuration('value', 'Value', []);

        $this->registry = new ConfigRegistry(
            [
                'value' => $this->configuration,
            ]
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::__construct
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::getConfigs
     */
    public function testGetConfigs(): void
    {
        self::assertSame(['value' => $this->configuration], $this->registry->getConfigs());
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::getConfig
     */
    public function testGetConfig(): void
    {
        self::assertSame($this->configuration, $this->registry->getConfig('value'));
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::getConfig
     */
    public function testGetConfigThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration for item type "other_value" does not exist.');

        $this->registry->getConfig('other_value');
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::hasConfig
     */
    public function testHasConfig(): void
    {
        self::assertTrue($this->registry->hasConfig('value'));
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::hasConfig
     */
    public function testHasConfigWithNoConfig(): void
    {
        self::assertFalse($this->registry->hasConfig('other_value'));
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $configs = [];
        foreach ($this->registry as $identifier => $config) {
            $configs[$identifier] = $config;
        }

        self::assertSame($this->registry->getConfigs(), $configs);
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::count
     */
    public function testCount(): void
    {
        self::assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('value', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame($this->configuration, $this->registry['value']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['value'] = $this->configuration;
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['value']);
    }
}
