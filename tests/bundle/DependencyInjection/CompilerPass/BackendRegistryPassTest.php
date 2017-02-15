<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\BackendRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BackendRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\BackendRegistryPass::process
     */
    public function testProcess()
    {
        $this->setDefinition('netgen_content_browser.registry.backend', new Definition());

        $backend = new Definition();
        $backend->addTag('netgen_content_browser.backend', array('item_type' => 'test'));
        $this->setDefinition('netgen_content_browser.backend.test', $backend);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_content_browser.registry.backend',
            'addBackend',
            array('test', new Reference('netgen_content_browser.backend.test'))
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\BackendRegistryPass::process
     * @expectedException \Netgen\ContentBrowser\Exceptions\RuntimeException
     * @expectedExceptionMessage Backend definition must have a 'item_type' attribute in its' tag.
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagType()
    {
        $this->setDefinition('netgen_content_browser.registry.backend', new Definition());

        $backend = new Definition();
        $backend->addTag('netgen_content_browser.backend');
        $this->setDefinition('netgen_content_browser.backend.test', $backend);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\BackendRegistryPass::process
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
        $container->addCompilerPass(new BackendRegistryPass());
    }
}
