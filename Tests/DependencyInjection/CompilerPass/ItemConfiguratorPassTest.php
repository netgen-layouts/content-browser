<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemConfiguratorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ItemConfiguratorPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ItemConfiguratorPass());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemConfiguratorPass::process
     */
    public function testProcess()
    {
        $itemConfigurator = new Definition();
        $itemConfigurator->setArguments(array(null, null));
        $this->setDefinition('netgen_content_browser.item_configurator', $itemConfigurator);

        $handler = new Definition();
        $handler->addTag('netgen_content_browser.item_configurator.handler', array('value_type' => 'test'));
        $this->setDefinition('netgen_content_browser.item_configurator.handler.test', $handler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_content_browser.item_configurator',
            2,
            array('test' => new Reference('netgen_content_browser.item_configurator.handler.test'))
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemConfiguratorPass::process
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagType()
    {
        $itemConfigurator = new Definition();
        $itemConfigurator->setArguments(array(null, null));
        $this->setDefinition('netgen_content_browser.item_configurator', $itemConfigurator);

        $handler = new Definition();
        $handler->addTag('netgen_content_browser.item_configurator.handler');
        $this->setDefinition('netgen_content_browser.item_configurator.handler.test', $handler);

        $this->compile();
    }
}
