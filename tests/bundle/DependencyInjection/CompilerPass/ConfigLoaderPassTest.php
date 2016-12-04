<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ConfigLoaderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ConfigLoaderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ConfigLoaderPass::process
     */
    public function testProcess()
    {
        $configLoader = new Definition();
        $configLoader->addArgument(42);
        $configLoader->addArgument(array());

        $this->setDefinition('netgen_content_browser.config_loader', $configLoader);

        $loader1 = new Definition();
        $loader1->addTag('netgen_content_browser.config_processor', array('priority' => 3));
        $this->setDefinition('netgen_content_browser.config_processor.loader1', $loader1);

        $loader2 = new Definition();
        $loader2->addTag('netgen_content_browser.config_processor', array('priority' => -2));
        $this->setDefinition('netgen_content_browser.config_processor.loader2', $loader2);

        $loader3 = new Definition();
        $loader3->addTag('netgen_content_browser.config_processor', array('priority' => 4));
        $this->setDefinition('netgen_content_browser.config_processor.loader3', $loader3);

        $loader4 = new Definition();
        $loader4->addTag('netgen_content_browser.config_processor', array('priority' => 2));
        $this->setDefinition('netgen_content_browser.config_processor.loader4', $loader4);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_content_browser.config_loader',
            0,
            array(
                new Reference('netgen_content_browser.config_processor.loader3'),
                new Reference('netgen_content_browser.config_processor.loader1'),
                new Reference('netgen_content_browser.config_processor.loader4'),
                new Reference('netgen_content_browser.config_processor.loader2'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ConfigLoaderPass::process
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
        $container->addCompilerPass(new ConfigLoaderPass());
    }
}
