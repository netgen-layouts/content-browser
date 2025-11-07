<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Config;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Configuration::class)]
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

    public function testGetItemType(): void
    {
        self::assertSame('value', $this->config->getItemType());
    }

    public function testGetName(): void
    {
        self::assertSame('Value', $this->config->getItemName());
    }

    public function testGetMinSelected(): void
    {
        self::assertSame(1, $this->config->getMinSelected());
    }

    public function testGetMinSelectedWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertSame(1, $this->config->getMinSelected());
    }

    public function testGetMaxSelected(): void
    {
        self::assertSame(3, $this->config->getMaxSelected());
    }

    public function testGetMaxSelectedWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertSame(0, $this->config->getMaxSelected());
    }

    public function testGetParameters(): void
    {
        self::assertSame(['default' => 'param'], $this->config->getParameters());
    }

    public function testHasTree(): void
    {
        self::assertTrue($this->config->hasTree());
    }

    public function testHasTreeWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertFalse($this->config->hasTree());
    }

    public function testHasSearch(): void
    {
        self::assertTrue($this->config->hasSearch());
    }

    public function testHasSearchWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertFalse($this->config->hasSearch());
    }

    public function testHasPreview(): void
    {
        self::assertTrue($this->config->hasPreview());
    }

    public function testHasPreviewWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertFalse($this->config->hasPreview());
    }

    public function testGetTemplate(): void
    {
        self::assertSame('template.html.twig', $this->config->getTemplate());
    }

    public function testGetTemplateWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertNull($this->config->getTemplate());
    }

    public function testGetColumns(): void
    {
        self::assertSame(['column' => ['column_value']], $this->config->getColumns());
    }

    public function testGetColumnsWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertSame([], $this->config->getColumns());
    }

    public function testGetDefaultColumns(): void
    {
        self::assertSame(['column1', 'column2'], $this->config->getDefaultColumns());
    }

    public function testGetDefaultColumnsWithEmptyConfig(): void
    {
        $this->config = new Configuration('value', 'Value', []);
        self::assertSame([], $this->config->getDefaultColumns());
    }

    public function testAddParameters(): void
    {
        $this->config->addParameters(['param' => 'value', 'default' => 'override']);
        self::assertSame('value', $this->config->getParameter('param'));
        self::assertSame('override', $this->config->getParameter('default'));

        self::assertTrue($this->config->hasParameter('param'));
        self::assertTrue($this->config->hasParameter('default'));
        self::assertFalse($this->config->hasParameter('other'));
    }

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

    public function testGetParameterThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter "unknown" does not exist in configuration.');

        $this->config->getParameter('unknown');
    }
}
