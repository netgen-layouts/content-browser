<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Registry;

use ArrayIterator;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Registry\ConfigRegistry;
use PHPUnit\Framework\TestCase;

final class ConfigRegistryTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    private $configuration;

    /**
     * @var \Netgen\ContentBrowser\Registry\ConfigRegistry
     */
    private $registry;

    public function setUp(): void
    {
        $this->configuration = new Configuration('value', 'Value');

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
        $this->assertSame(['value' => $this->configuration], $this->registry->getConfigs());
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::getConfig
     */
    public function testGetConfig(): void
    {
        $this->assertSame($this->configuration, $this->registry->getConfig('value'));
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::getConfig
     * @expectedException \Netgen\ContentBrowser\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Configuration for item type "other_value" does not exist.
     */
    public function testGetConfigThrowsInvalidArgumentException(): void
    {
        $this->registry->getConfig('other_value');
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::hasConfig
     */
    public function testHasConfig(): void
    {
        $this->assertTrue($this->registry->hasConfig('value'));
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::hasConfig
     */
    public function testHasConfigWithNoConfig(): void
    {
        $this->assertFalse($this->registry->hasConfig('other_value'));
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::getIterator
     */
    public function testGetIterator(): void
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $configs = [];
        foreach ($this->registry as $identifier => $config) {
            $configs[$identifier] = $config;
        }

        $this->assertSame($this->registry->getConfigs(), $configs);
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::count
     */
    public function testCount(): void
    {
        $this->assertCount(1, $this->registry);
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::offsetExists
     */
    public function testOffsetExists(): void
    {
        $this->assertArrayHasKey('value', $this->registry);
        $this->assertArrayNotHasKey('other', $this->registry);
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::offsetGet
     */
    public function testOffsetGet(): void
    {
        $this->assertSame($this->configuration, $this->registry['value']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::offsetSet
     * @expectedException \Netgen\ContentBrowser\Exceptions\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetSet(): void
    {
        $this->registry['value'] = $this->configuration;
    }

    /**
     * @covers \Netgen\ContentBrowser\Registry\ConfigRegistry::offsetUnset
     * @expectedException \Netgen\ContentBrowser\Exceptions\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset(): void
    {
        unset($this->registry['value']);
    }
}
