<?php

namespace Netgen\ContentBrowser\Tests\Config;

use Netgen\ContentBrowser\Config\Configuration;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    private $config;

    public function setUp()
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

        $this->config = new Configuration('value', $configArray, $parameters);
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::__construct
     * @covers \Netgen\ContentBrowser\Config\Configuration::getItemType
     */
    public function testGetItemType()
    {
        $this->assertEquals('value', $this->config->getItemType());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMinSelected
     */
    public function testGetMinSelected()
    {
        $this->assertEquals(1, $this->config->getMinSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMinSelected
     */
    public function testGetMinSelectedWithEmptyConfig()
    {
        $this->config = new Configuration('value');
        $this->assertEquals(1, $this->config->getMinSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMaxSelected
     */
    public function testGetMaxSelected()
    {
        $this->assertEquals(3, $this->config->getMaxSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getMaxSelected
     */
    public function testGetMaxSelectedWithEmptyConfig()
    {
        $this->config = new Configuration('value');
        $this->assertEquals(0, $this->config->getMaxSelected());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getParameters
     */
    public function testGetParameters()
    {
        $this->assertEquals(['default' => 'param'], $this->config->getParameters());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasTree
     */
    public function testHasTree()
    {
        $this->assertTrue($this->config->hasTree());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasTree
     */
    public function testHasTreeWithEmptyConfig()
    {
        $this->config = new Configuration('value');
        $this->assertFalse($this->config->hasTree());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasSearch
     */
    public function testHasSearch()
    {
        $this->assertTrue($this->config->hasSearch());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasSearch
     */
    public function testHasSearchWithEmptyConfig()
    {
        $this->config = new Configuration('value');
        $this->assertFalse($this->config->hasSearch());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasPreview
     */
    public function testHasPreview()
    {
        $this->assertTrue($this->config->hasPreview());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::hasPreview
     */
    public function testHasPreviewWithEmptyConfig()
    {
        $this->config = new Configuration('value');
        $this->assertFalse($this->config->hasPreview());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getTemplate
     */
    public function testGetTemplate()
    {
        $this->assertEquals('template.html.twig', $this->config->getTemplate());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getTemplate
     */
    public function testGetTemplateWithEmptyConfig()
    {
        $this->config = new Configuration('value');
        $this->assertNull($this->config->getTemplate());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getColumns
     */
    public function testGetColumns()
    {
        $this->assertEquals(['columns'], $this->config->getColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getColumns
     */
    public function testGetColumnsWithEmptyConfig()
    {
        $this->config = new Configuration('value');
        $this->assertEquals([], $this->config->getColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getDefaultColumns
     */
    public function testGetDefaultColumns()
    {
        $this->assertEquals(['column1', 'column2'], $this->config->getDefaultColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getDefaultColumns
     */
    public function testGetDefaultColumnsWithEmptyConfig()
    {
        $this->config = new Configuration('value');
        $this->assertEquals([], $this->config->getDefaultColumns());
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::addParameters
     */
    public function testAddParameters()
    {
        $this->config->addParameters(['param' => 'value', 'default' => 'override']);
        $this->assertEquals('value', $this->config->getParameter('param'));
        $this->assertEquals('override', $this->config->getParameter('default'));

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
    public function testParameters()
    {
        $this->config->setParameter('param', 'value');
        $this->config->setParameter('param2', 'value2');
        $this->assertEquals('value', $this->config->getParameter('param'));

        $this->assertTrue($this->config->hasParameter('param'));
        $this->assertFalse($this->config->hasParameter('other'));

        $this->assertEquals(
            [
                'param' => 'value',
                'param2' => 'value2',
                'default' => 'param',
            ],
            $this->config->getParameters()
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Config\Configuration::getParameter
     * @expectedException \Netgen\ContentBrowser\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Parameter "unknown" does not exist in configuration.
     */
    public function testGetParameterThrowsInvalidArgumentException()
    {
        $this->config->getParameter('unknown');
    }
}
