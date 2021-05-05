<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Config;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    private Configuration $config;

    protected function setUp(): void
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
            'columns' => ['column' => ['column_value']],
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
        self::assertSame('value', $this->config->getItemType());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getItemName
     */
    public function testGetName(): void
    {
        self::assertSame('Value', $this->config->getItemName());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMinSelected
     */
    public function testGetMinSelected(): void
    {
        self::assertSame(1, $this->config->getMinSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMinSelected
     */
    public function testGetMinSelectedWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertSame(1, $this->config->getMinSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMaxSelected
     */
    public function testGetMaxSelected(): void
    {
        self::assertSame(3, $this->config->getMaxSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMaxSelected
     */
    public function testGetMaxSelectedWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertSame(0, $this->config->getMaxSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getParameters
     */
    public function testGetParameters(): void
    {
        self::assertSame(['default' => 'param'], $this->config->getParameters());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasTree
     */
    public function testHasTree(): void
    {
        self::assertTrue($this->config->hasTree());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasTree
     */
    public function testHasTreeWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertFalse($this->config->hasTree());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasSearch
     */
    public function testHasSearch(): void
    {
        self::assertTrue($this->config->hasSearch());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasSearch
     */
    public function testHasSearchWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertFalse($this->config->hasSearch());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasPreview
     */
    public function testHasPreview(): void
    {
        self::assertTrue($this->config->hasPreview());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasPreview
     */
    public function testHasPreviewWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertFalse($this->config->hasPreview());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getTemplate
     */
    public function testGetTemplate(): void
    {
        self::assertSame('template.html.twig', $this->config->getTemplate());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getTemplate
     */
    public function testGetTemplateWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertNull($this->config->getTemplate());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getColumns
     */
    public function testGetColumns(): void
    {
        self::assertSame(['column' => ['column_value']], $this->config->getColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getColumns
     */
    public function testGetColumnsWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertSame([], $this->config->getColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getDefaultColumns
     */
    public function testGetDefaultColumns(): void
    {
        self::assertSame(['column1', 'column2'], $this->config->getDefaultColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getDefaultColumns
     */
    public function testGetDefaultColumnsWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertSame([], $this->config->getDefaultColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::addParameters
     */
    public function testAddParameters(): void
    {
        $this->config->addParameters(['param' => 'value', 'default' => 'override']);
        self::assertSame('value', $this->config->getParameter('param'));
        self::assertSame('override', $this->config->getParameter('default'));

        self::assertTrue($this->config->hasParameter('param'));
        self::assertTrue($this->config->hasParameter('default'));
        self::assertFalse($this->config->hasParameter('other'));
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
        self::assertSame('value', $this->config->getParameter('param'));

        self::assertTrue($this->config->hasParameter('param'));
        self::assertFalse($this->config->hasParameter('other'));

        self::assertSame(
            [
                'default' => 'param',
                'param' => 'value',
                'param2' => 'value2',
            ],
            $this->config->getParameters(),
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getParameter
     */
    public function testGetParameterThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter "unknown" does not exist in configuration.');

        $this->config->getParameter('unknown');
    }
}
