<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ColumnProviderPass;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ColumnProviderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ColumnProviderPass::process
     */
    public function testProcess(): void
    {
        $columnProvider = new Definition();
        $columnProvider->setArguments([null, null]);
        $this->setDefinition('netgen_content_browser.column_provider', $columnProvider);

        $columnValueProvider = new Definition();
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
        $this->expectExceptionMessage('Column value provider definition must have a \'identifier\' attribute in its\' tag.');

        $columnProvider = new Definition();
        $columnProvider->setArguments([null, null, null]);
        $this->setDefinition('netgen_content_browser.column_provider', $columnProvider);

        $columnValueProvider = new Definition();
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

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ColumnProviderPass());
    }
}
