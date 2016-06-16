<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ValueLoaderRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ValueLoaderRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ValueLoaderRegistryPass());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ValueLoaderRegistryPass::process
     */
    public function testProcess()
    {
        $this->setDefinition('netgen_content_browser.registry.value_loader', new Definition());

        $valueLoader = new Definition();
        $valueLoader->addTag('netgen_content_browser.value_loader', array('value_type' => 'test'));
        $this->setDefinition('netgen_content_browser.value_loader.test', $valueLoader);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_content_browser.registry.value_loader',
            'addValueLoader',
            array('test', new Reference('netgen_content_browser.value_loader.test'))
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ValueLoaderRegistryPass::process
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagType()
    {
        $this->setDefinition('netgen_content_browser.registry.value_loader', new Definition());

        $valueLoader = new Definition();
        $valueLoader->addTag('netgen_content_browser.value_loader');
        $this->setDefinition('netgen_content_browser.value_loader.test', $valueLoader);

        $this->compile();
    }
}
