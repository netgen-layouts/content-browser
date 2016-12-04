<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemRendererPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ItemRendererPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemRendererPass::process
     */
    public function testProcess()
    {
        $itemRenderer = new Definition();
        $itemRenderer->setArguments(array(null, null));
        $this->setDefinition('netgen_content_browser.item_renderer', $itemRenderer);

        $templateValueProvider = new Definition();
        $templateValueProvider->addTag('netgen_content_browser.template_value_provider', array('item_type' => 'test'));
        $this->setDefinition('netgen_content_browser.template_value_provider.test', $templateValueProvider);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_content_browser.item_renderer',
            1,
            array('test' => new Reference('netgen_content_browser.template_value_provider.test'))
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemRendererPass::process
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagType()
    {
        $itemRenderer = new Definition();
        $itemRenderer->setArguments(array(null, null));
        $this->setDefinition('netgen_content_browser.item_renderer', $itemRenderer);

        $templateValueProvider = new Definition();
        $templateValueProvider->addTag('netgen_content_browser.template_value_provider');
        $this->setDefinition('netgen_content_browser.template_value_provider.test', $templateValueProvider);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemRendererPass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertEmpty($this->container->getAliases());
        // The container has at least self ("service_container") as the service
        $this->assertCount(1, $this->container->getServiceIds());
        $this->assertEmpty($this->container->getParameterBag()->all());
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ItemRendererPass());
    }
}