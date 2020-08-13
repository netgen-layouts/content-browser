<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ColumnProviderPass;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use stdClass;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ColumnProviderPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new ColumnProviderPass());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ColumnProviderPass::process
     */
    public function testProcess(): void
    {
        $columnProvider = new Definition(stdClass::class);
        $columnProvider->setArguments([null, null]);
        $this->setDefinition('netgen_content_browser.column_provider', $columnProvider);

        $columnValueProvider = new Definition(stdClass::class);
        $columnValueProvider->addTag('netgen_content_browser.column_value_provider', ['identifier' => 'test']);
        $this->setDefinition('netgen_content_browser.column_value_provider.test', $columnValueProvider);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_content_browser.column_provider',
            2,
            new Definition(
                ServiceLocator::class,
                [
                    [
                        'test' => new ServiceClosureArgument(new Reference('netgen_content_browser.column_value_provider.test')),
                    ],
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ColumnProviderPass::process
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagIdentifier(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not register column value provider "netgen_content_browser.column_value_provider.test". Make sure that either "identifier" attribute exists in the tag or a "$defaultIdentifier" static property exists in the class.');

        $columnProvider = new Definition(stdClass::class);
        $columnProvider->setArguments([null, null, null]);
        $this->setDefinition('netgen_content_browser.column_provider', $columnProvider);

        $columnValueProvider = new Definition(stdClass::class);
        $columnValueProvider->addTag('netgen_content_browser.column_value_provider');
        $this->setDefinition('netgen_content_browser.column_value_provider.test', $columnValueProvider);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ColumnProviderPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
