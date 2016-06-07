<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ChainedConfigLoaderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ChainedConfigLoaderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ChainedConfigLoaderPass());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ChainedConfigLoaderPass::process
     */
    public function testProcess()
    {
        $chainedLoader = new Definition();
        $chainedLoader->addArgument(42);
        $chainedLoader->addArgument(array());

        $this->setDefinition('netgen_content_browser.config_loader.chained', $chainedLoader);

        $loader1 = new Definition();
        $loader1->addTag('netgen_content_browser.config_loader', array('priority' => 3));
        $this->setDefinition('netgen_content_browser.config_loader.loader1', $loader1);

        $loader2 = new Definition();
        $loader2->addTag('netgen_content_browser.config_loader', array('priority' => -2));
        $this->setDefinition('netgen_content_browser.config_loader.loader2', $loader2);

        $loader3 = new Definition();
        $loader3->addTag('netgen_content_browser.config_loader', array('priority' => 4));
        $this->setDefinition('netgen_content_browser.config_loader.loader3', $loader3);

        $loader4 = new Definition();
        $loader4->addTag('netgen_content_browser.config_loader', array('priority' => 4));
        $this->setDefinition('netgen_content_browser.config_loader.loader4', $loader4);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_content_browser.config_loader.chained',
            1,
            array(
                new Reference('netgen_content_browser.config_loader.loader3'),
                new Reference('netgen_content_browser.config_loader.loader4'),
                new Reference('netgen_content_browser.config_loader.loader1'),
                new Reference('netgen_content_browser.config_loader.loader2'),
            )
        );
    }
}
