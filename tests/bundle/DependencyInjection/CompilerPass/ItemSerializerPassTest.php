<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemSerializerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ItemSerializerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemSerializerPass::process
     */
    public function testProcess()
    {
        $itemSerializer = new Definition();
        $itemSerializer->setArguments(array(null, null, null, null, null));
        $this->setDefinition('netgen_content_browser.item_serializer', $itemSerializer);

        $handler = new Definition();
        $handler->addTag('netgen_content_browser.serializer.handler', array('item_type' => 'test'));
        $this->setDefinition('netgen_content_browser.serializer.handler.test', $handler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_content_browser.item_serializer',
            4,
            array('test' => new Reference('netgen_content_browser.serializer.handler.test'))
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemSerializerPass::process
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagType()
    {
        $itemSerializer = new Definition();
        $itemSerializer->setArguments(array(null, null, null, null, null));
        $this->setDefinition('netgen_content_browser.item_serializer', $itemSerializer);

        $handler = new Definition();
        $handler->addTag('netgen_content_browser.serializer.handler');
        $this->setDefinition('netgen_content_browser.serializer.handler.test', $handler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemSerializerPass::process
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
        $container->addCompilerPass(new ItemSerializerPass());
    }
}
