<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ItemBuilderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ItemBuilderPass());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemBuilderPass::process
     */
    public function testProcess()
    {
        $itemBuilder = new Definition();
        $itemBuilder->setArguments(array(null, null, null));
        $this->setDefinition('netgen_content_browser.item_builder', $itemBuilder);

        $converter = new Definition();
        $converter->addTag('netgen_content_browser.converter', array('value_type' => 'test'));
        $this->setDefinition('netgen_content_browser.converter.test', $converter);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_content_browser.item_builder',
            2,
            array('test' => new Reference('netgen_content_browser.converter.test'))
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemBuilderPass::process
     * @expectedException \RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagType()
    {
        $itemBuilder = new Definition();
        $itemBuilder->setArguments(array(null, null, null));
        $this->setDefinition('netgen_content_browser.item_builder', $itemBuilder);

        $converter = new Definition();
        $converter->addTag('netgen_content_browser.converter');
        $this->setDefinition('netgen_content_browser.converter.test', $converter);

        $this->compile();
    }
}
