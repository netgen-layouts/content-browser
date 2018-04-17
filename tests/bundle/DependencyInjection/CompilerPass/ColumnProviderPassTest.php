<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ColumnProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ColumnProviderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ColumnProviderPass::process
     */
    public function testProcess()
    {
        $columnProvider = new Definition();
        $columnProvider->setArguments([null, null, null]);
        $this->setDefinition('netgen_content_browser.column_provider', $columnProvider);

        $columnValueProvider = new Definition();
        $columnValueProvider->addTag('netgen_content_browser.column_value_provider', ['identifier' => 'test']);
        $this->setDefinition('netgen_content_browser.column_value_provider.test', $columnValueProvider);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_content_browser.column_provider',
            2,
            ['test' => new Reference('netgen_content_browser.column_value_provider.test')]
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ColumnProviderPass::process
     * @expectedException \Netgen\ContentBrowser\Exceptions\RuntimeException
     * @expectedExceptionMessage Column value provider definition must have a 'identifier' attribute in its' tag.
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagIdentifier()
    {
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
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ColumnProviderPass());
    }
}
