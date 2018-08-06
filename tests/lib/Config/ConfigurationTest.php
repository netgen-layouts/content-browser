<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Config;

use Netgen\ContentBrowser\Config\Configuration;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Config\Configuration
     */
    private $config;

    public function setUp(): void
    {
        $configArray = [
            'min_selected' => 1,
            'max_selected' => 3,
            'tree' => [
                'enabled' => true,
            ],
            'search' => [
                'enabled' => true,
            ],
            'preview' => [
                'enabled' => true,
                'template' => 'template.html.twig',
            ],
            'columns' => ['columns'],
            'default_columns' => ['column1', 'column2'],
        ];

        $parameters = [
            'default' => 'param',
        ];

        $this->config = new Configuration('value', 'Value', $configArray, $parameters);
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::__construct
     * @covers \Netgen\ContentBrowser\Config\Configuration::getItemType
     */
    public function testGetItemType(): void
    {
        $this->assertSame('value', $this->config->getItemType());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getItemName
     */
    public function testGetName(): void
    {
        $this->assertSame('Value', $this->config->getItemName());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMinSelected
     */
    public function testGetMinSelected(): void
    {
        $this->assertSame(1, $this->config->getMinSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMinSelected
     */
    public function testGetMinSelectedWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        $this->assertSame(1, $this->config->getMinSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMaxSelected
     */
    public function testGetMaxSelected(): void
    {
        $this->assertSame(3, $this->config->getMaxSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMaxSelected
     */
    public function testGetMaxSelectedWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        $this->assertSame(0, $this->config->getMaxSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getParameters
     */
    public function testGetParameters(): void
    {
        $this->assertSame(['default' => 'param'], $this->config->getParameters());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasTree
     */
    public function testHasTree(): void
    {
        $this->assertTrue($this->config->hasTree());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasTree
     */
    public function testHasTreeWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        $this->assertFalse($this->config->hasTree());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasSearch
     */
    public function testHasSearch(): void
    {
        $this->assertTrue($this->config->hasSearch());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasSearch
     */
    public function testHasSearchWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        $this->assertFalse($this->config->hasSearch());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasPreview
     */
    public function testHasPreview(): void
    {
        $this->assertTrue($this->config->hasPreview());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasPreview
     */
    public function testHasPreviewWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        $this->assertFalse($this->config->hasPreview());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getTemplate
     */
    public function testGetTemplate(): void
    {
        $this->assertSame('template.html.twig', $this->config->getTemplate());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getTemplate
     */
    public function testGetTemplateWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        $this->assertNull($this->config->getTemplate());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getColumns
     */
    public function testGetColumns(): void
    {
        $this->assertSame(['columns'], $this->config->getColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getColumns
     */
    public function testGetColumnsWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        $this->assertSame([], $this->config->getColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getDefaultColumns
     */
    public function testGetDefaultColumns(): void
    {
        $this->assertSame(['column1', 'column2'], $this->config->getDefaultColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getDefaultColumns
     */
    public function testGetDefaultColumnsWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        $this->assertSame([], $this->config->getDefaultColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::addParameters
     */
    public function testAddParameters(): void
    {
        $this->config->addParameters(['param' => 'value', 'default' => 'override']);
        $this->assertSame('value', $this->config->getParameter('param'));
        $this->assertSame('override', $this->config->getParameter('default'));

        $this->assertTrue($this->config->hasParameter('param'));
        $this->assertTrue($this->config->hasParameter('default'));
        $this->assertFalse($this->config->hasParameter('other'));
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getParameter
     * @covers \Netgen\ContentBrowser\Config\Configuration::getParameters
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasParameter
     * @covers \Netgen\ContentBrowser\Config\Configuration::setParameter
     */
    public function testParameters(): void
    {
        $this->config->setParameter('param', 'value');
        $this->config->setParameter('param2', 'value2');
        $this->assertSame('value', $this->config->getParameter('param'));

        $this->assertTrue($this->config->hasParameter('param'));
        $this->assertFalse($this->config->hasParameter('other'));

        $this->assertSame(
            [
                'default' => 'param',
                'param' => 'value',
                'param2' => 'value2',
            ],
            $this->config->getParameters()
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getParameter
     * @expectedException \Netgen\ContentBrowser\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Parameter "unknown" does not exist in configuration.
     */
    public function testGetParameterThrowsInvalidArgumentException(): void
    {
        $this->config->getParameter('unknown');
    }
}
